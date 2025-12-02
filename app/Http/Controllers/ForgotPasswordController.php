<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use DB;
use Session;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
  public function sendResetLinkEmail(Request $request)
    {
        // Validate the email input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Email value',
                'data' => $validator->errors() // Returning errors in the data field
            ], 422);
        }
    
        // Send the reset link to the user's email
        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => true,
                'message' => 'Password reset link sent!',
                'data' => []
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send reset link.',
                'data' => []
            ], 500);
        }
    }

    public function showResetForm(Request $request)
    {
        return view('reset-password')->with(['token' => $request->token, 'email' => $request->email]);
    }
    
    
    public function resetPassword(Request $request)
    {
        $data =array();
        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
    
        if ($status === Password::PASSWORD_RESET) { 
            $data['success'] = 'Password reset successfully. You can now log in.';
        } else { 
            $data['error'] = 'Failed to reset password. Please try again.';
        }
        return view('password-reset-response',$data);
    }

}
