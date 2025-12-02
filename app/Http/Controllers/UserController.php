<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatBot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
// use Smalot\PdfParser\Parser; 


class UserController extends Controller
{

    // GET [Auth: Token]
    public function profile()
    {

        $userData = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile information",
            "data" => $userData,
            "id" => auth()->user()->id
        ]);
    }


    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'birth_year' => 'nullable|max:9999',
            'country' => 'nullable|string|max:255',
            // 'bot_profile' => 'nullable|file', // Base64 string validation
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // return $request->bot_profile;
        $user =  $request->user(); // Auth::user();
        // return $request->country;
        // Exclude 'image' from the update to handle it separately
        $user->update($request->except('bot_profile'));

        // Handle Base64 image upload if provided and it's different from the current image
        // if ($request->hasFile('image')) { // $request->image && $request->image != $user->image
        //     $img = $request->file('image');
        //     // return $img;
        //     $imageData = base64_decode($request->image);
        //     $imageName = 'profile_images/' . uniqid() . '.png';

        //     // Delete the old image if exists
        //     if ($user->image) {
        //         // Storage::disk('public')->delete($user->image);
        //         unlink(public_path('assets/'.$user->img));
        //     }

        //     // Store the new image
        //     // Storage::disk('public')->put($imageName, $imageData);
        //     $img->move(public_path('assets/'), $imageName);
        //     $user->image = $imageName;
        //     $user->save();
        // }

        // if ($request->hasFile('bot_profile')) {
        //     try {
        //         $imageName = 'profile_images/' . uniqid() .'.'. $request->file('bot_profile')->getClientOriginalExtension();

        //         // Delete the old image if exists
        //         if ($user->image) {
        //             unlink(public_path('assets/' . $user->image));
        //         }

        //         // Store the new image
        //         $request->file('bot_profile')->move(public_path('assets/'), $imageName);

        //         // $user->image = $imageName;
        //         // $user->save();
        //         $user->update([
        //             'image' => $imageName
        //         ]);
        //     } catch (\Exception $e) {
        //         // Log the error or return a custom error response
        //         return response()->json(['message' => 'Error uploading image: ' . $e->getMessage()], 400);
        //     }
        // }

        if ($request->hasFile('bot_profile')) {
            try {
                $imageName =  uniqid() . '.' . $request->file('bot_profile')->getClientOriginalExtension(); // 'profile_images/' . 

                // Delete the old image if exists
                if ($user->image) {
                    unlink(public_path('assets/' . $user->image));
                }
                $img = $request->file('bot_profile');
                // Store the new image
                $img->move(public_path('assets/'), $imageName);

                // Update the user's image
                $user->update([
                    'image' => $imageName
                ]);
            } catch (\Exception $e) {
                // Log the error or return a custom error response
                return response()->json(['message' => 'Error uploading image: ' . $e->getMessage()], 400);
            }
        }

        return response()->json(['message' => 'Profile updated successfully.', 'user' => $user], 200);
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password does not match'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }

    // Save the bot ID for the logged-in user
    public function saveBot(Request $request)
    {
        $request->validate([
            'bot_id' => 'required|exists:chatbots,id', // Ensure the bot ID exists in the chatbots table
        ]);

        $user = Auth::user();
        $user->bot_id = $request->bot_id;
        $user->save();

        return response()->json([
            'message' => 'Bot ID saved successfully',
            'bot_id' => $user->bot_id,
        ], 200);
    }

    // Fetch the bot ID for the logged-in user
    // Fetch the bot data for the logged-in user
    public function getBot()
    {
        $user = Auth::user();

        if ($user->bot_id) {
            // Fetch the bot associated with the user's bot_id
            $bot = ChatBot::find($user->bot_id);

            if ($bot) {
                return response()->json([
                    'bot' => $bot,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Bot not found',
                ], 404);
            }
        } else {
            // Fetch the default bot where is_default = 1
            $defaultBot = ChatBot::where('is_default', 1)->first();

            if ($defaultBot) {
                return response()->json([
                    'bot' => $defaultBot,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No bot assigned and no default bot found',
                ], 404);
            }
        }
    }
    public function uploadFileNew_back(Request $request)
    {
        try {
            // Validate the incoming request to ensure at least file or text is provided
            $request->validate([
                'file' => 'nullable|max:2048', // File is optional, max size is 2MB file|mimes:png,jpeg,gif,webp
                'text' => 'nullable|string', // Text is optional, max length 255 characters
            ]);

            // Ensure at least one of file or text is provided
            if (!$request->hasFile('file') && !$request->input('text')) {
                return response()->json(['error' => 'Either file or text must be provided.'], 400);
            }

            $publicPath = null; // To hold the public URL of the uploaded file
            $pdfText = null; // To hold the public URL of the uploaded file
            $userText = $request->input('text', ''); // Get text or set default as an empty string

            // Handle file upload if the file is provided
            if ($request->file('file')) {
                // dd($request->hasFile('pdf'));
                $file = $request->file('file');

                // Get the original file extension
                $extension = $file->getClientOriginalExtension();
                // dd($extension);
                // Generate a unique name for the file using timestamp and random string
                $newFileName = time() . '_' . Str::random(10) . '.' . $extension;

                // Move the file to the 'public/uploads' directory
                $file->move(public_path('uploads'), $newFileName);

                // Create the public URL for the uploaded file
                // $publicPath = asset('uploads/' . $newFileName);



                if ($extension == 'pdf') {
                    //temparary file access
                    $pdfFile = $request->file('file');
                    $parser = new Parser();
                    $pdf = $parser->parseFile($pdfFile->getPathname());
                    $pdfText = $pdf->getText();

                    // Add extracted PDF text to messages                  
                } else {
                    // Create the public URL for the uploaded file
                    $publicPath = asset('uploads/' . $newFileName);
                }
            }
            //   return "hi";
            // Call the completion function and handle the response
            $response = $this->getCompletion($publicPath, $userText, $pdfText);
            // return response()->json(['data' => $response]);
            $assistantResponse = $response['choices'][0]['message']['content'] ?? 'No response from assistant.';
            //$botImageUrl = bot_profile
            $userId = auth()->id(); // Get the authenticated user's ID
            // return "hi";
            // Retrieve the user's bot ID
            $user = User::find($userId); // Adjust this if you're using a different method to get the user


            if (!empty($user->bot_id)) {
                $bot = Chatbot::where('id', '=', $user->bot_id)->get();
                $botImageUrl = $bot[0]->bot_profile;
            } else {
                $bot = Chatbot::where('is_default', '=', '1')->get();
                $botImageUrl = $bot[0]->bot_profile;
            }

            return response()->json(['id' => $bot[0]->id, 'assistance_response' => $assistantResponse, 'bot_image_url' => $botImageUrl], 200);
        } catch (ValidationException $e) {
            // Handle validation exceptions
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function uploadFileNew(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'file' => 'nullable|max:2048|mimes:jpeg,png,jpg,gif,webp,pdf', // File is optional, max size is 2MB
                'text' => 'nullable|string', // Text is optional
            ]);

            // Ensure at least one of file or text is provided
            if (!$request->hasFile('file') && !$request->input('text')) {
                return response()->json(['error' => 'Either file or text must be provided.'], 400);
            }

            $publicPath = null;
            $pdfText = null;
            $userText = $request->input('text', '');

            // Handle file upload if provided
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $newFileName = time() . '_' . Str::random(10) . '.' . $extension;

                // Move file to 'public/uploads'
                $file->move(public_path('uploads'), $newFileName);
                if ($extension == 'pdf') {
                    // return $request->hasFile('file');
                    $pdfFilePath = public_path('uploads/' . $newFileName);
                    $pdfText = $this->extractPdfText($pdfFilePath);
                } else {
                    $publicPath = asset('uploads/' . $newFileName);
                }
            }

            // Call AI processing function
            $response = $this->getCompletionNew($publicPath, $userText, $pdfText);
            // return $response;
            $assistantResponse = $response['choices'][0]['message']['content'] ?? 'No response from assistant.';
            // return $response;
            $userId = auth()->id();
            $user = User::find($userId);

            $bot = Chatbot::where('id', '=', $user->bot_id ?? 0)
                ->orWhere('is_default', '=', 1)
                ->first();

            return response()->json([
                'id' => $bot->id ?? null,
                'assistance_response' => $assistantResponse,
                'bot_image_url' => $bot->bot_profile ?? null
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage(), 'assistance_response' => 'I\'m busy, please ask again later', 'bot_image_url' => null]);
        }
    }

    /**
     * Đọc nội dung PDF bằng fopen()
     */
    // private function extractPdfText($pdfPath)
    // {
    //     $content = file_get_contents($pdfPath);
    //     if (!$content) {
    //         return 'Không thể đọc nội dung file PDF.';
    //     }

    //     // return trim($content);
    //     // Xóa các ký tự không hiển thị (PDF có thể chứa ký tự đặc biệt)
    //     $text = preg_replace('/[\x00-\x1F\x80-\xFF]/', ' ', $content);
    //     return trim($text);
    // }

    /**
     * Đọc nội dung PDF bằng fopen()
     */
    private function extractPdfText($pdfPath)
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();
            return trim($text);
        } catch (\Exception $e) {
            \Log::error('Error extracting PDF text: ' . $e->getMessage());
            return 'Không thể đọc nội dung file PDF.';
        }
    }


    public function getCompletionNew($imageUrl = null, $userText = null, $pdfText = null)
    {
        try {
            $messages = [];
            // Assuming you have access to the authenticated user
            $userId = auth()->id(); // Get the authenticated user's ID

            // Retrieve the user's bot ID
            $user = User::find($userId); // Adjust this if you're using a different method to get the user


            if (!empty($user->bot_id)) {
                $botId = $user->bot_id;
            } else {
                $bot = Chatbot::where('is_default', '=', '1')->get();
                $botId = $bot[0]->id;
            }

            $bot = Chatbot::find($botId);

            // Add the bot's prompt to messages
            if (isset($bot) && !empty($bot->prompt)) {
                $messages[] = [
                    'role' => 'system',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $bot->prompt
                        ],
                    ],
                ];
            }
            // Add text message if provided
            if ($userText) {
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $userText // Include the user's text input if provided
                        ],
                    ],
                ];
            }

            // Add image message if image URL is provided
            if ($imageUrl) {
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageUrl, // The URL of the uploaded image
                                'detail' => 'high',
                            ],
                        ],
                    ],
                ];
            }

            if ($pdfText) {

                // Add extracted PDF text to messages
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $pdfText, // Send extracted text to AI
                        ],
                    ],
                ];
            }

            // Send the request to OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('CHATGPT_SERVER_KEY'),
                'Content-Type' => 'application/json'
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('CHATGPT_VERSION'),
                'messages' => $messages,
            ]);


            // Check if the response is successful
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('API request failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            // Handle API exceptions
            return response()->json(['error' => 'API request error: ' . $e->getMessage()], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        try {
            // Validate the incoming request to ensure at least file or text is provided
            $request->validate([
                'file' => 'nullable|max:2048', // File is optional, max size is 2MB mimes:png,jpeg,gif,webp
                'text' => 'nullable|string', // Text is optional, max length 255 characters
            ]);

            // Ensure at least one of file or text is provided
            if (!$request->hasFile('file') && !$request->input('text')) {
                return response()->json(['error' => 'Either file or text must be provided.'], 400);
            }

            $publicPath = null; // To hold the public URL of the uploaded file
            $userText = $request->input('text', ''); // Get text or set default as an empty string

            // Handle file upload if the file is provided
            if ($request->file('file')) {
                $file = $request->file('file');

                // Get the original file extension
                $extension = $file->getClientOriginalExtension();

                // Generate a unique name for the file using timestamp and random string
                $newFileName = time() . '_' . Str::random(10) . '.' . $extension;

                // Move the file to the 'public/uploads' directory
                $file->move(public_path('uploads'), $newFileName);

                // Create the public URL for the uploaded file
                $publicPath = asset('uploads/' . $newFileName);
            }

            // Call the completion function and handle the response
            $response = $this->getCompletion($publicPath, $userText);
            // return response()->json(['data' => $response]);
            $assistantResponse = $response['choices'][0]['message']['content'] ?? 'No response from assistant.';
            //$botImageUrl = bot_profile
            $userId = auth()->id(); // Get the authenticated user's ID
            // return "hi";
            // Retrieve the user's bot ID
            $user = User::find($userId); // Adjust this if you're using a different method to get the user


            if (!empty($user->bot_id)) {
                $bot = Chatbot::where('id', '=', $user->bot_id)->get();
                $botImageUrl = $bot[0]->bot_profile;
            } else {
                $bot = Chatbot::where('is_default', '=', '1')->get();
                $botImageUrl = $bot[0]->bot_profile;
            }

            return response()->json(['id' => $bot[0]->id, 'assistance_response' => $assistantResponse, 'bot_image_url' => $botImageUrl], 200);
        } catch (ValidationException $e) {
            // Handle validation exceptions
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }



    public function getCompletion($imageUrl = null, $userText = null)
    {

        try {
            $messages = [];
            // Assuming you have access to the authenticated user
            $userId = auth()->id(); // Get the authenticated user's ID

            // Retrieve the user's bot ID
            $user = User::find($userId); // Adjust this if you're using a different method to get the user


            if (!empty($user->bot_id)) {
                $botId = $user->bot_id;
            } else {
                $bot = Chatbot::where('is_default', '=', '1')->get();
                $botId = $bot[0]->id;
            }

            $bot = Chatbot::find($botId);

            // Add the bot's prompt to messages
            if (isset($bot) && !empty($bot->prompt)) {
                $messages[] = [
                    'role' => 'system',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $bot->prompt
                        ],
                    ],
                ];
            }
            // Add text message if provided
            if ($userText) {
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $userText // Include the user's text input if provided
                        ],
                    ],
                ];
            }

            // Add image message if image URL is provided
            if ($imageUrl) {
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageUrl, // The URL of the uploaded image
                                'detail' => 'high',
                            ],
                        ],
                    ],
                ];
            }

            // Send the request to OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('CHATGPT_SERVER_KEY'),
                'Content-Type' => 'application/json'
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('CHATGPT_VERSION'),
                'messages' => $messages,
            ]);


            // Check if the response is successful
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('API request failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            // Handle API exceptions
            return response()->json(['error' => 'API request error: ' . $e->getMessage()], 500);
        }
    }
}
