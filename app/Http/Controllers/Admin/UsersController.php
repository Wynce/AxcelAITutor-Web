<?php



namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use App\Models\User;
/*Uses*/

use Auth;

use Session;

use flash;

use Validator;

use DB;

use File;

class UsersController extends Controller

{

    /*

     * Define abjects of models, services.

     */

    function __construct() {



    }



    /**

     * Show list of records for products.

     * @return [array] [record array]

     */

    public function index() {

        $data = [];

        $data['pageTitle']              = "Users";

        $data['current_module_name']    = "Users";

        $data['module_name']            = "Users";

        $data['module_url']             = route('adminUsers');

        $data['recordsTotal']           = 0;

        $data['currentModule']          = '';

        return view('Admin/Users/index', $data);

    }



    /**

     * [getRecords for product list.This is a ajax function for dynamic datatables list]

     * @param  Request $request [sent filters if applied any]

     * @return [JSON]           [users list in json format]

     */

    public function getRecords(Request $request) {
        //DB::enableQueryLog();
        $user = Auth::guard('admin')->user();
        $currentUpasanaKendra = $user->upasana_kendra_id;
        $usersDetails = User::select('users.*')->where('users.is_deleted','!=',1);
        
        if(!empty($request['search']['value'])) {

            $field = ['users.first_name','users.email','users.country']; // 'users.username',

            $namefield = ['users.first_name','users.email','users.country'];  // 'users.username',

            $search=($request['search']['value']);

            $usersDetails = $usersDetails->Where(function ($query) use($search, $field,$namefield) {
                if (strpos($search, ' ') !== false){

                    $s=explode(' ',$search);

                    foreach($s as $val) {
                        for ($i = 0; $i < count($namefield); $i++){
                            $query->orwhere($namefield[$i], 'like',  '%' . $val .'%');
                        }
                    }
                }
                else {

                    for ($i = 0; $i < count($field); $i++){
                        $query->orwhere($field[$i], 'like',  '%' . $search .'%');
                    }
                }
            });
        }

        /*if(isset($request['order'][0])){
            $postedorder=$request['order'][0];
            if($postedorder['column']==0) $orderby='users.id';
            if($postedorder['column']==1) $orderby='users.name';
            if($postedorder['column']==2) $orderby='users.email';
            if($postedorder['column']==3) $orderby='users.phone_number'; 
            if($postedorder['column']==4) $orderby='users.location';
            $orderorder=$postedorder['dir'];
            $usersDetails = $usersDetails->orderby($orderby, $orderorder);
        }
        */

        if(isset($request['order'][0])){
            $postedorder = $request['order'][0];
            if($postedorder['column'] == 0) $orderby = 'users.id';
            // elseif($postedorder['column'] == 1) $orderby = 'users.username';
            elseif($postedorder['column'] == 1) $orderby = 'users.first_name'; 
            elseif($postedorder['column'] == 2) $orderby = 'users.email';
            elseif($postedorder['column'] == 3) $orderby = 'users.country'; 
            elseif($postedorder['column'] == 4) $orderby = 'users.birth_year'; 
            
            // Set default order as descending for initial load
            $orderorder = $postedorder['dir'] == 'asc' ? 'desc' : 'asc';
            $usersDetails = $usersDetails->orderBy($orderby, $orderorder);
        } else {
            // Default sorting when no specific column is selected
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
                if(!empty($recordDetailsVal['image'])) {
                    $image  = asset('assets/'.$recordDetailsVal['image']);
                }
                else{
                    $image  = asset('assets/profile_images/default/no-image.png');
                }

                $image  =   '<img src="'.$image.'" width="70" height="70">';

                // $username = (!empty($recordDetailsVal['username'])) ? mb_convert_encoding($recordDetailsVal['username'], 'UTF-8', 'auto')  : '-';
                $fullname = (!empty($recordDetailsVal['first_name'])) ? $recordDetailsVal['first_name']." ".@$recordDetailsVal['last_name']  : '-';

                $email = (!empty($recordDetailsVal['email'])) ? $recordDetailsVal['email']  : '-';
                $country   = (!empty($recordDetailsVal['country'])) ? $recordDetailsVal['country'] : '-';
                $birth_year   = (!empty($recordDetailsVal['birth_year'])) ? $recordDetailsVal['birth_year'] : '-';
               
                 if ($recordDetailsVal['status'] == 'active') {
                    $status = '<a href="javascript:void(0)" onclick=" return ConfirmStatusFunction(\''.route('adminUserChangeStatus', [base64_encode($recordDetailsVal['id']), 'block']).'\');" class="btn btn-icon btn-success" title="Block"><i class="fa fa-unlock"></i> </a>';
                } else {
                    $status = '<a href="javascript:void(0)" onclick=" return ConfirmStatusFunction(\''.route('adminUserChangeStatus', [base64_encode($recordDetailsVal['id']), 'active']).'\');" class="btn btn-icon btn-danger" title="Active"><i class="fa fa-lock"></i> </a>';
                }
               /* $action = '<a href="'.route('adminUserEdit', base64_encode($id)).'" title="Edit" class="btn btn-icon btn-success"><i class="fas fa-edit"></i> </a>&nbsp;&nbsp;';*/
                $action .= '<a href="javascript:void(0)" onclick=" return ConfirmDeleteFunction(\''.route('adminUserDelete', base64_encode($id)).'\');"  title="Delete" class="btn btn-icon btn-danger"><i class="fas fa-trash"></i></a>';
 
                $i++;

                $arr[] = [ $image , $fullname, $email, $birth_year , $country,$status,$action]; // , $username

            }

        }
        else {
            $arr[] = ['','','', "No record found" ,'' ,'', '', ''];
        }

