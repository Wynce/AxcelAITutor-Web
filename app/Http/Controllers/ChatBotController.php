<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatBot;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ChatBotController extends Controller
{
   public function create(Request $request)
    {
        try {
            
            // Check if the user is an admin
            if (!Auth::check() || !Auth::user()->is_admin) {
                return response()->json([
                    "status" => false,
                    "message" => "Unauthorized. Admin access required.",
                ], 403);
            }
            
            // Validate the request data
            $validatedData = $request->validate([
                'bot_name' => 'required|string|max:255',
                'bot_type' => 'required|string',
                'base_bot' => 'required|string',
                'prompt' => 'nullable|string',
                'greeting_message' => 'nullable|string',
                'bot_bio' => 'nullable|string',
                'public_access' => 'required|boolean',
                'bot_recommendations' => 'required|boolean',
                'show_prompt' => 'required|boolean',
                'profilePath' => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // Validate image type and size
                'knowledge_base.*' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048', // Multiple files allowed
                'is_default' => 'nullable|boolean',
            ]);
            
            // If is_default is 1, update the existing default bot to is_default = 0
            if ($request->input('is_default') == 1) {
                ChatBot::where('is_default', 1)->update(['is_default' => 0]);
            }
            
            // Handle bot profile file
            if ($request->hasFile('profilePath')) {
                $profileFile = $request->file('profilePath');
                $profileFileName = 'bot_profile_' . time() . '.' . $profileFile->getClientOriginalExtension();
                // $profilePath = $profileFile->storeAs('chatbot/profile', $profileFileName, 'public'); // Store in public disk
                $profileFile->move(public_path('assets/chatbot/profile'), $profileFileName);
                $validatedData['bot_profile'] = 'chatbot/profile/'.$profileFileName; // Save the file path
            }
    
            // Handle knowledge base files
            $fileNames = [];
            if ($request->hasFile('knowledge_base')) {
                // Check if knowledge_base contains multiple files
                if (is_array($request->file('knowledge_base'))) {
                    foreach ($request->file('knowledge_base') as $file) {
                        $fileName = 'knowledge_base_' . time() . '_' . $file->getClientOriginalExtension();
                        // $filePath = $file->storeAs('chatbot/knowledge_base', $fileName, 'public'); // Store in public disk
                        $file->move(public_path('/assets/chatbot/knowledge_base'), $fileName);
                        $fileNames[] = 'chatbot/knowledge_base/'.$fileName;
                    }
                } else {
                    // Single file handling
                    $file = $request->file('knowledge_base');
                    $fileName = 'knowledge_base_' . time() . '.' . $file->getClientOriginalExtension();
                    // $filePath = $file->storeAs('chatbot/knowledge_base', $fileName, 'public');
                     $file->move(public_path('/assets/chatbot/knowledge_base'), $fileName);
                    $fileNames[] = 'chatbot/knowledge_base/'.$fileName;
                }
            }
    
            // If files were uploaded, store the paths
            if (!empty($fileNames)) {
                $validatedData['knowledge_base'] = implode(',', $fileNames); // Save the file paths as a comma-separated string
            }
    
            // Create the chatbot
            $chatbot = ChatBot::create($validatedData);
    
            return response()->json(['message' => 'Chatbot created successfully', 'data' => $chatbot], 201);
    
        } catch (ValidationException $e) {
            // Return validation error messages as JSON
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle any other errors
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the chatbot',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
 
    // 2. Update an existing bot
    public function update(Request $request, $id)
    {
        try {
            // Check if the user is an admin
            if (!Auth::check() || !Auth::user()->is_admin) {
                return response()->json([
                    "status" => false,
                    "message" => "Unauthorized. Admin access required.",
                ], 403);
            }
            
            // Find the chatbot or fail
            $chatbot = ChatBot::findOrFail($id);
    
            // Validate the request data
            $validatedData = $request->validate([
                'bot_name' => 'nullable|string|max:255',
                'bot_type' => 'nullable|string',
                'base_bot' => 'nullable|string',
                'prompt' => 'nullable|string',
                'greeting_message' => 'nullable|string',
                'bot_bio' => 'nullable|string',
                'public_access' => 'nullable|boolean',
                'bot_recommendations' => 'nullable|boolean',
                'show_prompt' => 'nullable|boolean',
                'bot_profile' => 'nullable|file|mimes:jpeg,jpg,png|max:2048', // Allow specific file types and set a max size
                'knowledge_base.*' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048', // Ensure each file is of a specific type and size
                'is_default' => 'nullable|boolean',
            ]);
        
        // If is_default is 1, update the existing default bot to is_default = 0
        if ($request->input('is_default') == 1) {
            ChatBot::where('is_default', 1)->where('id','!=', $id)->update(['is_default' => 0]);
        }


            // Handle bot profile file update
          if ($request->hasFile('bot_profile')) {
                // Get the original file extension
                $extension = $request->file('bot_profile')->getClientOriginalExtension();
                $bot_profile = $request->file('bot_profile');
                // Generate a new unique file name (e.g., timestamp with random string)
                $fileName = 'bot_profile_' . time() . '.' . $extension;
                
                // Save the file with the new name to the 'chatbot/profile' directory on the 'public' disk
                // $profilePath = $request->file('bot_profile')->storeAs('chatbot/profile', $fileName, 'public');
                $bot_profile->move(public_path('assets/chatbot/profile'), $fileName);
                
                // Store the new file path in the validatedData array
                $validatedData['bot_profile'] = 'chatbot/profile/' . $fileName;
            
                // Optionally delete the old file if it exists
                if ($chatbot->bot_profile) {
                    // Storage::disk('public')->delete($chatbot->bot_profile);
                    unlink(public_path('assets/'.$chatbot->bot_profile));
                }
            }

    
            // Handle multiple knowledge base file uploads update
           if ($request->hasFile('knowledge_base')) {
                $fileNames = [];
                foreach ($request->file('knowledge_base') as $file) {
                    // Get the original file extension
                    $extension = $file->getClientOriginalExtension();
                    
                    // Generate a new unique file name (e.g., timestamp with random string)
                    $fileName = 'knowledg_source_' . time() . '.' . $extension;
                    
                    // Save the file with the new name to the 'public' disk
                    // $file->storeAs('chatbot/knowledge_base', $fileName, 'public');
                    $file->move(public_path('assets/chatbot/knowledge_base'), $fileName);
                    
                    // Collect the new file name to store in the database
                    $fileNames[] = 'assets/' . $fileName;
                }
                
                // Save the file names in the database
                $validatedData['knowledge_base'] = implode(',', $fileNames);
            
                // Optionally delete old files
                if ($chatbot->knowledge_base) {
                    $oldFiles = explode(',', $chatbot->knowledge_base);
                    foreach ($oldFiles as $oldFile) {
                        // Storage::disk('public')->delete($oldFile);
                        unlink(public_path('assets/'.$oldFile));
                    }
                }
            }


    
            // Update the chatbot with validated data
            $chatbot->update($validatedData);
    
            return response()->json(['message' => 'Chatbot updated successfully', 'data' => $chatbot], 200);
    
        } catch (ValidationException $e) {
            // Return validation error messages as JSON
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the chatbot',
            ], 500);
        }
    }


    // 3. Get data of a specific bot
    public function show($id)
    {
        // Check if the user is an admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized. Admin access required.",
            ], 403);
        }
        $chatbot = ChatBot::findOrFail($id); 
        return response()->json($chatbot, 200);
    }

   // 4. Get list of all bots
    public function index()
    {
    
        // Fetch chatbots ordered by the latest creation date
        //$chatbots = ChatBot::orderBy('created_at', 'desc')->get();  // Assuming 'created_at' is the timestamp column
        
        if (!Auth::check() || !Auth::user()->is_admin) {
       
            $chatbots = ChatBot::where ('public_access', 1)->orderBy('created_at', 'desc')->get(); 
            
        }else{
            
            $chatbots = ChatBot::orderBy('created_at', 'desc')->get();  
        }
    
    
        return response()->json($chatbots, 200);
    }
    
    // 4. Delete a specific bot
    public function destroy($id)
    {
        // Check if the user is an admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized. Admin access required.",
            ], 403);
        }
    
        // Find the bot by ID, or return 404 if not found
        $chatbot = ChatBot::find($id);
        
        if (!$chatbot) {
            return response()->json([
                "status" => false,
                "message" => "Chatbot not found.",
            ], 404);
        }
    
        // Attempt to delete the bot
        if ($chatbot->delete()) {
            return response()->json([
                "status" => true,
                "message" => "Chatbot deleted successfully.",
            ], 200);
        }
    
        return response()->json([
            "status" => false,
            "message" => "Failed to delete chatbot.",
        ], 500);
    }


}