<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\User;
use App\Models\ChatHistory;
use App\Models\UserAnalytics;
use App\Models\Classroom;
use App\Traits\LogsActivity;
use Auth;
use Session;
use flash;
use Validator;
use DB;
use File;
use Carbon\Carbon;

class UsersController extends Controller
{
    use LogsActivity;

    function __construct() {
    }

    /**
     * Show list of records for users.
     */
    public function index() {
        $data = [];
        $data['pageTitle'] = "Users";
        $data['current_module_name'] = "Users";
        $data['module_name'] = "Users";
        $data['module_url'] = route('adminUsers');
        $data['recordsTotal'] = 0;
        $data['currentModule'] = '';
        
        // Stats by user type
        $data['totalUsers'] = User::where('is_deleted', '!=', 1)->count();
        $data['totalStudents'] = User::where('is_deleted', '!=', 1)->where('user_type', 'student')->count();
        $data['totalParents'] = User::where('is_deleted', '!=', 1)->where('user_type', 'parent')->count();
        $data['totalTeachers'] = User::where('is_deleted', '!=', 1)->where('user_type', 'teacher')->count();

        return view('Admin/Users/index', $data);
    }

    /**
     * Get records for DataTables
     */
    public function getRecords(Request $request) {
        $usersDetails = User::select('users.*')->where('users.is_deleted', '!=', 1);
        
        // Filter by user type
        if (!empty($request->input('user_type'))) {
            $usersDetails = $usersDetails->where('users.user_type', $request->input('user_type'));
        }

        // Filter by status
        if (!empty($request->input('status'))) {
            $usersDetails = $usersDetails->where('users.status', $request->input('status'));
        }

        // Filter by country
        if (!empty($request->input('country'))) {
            $usersDetails = $usersDetails->where('users.country', 'like', '%' . $request->input('country') . '%');
        }

        // Filter by date range
        if (!empty($request->input('date_from'))) {
            $usersDetails = $usersDetails->whereDate('users.created_at', '>=', $request->input('date_from'));
        }

        if (!empty($request->input('date_to'))) {
            $usersDetails = $usersDetails->whereDate('users.created_at', '<=', $request->input('date_to'));
        }
        
        // Search
        if (!empty($request['search']['value'])) {
            $field = ['users.first_name', 'users.last_name', 'users.email', 'users.country', 'users.school_name'];
            $namefield = ['users.first_name', 'users.last_name', 'users.email', 'users.country'];
            $search = $request['search']['value'];

            $usersDetails = $usersDetails->where(function ($query) use ($search, $field, $namefield) {
                if (strpos($search, ' ') !== false) {
                    $s = explode(' ', $search);
                    foreach ($s as $val) {
                        for ($i = 0; $i < count($namefield); $i++) {
                            $query->orwhere($namefield[$i], 'like', '%' . $val . '%');
                        }
                    }
                } else {
                    for ($i = 0; $i < count($field); $i++) {
                        $query->orwhere($field[$i], 'like', '%' . $search . '%');
                    }
                }
            });
        }

        // Ordering
        if (isset($request['order'][0])) {
            $postedorder = $request['order'][0];
            switch ($postedorder['column']) {
                case 2: $orderby = 'users.first_name'; break;
                case 3: $orderby = 'users.email'; break;
                case 4: $orderby = 'users.user_type'; break;
                case 5: $orderby = 'users.country'; break;
                default: $orderby = 'users.id';
            }
            $orderorder = $postedorder['dir'] ?? 'desc';
            $usersDetails = $usersDetails->orderBy($orderby, $orderorder);
        } else {
            $usersDetails = $usersDetails->orderBy('users.id', 'desc');
        }

        $recordsTotal = $usersDetails->count();
        $recordDetails = $usersDetails->offset($request->input('start'))->limit($request->input('length'))->get();

        $arr = [];
        if (count($recordDetails) > 0) {
            $recordDetails = $recordDetails->toArray();
            $i = 0;
            foreach ($recordDetails as $recordDetailsKey => $recordDetailsVal) {
                $action = $status = $image = '-';

                $id = (!empty($recordDetailsVal['id'])) ? $recordDetailsVal['id'] : '-';
                if (!empty($recordDetailsVal['image'])) {
                    $image = asset('assets/' . $recordDetailsVal['image']);
                } else {
                    $image = asset('assets/profile_images/default/no-image.png');
                }

                $image = '<img src="' . $image . '" width="50" height="50" class="rounded-circle">';

                $fullname = (!empty($recordDetailsVal['first_name'])) ? $recordDetailsVal['first_name'] . " " . @$recordDetailsVal['last_name'] : '-';

                $email = (!empty($recordDetailsVal['email'])) ? $recordDetailsVal['email'] : '-';
                $country = (!empty($recordDetailsVal['country'])) ? $recordDetailsVal['country'] : '-';

                // User type badge
                $userType = $recordDetailsVal['user_type'] ?? 'student';
                $typeBadge = match($userType) {
                    'student' => '<span class="badge badge-primary">Student</span>',
                    'parent' => '<span class="badge badge-success">Parent</span>',
                    'teacher' => '<span class="badge badge-info">Teacher</span>',
                    default => '<span class="badge badge-secondary">-</span>',
                };

                if ($recordDetailsVal['status'] == 'active') {
                    $status = '<a href="javascript:void(0)" onclick=" return ConfirmStatusFunction(\'' . route('adminUserChangeStatus', [base64_encode($recordDetailsVal['id']), 'blocked']) . '\');" class="btn btn-icon btn-success" title="Block"><i class="fa fa-unlock"></i> </a>';
                } else {
                    $status = '<a href="javascript:void(0)" onclick=" return ConfirmStatusFunction(\'' . route('adminUserChangeStatus', [base64_encode($recordDetailsVal['id']), 'active']) . '\');" class="btn btn-icon btn-danger" title="Active"><i class="fa fa-lock"></i> </a>';
                }

                $action = '<a href="' . route('admin.users.view', base64_encode($id)) . '" title="View" class="btn btn-icon btn-info"><i class="fas fa-eye"></i></a> ';
                $action .= '<a href="' . route('adminUserEdit', base64_encode($id)) . '" title="Edit" class="btn btn-icon btn-warning"><i class="fas fa-edit"></i></a> ';
                $action .= '<a href="javascript:void(0)" onclick=" return ConfirmDeleteFunction(\'' . route('adminUserDelete', base64_encode($id)) . '\');"  title="Delete" class="btn btn-icon btn-danger"><i class="fas fa-trash"></i></a>';

                $i++;

                // Get chat count
                $chatCount = ChatHistory::where('user_id', $id)->where('is_deleted', 0)->count();

                $checkbox = '<input type="checkbox" class="user-checkbox" value="' . base64_encode($id) . '">';

                $arr[] = [$checkbox, $image, $fullname, $email, $typeBadge, $country, $chatCount, $status, $action];
            }
        } else {
            $arr[] = ['', '', '', 'No record found', '', '', '', '', ''];
        }

        $json_arr = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => ($arr),
        ];