        $json_arr = [
            'draw'            => $request->input('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data'            => ($arr),
        ];

        return json_encode($json_arr);
    }


       /* function to open user create add/edit form */
    public function create(Request $request) {
      
        $id         = $request->id;
  
        $data['module_url']     = route('adminUsers');
       
        if($id !=''){
            $data['id'] = $id;
            $id = base64_decode($id);
            $data['userDetails'] = User::where('id', $id)->first();
            $user_label = trans('admin.edit_users');
            $data['pageTitle']              = $user_label;
            $data['current_module_name']    = $user_label;
            $data['module_name']            = $user_label;

            $data['upasana_kendra'] = DB::table('upasana_kendra')
                   ->where('is_deleted','!=',1)
                   ->where('status','=','active')
                   ->get();

            return view("Admin/Users/edit", $data);
        }else{
            
            $user_label = trans('admin.add_users');
            $data['pageTitle']              = $user_label;
            $data['current_module_name']    = $user_label;
            $data['module_name']            = $user_label;
            return view("Admin/Users/create", $data);
        }     
    }
    

    /*function to save/update users details*/
    public function store(Request $request) {

        $id =trim(base64_decode($request->input('id')));
        $role_id = $request->input('role_id');
        $rules = [
        'name'  => 'required',
        'phone_number' => 'required',
        'location' => 'required',    
        'latitude'    =>  'required',
        'longitude'   =>  'required',
        'upasana_kendra' => 'required',
        ];

        if($request->input('id')!='') {
            $rules['email']         = 'required|regex:/(.*)\.([a-zA-z]+)/i|unique:users,email,'.$id;
        }
        else{
           $rules['email']        = "required|email|unique:users,email,NULL,id,is_deleted,0";
        }

        $messages = [
            'name.required'            => trans('admin.enter_fullname'),
            'phone_number.required'        => trans('admin.fill_in_phone_no_err'),
            'email.unique'                 => trans('admin.email_already_exist_err'),
            'email.email'                  => trans('admin.fill_in_valid_email_err'),
            'email.required'               => trans('admin.fill_in_email_err'),
            'location.required'            => trans('admin.enter_address'),
            'latitude.required'            => "Please select your location",
            'longitude.required'           => "Please select your location",
            'upasana_kendra.required'      => "Please select upasana kendra",
        ];

        $validator = validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            $messages = $validator->messages(); //p($messages);
            return redirect()->back()->withInput($request->all())->withErrors($messages);
        }


