<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin;
use App\Models\Role;
use App\Traits\LogsActivity;
use Auth;
use Session;
use Validator;
use Hash;

class AdminsController extends Controller
{
    use LogsActivity;

    /**
     * Show list of admin users
     */
    public function index()
    {
        $data = [];
        $data['pageTitle'] = "Admins";
        $data['current_module_name'] = "Admins";
        $data['module_name'] = "Admins";
        $data['module_url'] = route('admin.admins.index');
        $data['recordsTotal'] = 0;
        $data['currentModule'] = '';

        return view('Admin/Admins/index', $data);
    }

    /**
     * Get records for DataTables
     */
    public function getRecords(Request $request)
    {
        $admins = Admin::with('role')->whereNull('deleted_at');

        // Search functionality
        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $admins = $admins->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Status filter
        if (!empty($request->input('status'))) {
            $admins = $admins->where('status', $request->input('status'));
        }

        $recordsTotal = $admins->count();
        $admins = $admins->skip($request->input('start'))
            ->take($request->input('length'))
            ->orderBy('created_at', 'desc')
            ->get();

        $arr = [];
        foreach ($admins as $admin) {
            $statusBadge = $admin->status === 'active' 
                ? '<span class="badge badge-success">Active</span>' 
                : '<span class="badge badge-danger">Inactive</span>';

            $roleName = $admin->role ? $admin->role->name : 'No Role';

            $actions = '<a href="' . route('admin.admins.edit', base64_encode($admin->id)) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a> ';
            
            if ($admin->id != Auth::guard('admin')->id()) {
                $actions .= '<a href="' . route('admin.admins.delete', base64_encode($admin->id)) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash"></i></a> ';
                $newStatus = $admin->status === 'active' ? 'inactive' : 'active';
                $actions .= '<a href="' . route('admin.admins.changeStatus', [base64_encode($admin->id), $newStatus]) . '" class="btn btn-sm btn-warning"><i class="fas fa-toggle-' . ($admin->status === 'active' ? 'on' : 'off') . '"></i></a>';
            }

            $arr[] = [
                '<img src="' . asset('assets/admin/img/default-avatar.png') . '" class="img-circle" width="40" height="40">',
                $admin->name,
                $admin->email,
                $roleName,
                $statusBadge,
                $admin->last_login_at ? $admin->last_login_at->format('Y-m-d H:i') : 'Never',
                $admin->created_at->format('Y-m-d H:i'),
                $actions,
            ];
        }

        if (empty($arr)) {
            $arr[] = ['', '', '', '', '', '', '', 'No records found'];
        }

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $arr,
        ]);
    }

    /**
     * Show create/edit form
     */
    public function create(Request $request)
    {
        $id = $request->id;
        $data = [];
        $data['module_url'] = route('admin.admins.index');
        $data['roles'] = Role::where('is_active', true)->get();

        if ($id != '') {
            $id = base64_decode($id);
            $data['admin'] = Admin::findOrFail($id);
            $data['pageTitle'] = "Edit Admin";
            $data['current_module_name'] = "Edit Admin";
            $data['module_name'] = "Edit Admin";
            return view('Admin/Admins/edit', $data);
        } else {
            $data['pageTitle'] = "Add Admin";
            $data['current_module_name'] = "Add Admin";
            $data['module_name'] = "Add Admin";
            return view('Admin/Admins/create', $data);
        }
    }

    /**
     * Store/Update admin
     */
    public function store(Request $request)
    {
        $id = trim(base64_decode($request->input('id')));

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . ($id ? $id : 'NULL') . ',id,deleted_at,NULL',
            'role_id' => 'nullable|exists:roles,id',
        ];

        if (empty($id)) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required';
        } else {
            if ($request->filled('password')) {
                $rules['password'] = 'required|string|min:8|confirmed';
                $rules['password_confirmation'] = 'required';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role_id' => $request->input('role_id'),
            'status' => $request->input('status', 'active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        if ($id) {
            $admin = Admin::findOrFail($id);
            $oldData = $admin->toArray();
            $admin->update($data);
            $this->logUpdated($admin, $oldData, $admin->toArray(), 'Admin updated');
            Session::flash('success', 'Admin updated successfully.');
        } else {
            $admin = Admin::create($data);
            $this->logCreated($admin, 'New admin created');
            Session::flash('success', 'Admin created successfully.');
        }

        return redirect(route('admin.admins.index'));
    }

    /**
     * Delete admin
     */
    public function delete($id)
    {
        if (empty($id)) {
            Session::flash('error', 'Invalid request.');
            return redirect()->back();
        }

        $id = base64_decode($id);
        $admin = Admin::findOrFail($id);

        // Prevent deleting yourself
        if ($admin->id == Auth::guard('admin')->id()) {
            Session::flash('error', 'You cannot delete your own account.');
            return redirect()->back();
        }

        $this->logDeleted($admin, 'Admin deleted');
        $admin->delete();

        Session::flash('success', 'Admin deleted successfully.');
        return redirect()->back();
    }

    /**
     * Change status
     */
    public function changeStatus($id, $status)
    {
        if (empty($id)) {
            Session::flash('error', 'Invalid request.');
            return redirect()->back();
        }

        $id = base64_decode($id);
        $admin = Admin::findOrFail($id);

        // Prevent deactivating yourself
        if ($admin->id == Auth::guard('admin')->id() && $status === 'inactive') {
            Session::flash('error', 'You cannot deactivate your own account.');
            return redirect()->back();
        }

        $oldStatus = $admin->status;
        $admin->update(['status' => $status]);
        $this->logStatusChange($admin, $oldStatus, $status, 'Admin status changed');

        Session::flash('success', 'Status updated successfully.');
        return redirect()->back();
    }
}

