<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Laravel\Passport\PersonalAccessTokenResult;
use Illuminate\Auth\Events\Verified;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function getEmailFromAppleToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        return $payload['email'] ?? null;
    }

    public function register(Request $request)
    {

        // Custom validation for email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the email exists with is_deleted = 1
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser && $existingUser->is_deleted == 1) {
            // If the user exists and is deleted, restore the account and update details
            $existingUser->update([
                'password' => bcrypt($request->password),
                'is_deleted' => 0,
            ]);

            // Send the email verification link
            event(new Registered($existingUser));

            return response()->json([
                'status' => true,
                'message' => "User account restored. Please verify your email.",
                'data' => [],
            ], 200);
        } elseif ($existingUser) {
            // If the user exists and is not deleted, return an error
            return response()->json([
                'status' => false,
                'error' => 'The email has already been taken.'
            ], 422);
        }

        // If no user exists, create a new user
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Send the email verification link
        event(new Registered($user));

        return response()->json([
            'status' => true,
            'message' => "User Registered Successfully. Please verify your email.",
            'data' => [],
        ], 200);
    }

    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            "email" => "nullable|email|string",
            "login_type" => "required|string|in:app,google,facebook,apple",
            "password" => $request->login_type === 'app' ? 'required' : 'nullable',
            // "code" => $request->login_type === 'google' ? 'required' : 'nullable',
        ]);

        // Retrieve the user by email
        if ($request->login_type == 'apple') {
            $request['email'] = $this->getEmailFromAppleToken($request->code);
        }

        $user = User::where("email", $request->email)->first();
        $isFirstLogin = 0;
        if ($user && $user->is_first_login) {
            $isFirstLogin = $user->is_first_login;
            $user->update(['is_first_login' => false]);
        }

        if ($user) {
            if ($request->login_type === 'app') {
                if ($user->email_verified_at === null) {
                    return response()->json([
                        "status" => false,
                        "message" => "Please verify your email before logging in.",
                        "data" => []
                    ]);
                }

                if (!Hash::check($request->password, $user->password)) {
                    return response()->json([
                        "status" => false,
                        "message" => "Password didn't match.",
                        "data" => []
                    ]);
                }
            }

            if ($user->login_type !== $request->login_type) {
                $user->update(['login_type' => $request->login_type]);
            }

            if ($user->login_type == 'google' && $user->is_deleted == 1) {
                $user->update(['is_deleted' => 0]);
            }

            // Generate token
            $token = $user->createToken("aiTutorToken")->accessToken;
            // return $isFirstLogin;
            return response()->json([
                "status" => true,
                "message" => "Login successful",
                "token" => $token,
                "is_first_login" => $isFirstLogin,
                "data" => $user
            ]);
        } else {
            if ($request->login_type === 'google' || $request->login_type === 'facebook') {
                // Fetch user from Google
                try {
                    $googleUser = Socialite::driver('google')->stateless()->user();
                    // $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->code);
                    // $googleUser = Socialite::driver('google')->with(['code' => $request->code])->stateless()->user();

                } catch (\Exception $e) {
                    return response()->json([
                        "status" => false,
                        "message" => "Failed to authenticate with Google.",
                        "error" => $e->getMessage()
                    ], 400);
                }

                // try {
                //     $authCode = $request->code; // The auth code sent from frontend

                //     if (!$authCode) {
                //         return response()->json(['status' => false, 'message' => 'Authorization code missing.'], 400);
                //     }

                //     // Exchange auth code for access token
                //     $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                //         'code' => $authCode,
                //         'client_id' => env('GOOGLE_CLIENT_ID'),
                //         'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                //         'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
                //         'grant_type' => 'authorization_code',
                //     ]);

                //     $tokenData = $response->json();        

                //     $googleUser = Socialite::driver('google')->stateless()->userFromToken($tokenData['access_token']);

                //     // Find or create user
                //     $user = User::updateOrCreate(
                //         ['email' => $googleUser->getEmail()],
                //         [
                //             'first_name' => $googleUser->getName(),
                //             'google_id' => $googleUser->getId(),
                //             'login_type' => 'google',
                //             'email_verified_at' => now(),
                //             'is_deleted' => 0,
                //             'password' => bcrypt(Str::random(16))
                //         ]
                //     );

                //     // Generate token
                //     $token = $user->createToken("aiTutorToken")->accessToken;

                //     return response()->json([
                //         "status" => true,
                //         "message" => "Login successful (new user created)",
                //         "token" => $token,
                //         "is_first_login" => $user->is_first_login,
                //         "data" => $user
                //     ]);
                // } catch (\Exception $e) {
                //     return response()->json([
                //         "status" => false,
                //         "message" => "Failed to authenticate with Google.",
                //         "error" => $e->getMessage()
                //     ], 400);
                // }

                // Find or create user
                $user = User::updateOrCreate(
                    ['email' => $googleUser->getEmail()],
                    [
                        'first_name' => $googleUser->getName(),
                        'google_id' => $googleUser->getId(),
                        'login_type' => 'google',
                        'email_verified_at' => now(),
                        'is_deleted' => 0,
                        'password' => bcrypt(Str::random(16))
                    ]
                );

                // Generate token
                $token = $user->createToken("aiTutorToken")->accessToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login successful (new user created)",
                    "token" => $token,
                    "is_first_login" => $user->is_first_login,
                    "data" => $user
                ]);
            } else if ($request->login_type === 'apple') {
                $email = $this->getEmailFromAppleToken($request->code);

                if ($email) {
                    // Find or create user
                    $user = User::updateOrCreate(
                        ['email' => $email],
                        [
                            'login_type' => 'apple',
                            'email_verified_at' => now(),
                            'is_deleted' => 0,
                            'password' => bcrypt(Str::random(16))
                        ]
                    );

                    // Generate token
                    $token = $user->createToken("aiTutorToken")->accessToken;

                    return response()->json([
                        "status" => true,
                        "message" => "Login successful (new user created)",
                        "token" => $token,
                        "is_first_login" => $user->is_first_login,
                        "data" => $user
                    ]);
                }
            }

            return response()->json([
                "status" => false,
                "message" => "Please enter a valid email address.",
                "data" => []
            ]);
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Failed to authenticate with Google.",
                "error" => $e->getMessage()
            ], 400);
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'first_name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'login_type' => 'google',
                'email_verified_at' => now(),
                'is_deleted' => 0,
                'password' => bcrypt(Str::random(16))
            ]
        );

        // Generate token
        $token = $user->createToken('aiTutorToken')->accessToken;

        return response()->json([
            "status" => true,
            'token' => $token,
            'is_first_login' => $user->is_first_login,
            "data" => $user
        ]);
    }


    public function loginOLD(Request $request)
    {
        // Validate the request
        $request->validate([
            "email" => "required|email|string",
            "login_type" => "required|string|in:app,google,facebook", // Ensure valid login type
            "password" => $request->login_type === 'app' ? 'required' : 'nullable', // Password required only for 'app' login type
        ]);

        // Retrieve the user by email
        $user = User::where("email", $request->email)->first();

        if ($user) {
            // If logging in via app, validate the password
            if ($request->login_type === 'app') {
                // Check if the user is verified
                if ($user->email_verified_at === null) {
                    return response()->json([
                        "status" => false,
                        "message" => "Please verify your email before logging in.",
                        "data" => []
                    ]);
                }

                // Check if the password matches
                if (!Hash::check($request->password, $user->password)) {
                    return response()->json([
                        "status" => false,
                        "message" => "Password didn't match.",
                        "data" => []
                    ]);
                }
            }

            // Update the login type if different
            if ($user->login_type !== $request->login_type) {
                $user->update([
                    'login_type' => $request->login_type
                ]);
            }

            if ($user->login_type == 'google' && $user->is_deleted == 1) {
                $user->update([
                    'is_deleted' => 0
                ]);
            }
            // Generate a token for the user
            $token = $user->createToken("aiTutorToken")->accessToken;

            return response()->json([
                "status" => true,
                "message" => "Login successful",
                "token" => $token,
                "data" => $user
            ]);
        } else {
            // If the user does not exist and login_type is google or facebook, register them
            if ($request->login_type === 'google' || $request->login_type === 'facebook') {
                // Create a new user with verified email for social logins
                $user = User::create([
                    'email' => $request->email,
                    'login_type' => $request->login_type,
                    'is_deleted' => 0,
                    'email_verified_at' => now(), // Automatically verify the email for social logins
                ]);

                // Generate a token for the new user
                $token = $user->createToken("aiTutorToken")->accessToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login successful (new user created)",
                    "token" => $token,
                    "data" => $user
                ]);
            }

            // For app login, return an invalid email response
            return response()->json([
                "status" => false,
                "message" => "Please enter a valid email address.",
                "data" => []
            ]);
        }
    }



    // GET [Auth: Token]
    public function logout()
    {

        $token = auth()->user()->token();

        $token->revoke();

        return response()->json([
            "status" => true,
            "message" => "User Logged out successfully"
        ]);
    }

    public function verify(Request $request, $id, $hash)
    {
        // Retrieve the user by their ID
        $user = User::findOrFail($id);

        // Check if the hash matches the user's email verification hash
        if (!hash_equals((string)$hash, sha1($user->getEmailForVerification()))) {
            return view('verification', [
                'status' => false,
                'message' => 'Invalid verification link'
            ]);
        }

        // If the user's email is already verified
        if ($user->hasVerifiedEmail()) {
            return view('verification', [
                'status' => true,
                'message' => 'Email already verified'
            ]);
        }

        // Mark the user's email as verified
        $user->markEmailAsVerified();

        // Fire the Verified event
        event(new Verified($user));

        return view('verification', [
            'status' => true,
            'message' => 'Email has been verified successfully.'
        ]);
    }

    public function resendVerificationLink(Request $request)
    {
        // Validate the incoming request to ensure email is provided
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please enter a valid email address.',
                'data' => $validator->errors()
            ], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // If the user does not exist
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'data' => $user
            ], 404);
        }

        // Check if the user's email is already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Your email is already verified',
                'data' => $user
            ], 200);
        }

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'A new verification link has been sent to your email address',
            'data' => $user
        ], 200);
    }
}