        $created_at = date('Y-m-d H:i:s');
        //$password = Str::random(8);//Hash::make(str_random(8));

     
        $fileName = '';
        $buyerDetails = User::find($id);
         
        if($request->hasfile('profile'))
            {
                $image = $request->file('profile');
              
                if(!empty($buyerDetails->profile)){
                    $image_path = storage_path().'/app/public/uploads/User/'.$buyerDetails->profile; 
                    $resized_image_path = storage_path().'/app/public/uploads/User/'.$buyerDetails->profile; 
                   
                    if (File::exists($image_path)) {
                        unlink($image_path);
                    }

                    if (File::exists($resized_image_path)) {
                        unlink($resized_image_path);
                    }
                }

                $fileError = 0;
                $image = $request->file('profile');
                $name=$image->getClientOriginalName();
                $fileExt  = strtolower($image->getClientOriginalExtension());
                if(in_array($fileExt, ['jpg', 'jpeg', 'png','webp'])) {
                    $fileName = 'Buyer_'.date('YmdHis').'.'.$fileExt;
                  
                    Storage::disk('public')->put('uploads/User/' . $fileName, File::get($image));

                    $path = storage_path().'/app/public/uploads/User/'.$fileName; 
                    $mime = getimagesize($path);

                    if($mime['mime']=='image/png'){ $src_img = imagecreatefrompng($path); }
                    if($mime['mime']=='image/jpg'){ $src_img = imagecreatefromjpeg($path); }
                    if($mime['mime']=='image/jpeg'){ $src_img = imagecreatefromjpeg($path); }
                    if($mime['mime']=='image/pjpeg'){ $src_img = imagecreatefromjpeg($path); }


                    if ($mime['mime'] == 'image/webp') {
                        $image = Image::make($path);
                        $src_img = $image->getCore();
                    }

                    $old_x = imageSX($src_img);
                    $old_y = imageSY($src_img);

                    $newWidth = 300;
                    $newHeight = 300;

                    if($old_x > $old_y){
                        $thumb_w    =   $newWidth;
                        $thumb_h    =   $old_y/$old_x*$newWidth;
                    }

                    if($old_x < $old_y){
                        $thumb_w    =   $old_x/$old_y*$newHeight;
                        $thumb_h    =   $newHeight;
                    }

                    if($old_x == $old_y){
                        $thumb_w    =   $newWidth;
                        $thumb_h    =   $newHeight;
                    }

                    $dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);
                    imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);