        return json_encode($json_arr);
    }

    /**
     * Create/Edit user form
     */
    public function create(Request $request) {
        $id = $request->id;
        $data['module_url'] = route('adminUsers');
        $data['userTypes'] = User::TYPES;
        $data['availableParents'] = User::where('is_deleted', '!=', 1)->where('user_type', 'parent')->get();
        $data['availableTeachers'] = User::where('is_deleted', '!=', 1)->where('user_type', 'teacher')->get();
       
        if ($id != '') {
            $data['id'] = $id;
            $id = base64_decode($id);
            $data['userDetails'] = User::where('id', $id)->first();
            $user_label = "Edit User";
            $data['pageTitle'] = $user_label;
            $data['current_module_name'] = $user_label;
            $data['module_name'] = $user_label;


            $data["availableParents"] = User::where("is_deleted", "!=", 1)->where("user_type", "parent")->get();
            $data["availableTeachers"] = User::where("is_deleted", "!=", 1)->where("user_type", "teacher")->get();
            return view("Admin/Users/edit", $data);
        } else {
            $user_label = "Add User";
            $data['pageTitle'] = $user_label;
            $data['current_module_name'] = $user_label;
            $data['module_name'] = $user_label;
            return view("Admin/Users/create", $data);
        }
    }

    /**
     * Store/Update user
     */
    public function store(Request $request) {
        $id = trim(base64_decode($request->input('id')));
        $role_id = $request->input('role_id');

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'user_type' => 'nullable|in:student,parent,teacher',
            'country' => 'nullable|string|max:255',
            'birth_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'school_name' => 'nullable|string|max:255',
            'grade_level' => 'nullable|string|max:50',
        ];

        if ($request->input('id') != '') {
            $rules['email'] = 'required|regex:/(.*)\.([a-zA-z]+)/i|unique:users,email,' . $id;
        } else {
            $rules['email'] = "required|email|unique:users,email,NULL,id,is_deleted,0";
        }

        $messages = [
            'first_name.required' => "Please enter first name",
            'email.unique' => "Email already exists",
            'email.email' => "Please enter a valid email",
            'email.required' => "Please enter email",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return redirect()->back()->withInput($request->all())->withErrors($messages);
        }

        $created_at = date('Y-m-d H:i:s');

        $fileName = '';
        $buyerDetails = User::find($id);

        if ($request->hasfile('profile')) {
            $image = $request->file('profile');

            if (!empty($buyerDetails->profile)) {
                $image_path = storage_path() . '/app/public/uploads/User/' . $buyerDetails->profile;
                $resized_image_path = storage_path() . '/app/public/uploads/User/' . $buyerDetails->profile;

                if (File::exists($image_path)) {
                    unlink($image_path);
                }

                if (File::exists($resized_image_path)) {
                    unlink($resized_image_path);
                }
            }

            $fileError = 0;
            $image = $request->file('profile');
            $name = $image->getClientOriginalName();
            $fileExt = strtolower($image->getClientOriginalExtension());
            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                $fileName = 'Buyer_' . date('YmdHis') . '.' . $fileExt;

                Storage::disk('public')->put('uploads/User/' . $fileName, File::get($image));

                $path = storage_path() . '/app/public/uploads/User/' . $fileName;
                $mime = getimagesize($path);

                if ($mime['mime'] == 'image/png') { $src_img = imagecreatefrompng($path); }
                if ($mime['mime'] == 'image/jpg') { $src_img = imagecreatefromjpeg($path); }
                if ($mime['mime'] == 'image/jpeg') { $src_img = imagecreatefromjpeg($path); }
                if ($mime['mime'] == 'image/pjpeg') { $src_img = imagecreatefromjpeg($path); }

                if ($mime['mime'] == 'image/webp') {
                    $image = Image::make($path);
                    $src_img = $image->getCore();
                }

                $old_x = imageSX($src_img);
                $old_y = imageSY($src_img);

                $newWidth = 300;
                $newHeight = 300;

                if ($old_x > $old_y) {
                    $thumb_w = $newWidth;
                    $thumb_h = $old_y / $old_x * $newWidth;
                }

                if ($old_x < $old_y) {
                    $thumb_w = $old_x / $old_y * $newHeight;
                    $thumb_h = $newHeight;
                }

                if ($old_x == $old_y) {
                    $thumb_w = $newWidth;
                    $thumb_h = $newHeight;
                }

                $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
                imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

                // New save location
                $new_thumb_loc = storage_path() . '/app/public/uploads/User/resized/' . $fileName;
                if ($mime['mime'] == 'image/png') { $result = imagepng($dst_img, $new_thumb_loc, 8); }
                if ($mime['mime'] == 'image/jpg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80); }
                if ($mime['mime'] == 'image/jpeg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80); }
                if ($mime['mime'] == 'image/pjpeg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80); }

                if ($mime['mime'] == 'image/webp') {
                    $result = imagejpeg($dst_img, $new_thumb_loc, 80);
                }

                imagedestroy($dst_img);
                imagedestroy($src_img);

                /*resized for product details page small image*/
                $mime = getimagesize($path);

                if ($mime['mime'] == 'image/png') { $src_img = imagecreatefrompng($path); }
                if ($mime['mime'] == 'image/jpg') { $src_img = imagecreatefromjpeg($path); }
                if ($mime['mime'] == 'image/jpeg') { $src_img = imagecreatefromjpeg($path); }
                if ($mime['mime'] == 'image/pjpeg') { $src_img = imagecreatefromjpeg($path); }

                if ($mime['mime'] == 'image/webp') {
                    $image = Image::make($path);
                    $src_img = $image->getCore();
                }

                $old_x = imageSX($src_img);
                $old_y = imageSY($src_img);

                $newWidth = 70;
                $newHeight = 70;

                if ($old_x > $old_y) {
                    $thumb_w = $newWidth;
                    $thumb_h = $old_y / $old_x * $newWidth;
                }

                if ($old_x < $old_y) {
                    $thumb_w = $old_x / $old_y * $newHeight;
                    $thumb_h = $newHeight;
                }

                if ($old_x == $old_y) {
                    $thumb_w = $newWidth;
                    $thumb_h = $newHeight;
                }

                $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
                imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

                // New save location
                $new_thumb_loc = storage_path() . '/app/public/uploads/User/userIcons/' . $fileName;

                if ($mime['mime'] == 'image/png') { $result = imagepng($dst_img, $new_thumb_loc, 8); }
                if ($mime['mime'] == 'image/jpg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80); }
                if ($mime['mime'] == 'image/jpeg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80); }
                if ($mime['mime'] == 'image/pjpeg') { $result = imagejpeg($dst_img, $new_thumb_loc, 80); }

                if ($mime['mime'] == 'image/webp') {
                    $result = imagejpeg($dst_img, $new_thumb_loc, 80);
                }

                imagedestroy($dst_img);
                imagedestroy($src_img);
            } else {
                $fileError = 1;
            }

            if ($fileError == 1) {
                Session::flash('error', "File type not valid");
                return redirect()->back();
            }
        } else {
            if (!empty($buyerDetails->profile)) {
                $fileName = $buyerDetails->profile;
            }
        }

        $arrUserInsert = [
            'first_name' => trim($request->input('first_name')),
            'last_name' => trim($request->input('last_name')),
            'email' => trim($request->input('email')),
            'user_type' => $request->input('user_type', 'student'),
            'country' => trim($request->input('country')),
            'birth_year' => $request->input('birth_year'),
            'school_name' => trim($request->input('school_name')),
            'grade_level' => trim($request->input('grade_level')),
            'status' => $request->input('status', 'active'),
        ];

        if ($fileName) {
            $arrUserInsert['profile'] = $fileName;
        }

        // Handle parent assignment for students
        if ($request->input('user_type') === 'student' && $request->has('parent_id')) {
            $arrUserInsert['parent_id'] = $request->input('parent_id') ?: null;
        }

        // Handle subjects for teachers
        if ($request->input('user_type') === 'teacher' && $request->input('subjects_teaching')) {
            $subjects = $request->input('subjects_teaching');
            $arrUserInsert['subjects_teaching'] = is_array($subjects) ? implode(',', $subjects) : $subjects;
        }

        if ($id != '') {
            User::where('id', '=', $id)->update($arrUserInsert);
        } else {
            if ($request->input('password')) {
                $arrUserInsert['password'] = bcrypt($request->input('password'));
            }
            $id = User::create($arrUserInsert)->id;
        }

        Session::flash('success', "Record saved successfully.");
        return redirect(route('adminUsers'));
    }

    /**
     * View user details
     */
    public function view($id)
    {
        $id = base64_decode($id);
        $user = User::with(['chatHistory', 'parent', 'children', 'teachers', 'students', 'classrooms', 'ownedClassrooms'])
            ->findOrFail($id);

        $data = [];
        $data['pageTitle'] = "User Details";
        $data['current_module_name'] = "User Details";
        $data['module_name'] = "Users";
        $data['module_url'] = route('adminUsers');
        $data['user'] = $user;
        $data['chatCount'] = ChatHistory::where('user_id', $id)->where('is_deleted', 0)->count();
        $data['recentChats'] = ChatHistory::where('user_id', $id)
            ->where('is_deleted', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Analytics
        $data['analytics'] = $user->getAnalyticsSummary(30);
        $data['weeklyAnalytics'] = UserAnalytics::where('user_id', $id)
            ->where('date', '>=', now()->subDays(7))
            ->orderBy('date')
            ->get();

        // Related users based on type
        if ($user->isStudent()) {
            $data['availableParents'] = User::where('is_deleted', '!=', 1)->where('user_type', 'parent')->get();
            $data['availableTeachers'] = User::where('is_deleted', '!=', 1)->where('user_type', 'teacher')->get();
            $data['availableClassrooms'] = Classroom::where('status', 'active')->get();
        } elseif ($user->isParent()) {
            $data['availableStudents'] = User::where('is_deleted', '!=', 1)->where('user_type', 'student')->whereNull('parent_id')->get();
        } elseif ($user->isTeacher()) {
            $data['availableStudents'] = User::where('is_deleted', '!=', 1)->where('user_type', 'student')->get();
        }

        return view('Admin/Users/view', $data);
    }

    /**
     * Delete user
     */
    public function delete($id) {
        if (empty($id)) {
            Session::flash('error', "Oops! Something went wrong, please try again.");
            return redirect(route('adminUsers'));
        }

        $id = base64_decode($id);
        $user = User::find($id);

        if (!empty($user)) {
            $oldData = $user->toArray();
            $user->update(['is_deleted' => 1]);
            $this->logDeleted($user, 'User deleted');
            Session::flash('success', "Record deleted successfully.");
            return redirect()->back();
        } else {
            Session::flash('error', "Oops! Something went wrong, please try again.");
            return redirect()->back();
        }
    }

    /**
     * Change user status
     */
    public function changeStatus($id, $status) {
        if (empty($id)) {
            Session::flash('error', "Oops! Something went wrong, please try again.");
            return redirect(route('adminUsers'));
        }
        $id = base64_decode($id);
        $user = User::findOrFail($id);
        $oldStatus = $user->status;

        $user->update(['status' => $status]);
        $this->logStatusChange($user, $oldStatus, $status, 'User status changed');

        Session::flash('success', "Status updated successfully.");
        return redirect()->back();
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $userIds = $request->input('user_ids', []);

        if (empty($userIds)) {
            return response()->json(['success' => false, 'message' => 'No users selected']);
        }

        $ids = array_map(function ($id) {
            return base64_decode($id);
        }, $userIds);

        try {
            switch ($action) {
                case 'delete':
                    User::whereIn('id', $ids)->update(['is_deleted' => 1]);
                    $this->logActivity('bulk_delete', null, 'Bulk deleted ' . count($ids) . ' users');
                    return response()->json(['success' => true, 'message' => count($ids) . ' users deleted successfully']);

                case 'activate':
                    User::whereIn('id', $ids)->update(['status' => 'active']);
                    $this->logActivity('bulk_activate', null, 'Bulk activated ' . count($ids) . ' users');
                    return response()->json(['success' => true, 'message' => count($ids) . ' users activated successfully']);

                case 'deactivate':
                    User::whereIn('id', $ids)->update(['status' => 'inactive']);
                    $this->logActivity('bulk_deactivate', null, 'Bulk deactivated ' . count($ids) . ' users');
                    return response()->json(['success' => true, 'message' => count($ids) . ' users deactivated successfully']);

                default:
                    return response()->json(['success' => false, 'message' => 'Invalid action']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $users = User::where('is_deleted', '!=', 1);

        if ($request->input('status')) {
            $users = $users->where('status', $request->input('status'));
        }
        if ($request->input('user_type')) {
            $users = $users->where('user_type', $request->input('user_type'));
        }
        if ($request->input('country')) {
            $users = $users->where('country', 'like', '%' . $request->input('country') . '%');
        }
        if ($request->input('date_from')) {
            $users = $users->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->input('date_to')) {
            $users = $users->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $users = $users->get();

        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Type', 'Country', 'School', 'Grade', 'Birth Year', 'Status', 'Created At', 'Last Active']);

            // Add data rows
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->first_name . ' ' . $user->last_name,
                    $user->email,
                    $user->user_type ?? 'student',
                    $user->country ?? 'N/A',
                    $user->school_name ?? 'N/A',
                    $user->grade_level ?? 'N/A',
                    $user->birth_year ?? 'N/A',
                    $user->status,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->last_active_at ? $user->last_active_at->format('Y-m-d H:i:s') : 'Never',
                ]);
            }

            fclose($file);
        };

        $this->logActivity('export', null, 'Exported ' . $users->count() . ' users to CSV');
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get users for AJAX
     */
    public function getUsers(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $userId = $request->input('userId');
        $userType = $request->input('user_type');

        $users = User::where('users.is_deleted', '=', 0)->where('users.status', '=', 'active');

        if (!empty($searchTerm)) {
            $users = $users->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%");
            });
        }

        if (!empty($userId)) {
            $users = $users->where('id', '=', $userId);
        }

        if (!empty($userType)) {
            $users = $users->where('user_type', '=', $userType);
        }

        $users = $users->get();

        return response()->json($users);
    }

    /**
     * Assign parent to student
     */
    public function assignParent(Request $request)
    {
        $studentId = base64_decode($request->input('student_id'));
        $parentId = $request->input('parent_id');

        $student = User::findOrFail($studentId);
        $student->update(['parent_id' => $parentId ?: null]);

        return response()->json(['success' => true, 'message' => 'Parent assigned successfully']);
    }

    /**
     * Assign teacher to student
     */
    public function assignTeacher(Request $request)
    {
        $studentId = base64_decode($request->input('student_id'));
        $teacherId = $request->input('teacher_id');
        $subject = $request->input('subject');

        $student = User::findOrFail($studentId);

        if ($teacherId) {
            $student->teachers()->syncWithoutDetaching([
                $teacherId => ['subject' => $subject, 'assigned_at' => now()]
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Teacher assigned successfully']);
    }

    /**
     * Remove teacher from student
     */
    public function removeTeacher(Request $request)
    {
        $studentId = base64_decode($request->input('student_id'));
        $teacherId = $request->input('teacher_id');

        $student = User::findOrFail($studentId);
        $student->teachers()->detach($teacherId);

        return response()->json(['success' => true, 'message' => 'Teacher removed successfully']);
    }

    /**
     * Add child to parent
     */
    public function addChild(Request $request)
    {
        $parentId = base64_decode($request->input('parent_id'));
        $studentId = $request->input('student_id');

        $student = User::findOrFail($studentId);
        $student->update(['parent_id' => $parentId]);

        return response()->json(['success' => true, 'message' => 'Child added successfully']);
    }

    /**
     * Remove child from parent
     */
    public function removeChild(Request $request)
    {
        $studentId = $request->input('student_id');

        $student = User::findOrFail($studentId);
        $student->update(['parent_id' => null]);

        return response()->json(['success' => true, 'message' => 'Child removed successfully']);
    }
}