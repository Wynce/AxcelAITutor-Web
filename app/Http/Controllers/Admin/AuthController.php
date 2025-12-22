<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Models\Admin;
use App\Models\User;

/*Uses*/
use Auth;
use Session;
use flash;
use Validator;
use File;

class AuthController extends Controller
{
    /*
     * Define abjects of models, services.
     */
    function __construct() {
       
    }

    /**
     * Function for Show Login
     */
    public function index() {
        $data = [];

        if (Auth::guard('admin')->check()) {
            return redirect(route('adminDashboard'));
        }
        
        $data['pageTitle'] = "Log In";
        
        return view('Admin/login')->with($data);
    }

    /**
     * Function for refresh captcha
     */
    public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_img()]);
    }

    /*function use for ThrottlesLogins to check too many logins */
    public function username() {
     return 'email';
    }
    /**
     * Login functionlity for admin
     * @param  Request $request 
     */
    public function login(Request $request) {

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $messages = [
            'email.required'     => 'Please fill in your email',
            'email.email'        => 'Please Enter valid email',
            'password.required'  => 'Enter your password',
        ];


        $validator = validator::make($request->all(), $rules, $messages);
        if($validator->fails()) 
        {
            $messages = $validator->messages();
            return redirect()->back()->withInput($request->all())->withErrors($messages);
        }
        else 
        {
            $credentials = [
                'email'     => $request->input('email'),
                'password'  => $request->input('password'),
            ];

            $remember = false;
            if($request->input('remember'))
            {
                $remember = true;
            }
           
            $res = Auth::guard('admin')->attempt($credentials,$remember);

            if ($res) {
                $user = Auth::guard('admin')->user();
                $current_user = $user->name ?? ($user->first_name ?? '')." ".($user->last_name ?? ''); 
                Session::put('current_user', $current_user);
                
                // Update last login
                $user->updateLastLogin(request()->ip());

                // clear login attempt
               // $this->clearLoginAttempts($request);
                return redirect(route('adminDashboard'));
                
            }
            else {
                
                /*increament login attempt*/
               // $this->incrementLoginAttempts($request);
                Session::flash('error','Invalid email or password!');
                return redirect()->back()->withInput();
            }
        }
    }

     /**
     * Function for dashboard
     */
    public function dashboard(Request $request) {
        //echo "dsghj";exit;
        $data = $siteSetting = [];
        $user = Auth::guard('admin')->user();

        $data['pageTitle']           = "Dashboard";
        $data['module_name']         = "Summary";
        $data['current_module_name'] = '';
        $data['module_url']          = route('adminDashboard');
        $data['pageTitle']           = "Dashboard";
         
        $currentYear    = date('Y');
        $currentMonth   = date('m');

        $data['userCount']     = User::get_users_count();

        // Additional dashboard metrics
        $data['activeUsers'] = User::where('is_deleted','!=',1)->where('status','active')->count();
        $data['blockedUsers'] = User::where('is_deleted','!=',1)->where('status','blocked')->count();
        $data['newUsers7d'] = User::where('is_deleted','!=',1)->whereDate('created_at','>=', now()->subDays(7))->count();
        $data['chatsTotal'] = \App\Models\ChatHistory::where('is_deleted',0)->count();
        $data['chats7d'] = \App\Models\ChatHistory::where('is_deleted',0)->whereDate('created_at','>=', now()->subDays(7))->count();

        return view('Admin/dashboard', $data);
    }

    

    /**
     * Function for Change Password
     */
    public function changePassword() {
        $data = $siteSetting = [];

        $data['pageTitle'] = "Change Password";
        $data['module_name'] = "Change Password";
        $data['current_module_name'] = '';
        $data['module_url'] = route('adminChangePassword');
        return view('Admin/change_password', $data);
    }

    /**
     * Function for Change Password Store
     */
    public function changePasswordStore(Request $request) {

        $rules = [
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required',

        ];
        $messages = [
            'password.required'              => 'Enter your password',
            'password.confirmed'             => 'Password and Confirm Password must be same!',
            'password_confirmation.required' => 'Please fill in your confirm password',
        ];

        $validator = validator::make($request->all(), $rules, $messages);
        if($validator->fails()) 
        {
            $messages = $validator->messages();
            return redirect()->back()->withInput($request->all())->withErrors($messages);
        }
        else 
        {
            $user_id = \Auth::guard('admin')->id();
            
            $arrUpdate = ['password'=>bcrypt($request->input('password'))];
            Admin::where('id', $user_id)->update($arrUpdate);

            Session::Flash('success', 'Password changed successfully!');
            return redirect(route('adminChangePassword'));
        }
    }

    /**
     * Function for logout
     */
    public function logout(Request $request) { 
           $user = Auth::guard('admin')->user();
          
        Auth::guard('admin')->logout();
        if(!empty($msg)) {
            if($type=='success') {
                \Session::flash('success', $msg);    
            }
            else {
                \Session::flash('error', $msg);
            }
        }
        
        return redirect(route('adminDashboard'));
        
    }  

}