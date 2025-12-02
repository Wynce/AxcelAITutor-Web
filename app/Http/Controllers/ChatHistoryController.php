<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\ChatHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ChatHistoryController extends Controller
{
    
    // public function saveChatHistory(Request $request)
    // {
    //     $request->validate([
    //         'messages' => 'required|array|min:1',
    //         'messages.*.text' => 'nullable|string', // Allow empty strings
    //         'messages.*.user._id' => 'required|integer',
    //         'messages.*.image' => 'nullable|string', // Accept base64 string
    //     ]);
    
    //     $user = Auth::user();
    //     $chatId = uniqid(); // Generate a unique chat ID
    
    //     // Arrays to hold user messages and bot responses
    //     $userMessages = [];
    //     $botResponses = [];
    
    //     foreach ($request->messages as $messageData) {
    //         // Handle image upload if it exists
    //         $imagePath = null; // Reset imagePath for each message
    //         if (isset($messageData['image'])) {
    //             if (preg_match('/^data:image\/(\w+);base64,/', $messageData['image'], $type)) {
    //                 $imageData = substr($messageData['image'], strpos($messageData['image'], ',') + 1);
    //                 $imageData = base64_decode($imageData);
    //                 $extension = strtolower($type[1]); // Get the image type
    
    //                 // Generate a unique name for the image
    //                 $fileName = uniqid() . '.' . $extension;
    
    //                 // Store the image in the desired location
    //                 $imagePath = 'chat_images/' . $fileName;
    //                 \Storage::disk('public')->put($imagePath, $imageData);
    //             }
    //         }
    
    //         // Separate user messages and bot responses based on user ID
    //         if ($messageData['user']['_id'] == 1) {
    //             $userMessages[] = ['text' => $messageData['text'], 'image' => $imagePath];
    //         } elseif ($messageData['user']['_id'] == 2) {
    //             $botResponses[] = ['text' => $messageData['text'], 'image' => $imagePath];
    //         }
    //     }
        
    //     $getBotId = DB::table('users')
    //         ->select('bot_id')
    //         ->where('id', Auth::id())
    //         ->first();
            
    //      $current_bot_id = $getBotId && $getBotId->bot_id ? $getBotId->bot_id : "NULL";
    //     // Save the collected messages and responses
    //     foreach ($userMessages as $index => $userMessage) {
    //         $response = isset($botResponses[$index]) ? $botResponses[$index]['text'] : null; // Get the corresponding bot response or null
    //         $responseImagePath = isset($botResponses[$index]) ? $botResponses[$index]['image'] : null; // Get bot image path or null
    
    //         // Save user and bot messages in a single record
    //         ChatHistory::create([
    //             'user_id' => $user->id,
    //             'chat_id' => $chatId,
    //             'user_message' => $userMessage['text'], // Save user message
    //             'response' => $response, // Save bot response or null if no response exists
    //             'image_path' => $userMessage['image'] ?? $responseImagePath, // Save the image path for user or bot
    //             'selected_bot_id'=>$current_bot_id,
    //         ]);
    //     }
    
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Chat history saved successfully',
    //         'chat_id' => $chatId
    //     ], 201);
    // }
    
    public function saveChatHistory(Request $request)
    {
        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.text' => 'nullable|string',
            'messages.*.user._id' => 'required|integer',
            'messages.*.image' => 'nullable|string',
            'messages.*.botId' => 'nullable|integer', // Thêm validate cho botId
        ]);
    
        $user = Auth::user();
        $chatId = uniqid();
    
        $userMessages = [];
        $botResponses = [];
    
        foreach ($request->messages as $messageData) {
            $imagePath = null;
            if (isset($messageData['image'])) {
                if (preg_match('/^data:image\/(\w+);base64,/', $messageData['image'], $type)) {
                    $imageData = base64_decode(substr($messageData['image'], strpos($messageData['image'], ',') + 1));
                    $extension = strtolower($type[1]);
                    $fileName = uniqid() . '.' . $extension;
                    $imagePath = 'chat_images/' . $fileName;
                    \Storage::disk('public')->put($imagePath, $imageData);
                }
            }
    
            // Lấy botId từ request, nếu không có thì để null
            $botId = isset($messageData['botId']) ? $messageData['botId'] : null;
    
            if ($messageData['user']['_id'] == 1) {
                $userMessages[] = ['text' => $messageData['text'], 'image' => $imagePath, 'botId' => $botId];
            } elseif ($messageData['user']['_id'] == 2) {
                $botResponses[] = ['text' => $messageData['text'], 'image' => $imagePath, 'botId' => $botId];
            }
        }
    
        foreach ($userMessages as $index => $userMessage) {
            $response = isset($botResponses[$index]) ? $botResponses[$index]['text'] : null;
            $responseImagePath = isset($botResponses[$index]) ? $botResponses[$index]['image'] : null;
            $botId = $userMessage['botId']; // Lấy botId của tin nhắn
    
            ChatHistory::create([
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'user_message' => $userMessage['text'],
                'response' => $response,
                'image_path' => $userMessage['image'] ?? $responseImagePath,
                'selected_bot_id' => $botId, // Lưu botId của tin nhắn
            ]);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Chat history saved successfully',
            'chat_id' => $chatId
        ], 201);
    }


  /*  public function getChatSummaries(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
            $currentBotId = $request->input('current_bot_id'); // Get the bot ID from the request
//echo "<pre>";print_r($currentBotId);exit;
        $summaries = DB::table('user_chat_history')
            ->select('chat_id','selected_bot_id', DB::raw('MAX(created_at) as last_message_time'), DB::raw('GROUP_CONCAT(user_message ORDER BY created_at SEPARATOR " | ") as messages'))
            ->where('user_id', $userId)
            ->groupBy('chat_id','selected_bot_id')
            ->orderBy('last_message_time', 'desc')
            ->get();
    
        $usedTitles = []; // To track used titles
    
        $summaries = $summaries->map(function ($summary) use (&$usedTitles) {
            // Derive the label from messages
            $messages = explode(' | ', $summary->messages);
            $lastMessage = end($messages);
    
            // Generate the title (use first few words of the last message)
            $title = Str::limit($lastMessage, 50, '...');
          
            // Handle duplicate titles
            if (in_array($title, $usedTitles)) {
                $counter = 2;
                $originalTitle = $title;
    
                // Increment counter and append until we find a unique title
                while (in_array($title, $usedTitles)) {
                    $title = $originalTitle . ' (' . $counter . ')';
                    $counter++;
                }
            }
    
            // Store the title to track duplicates
            $usedTitles[] = $title;
             if (empty($title)) {
                // Handle the case of missing or empty titles
                $title = 'No Title Available';
            }
            
            $getBotId = DB::table('users')
            ->select('bot_id')
            ->where('id', Auth::id())
            ->first();
            
            return [
                'bot_id' => $summary->selected_bot_id,
                'chat_id' => $summary->chat_id,
                'last_message_time' => $summary->last_message_time,
                'messages' => $summary->messages,
                'label' => $title, // Unique title
            ];
        });
    
        return response()->json([
            'status' => true,
            'summaries' => $summaries,
        ]);
    }*/
    
    public function getChatSummaries(Request $request)
{
    $userId = Auth::id();
    $currentBotId = $request->input('current_bot_id'); // Get the bot ID from the request

    // Base query
    $query = DB::table('user_chat_history')
        ->select(
            'chat_id',
            'selected_bot_id',
            DB::raw('MAX(created_at) as last_message_time'),
            DB::raw('GROUP_CONCAT(user_message ORDER BY created_at SEPARATOR " | ") as messages')
        )
        ->where('user_id', $userId);

    // Apply bot ID filter if provided
    if (!empty($currentBotId)) {
        $query->where('selected_bot_id', $currentBotId);
    }

    $summaries = $query
        ->groupBy('chat_id', 'selected_bot_id')
        ->orderBy('last_message_time', 'desc')
        ->get();

    $usedTitles = []; // To track used titles

    $summaries = $summaries->map(function ($summary) use (&$usedTitles) {
        // Derive the label from messages
        $messages = explode(' | ', $summary->messages);
        $lastMessage = end($messages);

        // Generate the title (use first few words of the last message)
        $title = Str::limit($lastMessage, 50, '...');

        // Handle duplicate titles
        if (in_array($title, $usedTitles)) {
            $counter = 2;
            $originalTitle = $title;

            // Increment counter and append until we find a unique title
            while (in_array($title, $usedTitles)) {
                $title = $originalTitle . ' (' . $counter . ')';
                $counter++;
            }
        }

        // Store the title to track duplicates
        $usedTitles[] = $title;

        if (empty($title)) {
            // Handle the case of missing or empty titles
            $title = 'No Title Available';
        }

        return [
            'bot_id' => $summary->selected_bot_id,
            'chat_id' => $summary->chat_id,
            'last_message_time' => $summary->last_message_time,
            'messages' => $summary->messages,
            'label' => $title, // Unique title
        ];
    });

    return response()->json([
        'status' => true,
        'summaries' => $summaries,
    ]);
}

  public function getFullConversation($chatId)
    {
        $userId = Auth::id();
    
        // Fetch chat history for the given chat_id
        $chatHistory = ChatHistory::where('chat_id', $chatId)
                                  ->where('user_id', $userId)
                                  ->orderBy('created_at', 'asc')
                                  ->get();
    
        // Prepare conversation response
        $conversation = [];
    
        foreach ($chatHistory as $chat) {
            
            // Push user message
            $conversation[] = [
                'id' => $chat->id,
                '_id' => $chat->id . '-user', // Use original ID with a suffix for user messages
                'createdAt' => Carbon::parse($chat->created_at)->toIso8601String(),
                'text' => $chat->user_message,
                'user' => [
                    '_id' => 1, // Assuming 1 represents "User"
                    'name' => 'User'
                ],
                'image' => $chat->image_path ? asset('storage/' . $chat->image_path) : null, // Add image path if exists
            ];
            
            // Push bot response (if exists)
            if (!empty($chat->response)) {
                $conversation[] = [
                    'id' => $chat->id,
                    '_id' => $chat->id . '-bot', // Use original ID with a suffix for bot messages
                    'createdAt' => Carbon::parse($chat->created_at)->toIso8601String(),
                    'text' => $chat->response,
                    'user' => [
                        '_id' => 2, // Assuming 2 represents "Bot"
                        'name' => 'Bot'
                    ],
                    'image' => $chat->image_path ? asset('storage/' . $chat->image_path) : null, // Add image path if exists
                ];
            }
    
            
        }
    
        return response()->json([
            'status' => true,
            'conversation' => $conversation
        ]);
    }


    
    public function getFullConversationOLD($chatId)
    {
        $userId = Auth::id();
        // Fetch chat history for the given chat_id
        $chatHistory = ChatHistory::where('chat_id', $chatId)
                                  ->where('user_id', $userId)
                                  ->orderBy('created_at', 'asc')
                                  ->get();
    
        // Prepare conversation response
        $conversation = [];
    
        foreach ($chatHistory as $chat) {
             // Push bot response (if exists)
            if (!empty($chat->response)) {
                $conversation[] = [
                    'id' => $chat->id,
                    '_id' => uniqid(),
                    'createdAt' => Carbon::parse($chat->created_at)->toIso8601String(),
                    'text' => $chat->response,
                    'user' => [
                        '_id' => 2, // Assuming 2 represents "Bot"
                        'name' => 'Bot'
                    ],
                    'image' => $chat->image_path ? asset('storage/' . $chat->image_path) : null, // Add image path if exists
                ];
            }
            
            // Push user message
            $conversation[] = [
                'id' => $chat->id,
                '_id' => uniqid(),
                'createdAt' => Carbon::parse($chat->created_at)->toIso8601String(),
                'text' => $chat->user_message,
                'user' => [
                    '_id' => 1, // Assuming 1 represents "User"
                    'name' => 'User'
                ],
                'image' => $chat->image_path ? asset('storage/' . $chat->image_path) : null, // Add image path if exists
            ];
    
           
        }
    
        return response()->json([
            'status' => true,
            'conversation' => $conversation
        ]);
    }
    
    // Search through chat history
    public function searchChatHistory(Request $request)
    {
        $userId = Auth::id();
        $searchTerm = $request->input('search');

        // Search through the chat history for the user
        $results = DB::table('user_chat_history')
            ->where('user_id', $userId)
            ->where(function($query) use ($searchTerm) {
                $query->where('user_message', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('response', 'LIKE', "%{$searchTerm}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($results);
    }
    
    public function updateChatHistory(Request $request, $chatId)
    {
        // Validate the request inputs
        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.text' => 'nullable|string', // Allow empty strings
            'messages.*.user._id' => 'required|integer',
            'messages.*.image' => 'nullable|string', // Accept base64 string
        ]);
    
        $user = Auth::user();
    
        // Fetch existing chat history by chatId
        $existingChatHistories = ChatHistory::where('chat_id', $chatId)->get();
    
        // Arrays to hold user messages and bot responses
        $userMessages = [];
        $botResponses = [];
    
        foreach ($request->messages as $messageData) {
            // Handle image upload if it exists
            $imagePath = null;
            if (isset($messageData['image']) && preg_match('/^data:image\/(\w+);base64,/', $messageData['image'], $type)) {
                $imageData = substr($messageData['image'], strpos($messageData['image'], ',') + 1);
                $imageData = base64_decode($imageData);
                $extension = strtolower($type[1]); // Get image extension
    
                // Generate a unique image name
                $fileName = uniqid() . '.' . $extension;
                $imagePath = 'chat_images/' . $fileName;
    
                // Save the image
                \Storage::disk('public')->put($imagePath, $imageData);
            }
    
            // Separate user and bot messages
            if ($messageData['user']['_id'] == 1) {
                $userMessages[] = ['text' => $messageData['text'], 'image' => $imagePath];
            } elseif ($messageData['user']['_id'] == 2) {
                $botResponses[] = ['text' => $messageData['text'], 'image' => $imagePath];
            }
        }
    
        // Update existing chat histories or create new entries
        foreach ($userMessages as $index => $userMessage) {
            // Check if there is an existing record for this index
            if (isset($existingChatHistories[$index])) {
                // Update existing chat history record
                $existingChatHistories[$index]->update([
                    'user_message' => $userMessage['text'],
                    'response' => $botResponses[$index]['text'] ?? null,
                    'image_path' => $userMessage['image'] ?? ($botResponses[$index]['image'] ?? null), // Update image if provided
                ]);
            } else {
                // If no record exists, create a new one
                ChatHistory::create([
                    'user_id' => $user->id,
                    'chat_id' => $chatId,
                    'user_message' => $userMessage['text'],
                    'response' => $botResponses[$index]['text'] ?? null,
                    'image_path' => $userMessage['image'] ?? ($botResponses[$index]['image'] ?? null),
                ]);
            }
        }
    
        // Handle any remaining bot responses that don't have a matching user message
        if (count($botResponses) > count($userMessages)) {
            for ($i = count($userMessages); $i < count($botResponses); $i++) {
                ChatHistory::create([
                    'user_id' => $user->id,
                    'chat_id' => $chatId,
                    'user_message' => null, // No user message in this case
                    'response' => $botResponses[$i]['text'],
                    'image_path' => $botResponses[$i]['image'] ?? null,
                ]);
            }
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Chat history updated successfully',
            'chat_id' => $chatId
        ], 200);
    }


}