                    // New save location
                    $new_thumb_loc = storage_path().'/app/public/uploads/User/resized/'.$fileName; 
                    if($mime['mime']=='image/png'){ $result = imagepng($dst_img,$new_thumb_loc,8); }
                    if($mime['mime']=='image/jpg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
                    if($mime['mime']=='image/jpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
                    if($mime['mime']=='image/pjpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }


                    if ($mime['mime'] == 'image/webp') {
                         $result = imagejpeg($dst_img,$new_thumb_loc,80);
                    }

                    imagedestroy($dst_img);
                    imagedestroy($src_img);

                     /*resized for product details page small image*/
                        $mime = getimagesize($path);
            
                        if($mime['mime']=='image/png'){ $src_img = imagecreatefrompng($path); }
                        if($mime['mime']=='image/jpg'){ $src_img = imagecreatefromjpeg($path); }
                        if($mime['mime']=='image/jpeg'){ $src_img = imagecreatefromjpeg($path); }
                        if($mime['mime']=='image/pjpeg'){ $src_img = imagecreatefromjpeg($path); }
                        
                        if ($mime['mime'] == 'image/webp') {
                            $image = Image::make($path);
                            $src_img = $image->getCore();
                        }

                        $old_x = imageSX($src_img);
                        $old_y = imageSY($src_img);
    
                        $newWidth = 70;
                        $newHeight = 70;
    
                        if($old_x > $old_y) {
                            $thumb_w    =   $newWidth;
                            $thumb_h    =   $old_y/$old_x*$newWidth;
                        }
    
                        if($old_x < $old_y) {
                            $thumb_w    =   $old_x/$old_y*$newHeight;
                            $thumb_h    =   $newHeight;
                        }
    
                        if($old_x == $old_y) {
                            $thumb_w    =   $newWidth;
                            $thumb_h    =   $newHeight;
                        }
    
                        $dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);
                        imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
    
                        // New save location
                        $new_thumb_loc = storage_path().'/app/public/uploads/User/userIcons/'.$fileName;
    
                        if($mime['mime']=='image/png'){ $result = imagepng($dst_img,$new_thumb_loc,8); }
                        if($mime['mime']=='image/jpg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
                        if($mime['mime']=='image/jpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
                        if($mime['mime']=='image/pjpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,80); }
                        
                        if ($mime['mime'] == 'image/webp') {
                            $result = imagejpeg($dst_img,$new_thumb_loc,80);
                        }

                        imagedestroy($dst_img);
                        imagedestroy($src_img);        
                }
                else {
                    $fileError = 1;
                }

                if($fileError == 1)
                {
                    Session::flash('error', trans('admin.file_not_valid_err'));
                    return redirect()->back();
                }
            } else{
                if(!empty($buyerDetails->profile)){
                    $fileName = $buyerDetails->profile;
                }
            }
        $arrUserInsert = [
            'name'          => trim($request->input('name')),
            'phone_number'      => trim($request->input('phone_number')),
            'location' => trim($request->input('location')),
            'email'             => trim($request->input('email')), 
            'profile'      => $fileName,
            'latitude'  => number_format(trim($request->input('latitude')), 4, '.', ''),
            'longitude' => number_format(trim($request->input('longitude')), 4, '.', ''),
            'upasana_kendra_id' => trim($request->input('upasana_kendra')),
        ];
;
        if($id != ''){
           User::where('id','=',$id)->update($arrUserInsert);
        }else{
           $id = User::create($arrUserInsert)->id;     
        }
       
        Session::flash('success', "Record saved successfully.");
        return redirect(route('adminUsers',base64_encode($role_id)));
    }

      /**
    * Delete Record
    * @param  $id = Id
    */
    public function delete($id) {
        if(empty($id)) {
            Session::flash('error', "Opps.! Something went wrong, please try again.");
            return redirect(route('adminUsers'));
        }

        $id = base64_decode($id);
        $result = User::find($id);

        if (!empty($result)){
            $users = User::where('id', $id)->update(['is_deleted' => '1']);
            Session::flash('success', "Record deleted successfully.");
            return redirect()->back();
        } else {
            Session::flash('error', "Opps.! Something went wrong, please try again.");
            return redirect()->back();
        }
    }

      /**
     * Change status for Record [active/block].
     * @param  $id = Id, $status = active/block 
     */
    public function changeStatus($id, $status) {
        if(empty($id)) {
            Session::flash('error', "Opps.! Something went wrong, please try again.");
            return redirect(route('adminUsers'));
        }
        $id = base64_decode($id);

        $result = User::where('id', $id)->update(['status' => $status]);
        if ($result) {
            Session::flash('success', "Status updated successfully.");
            return redirect()->back();
        } else {
            Session::flash('error', "Opps.! Something went wrong, please try again.");
            return redirect()->back();
        }
    }

    public function getUsers(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $userId     = $request->input('userId');
        $users = User::where('users.is_deleted','=',0)->where('users.status','=','active');

        if(!empty($searchTerm)){
            $users = $users->where('name', 'like', "%{$searchTerm}%");
        }

        if(!empty($userId)){ 
            $users = $users->where('id', '=', $userId);
        }

        $users = $users->get();
        // Perform the database query to fetch users based on the search term

        // Return JSON response containing the users
        return response()->json($users);
    }

}