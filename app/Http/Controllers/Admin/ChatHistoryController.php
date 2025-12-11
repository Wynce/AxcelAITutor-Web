<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatHistory;
use App\Models\User;
use App\Models\ChatBot;
use Carbon\Carbon;

class ChatHistoryController extends Controller
{
    /**
     * List chat history with filters
     */
    public function index(Request $request)
    {
        $query = ChatHistory::with(['user'])->where('is_deleted', 0);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('bot_id')) {
            $query->where('selected_bot_id', $request->input('bot_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $chats = $query->latest()->paginate(20)->appends($request->all());

        $data = [];
        $data['pageTitle'] = "Chat History";
        $data['module_name'] = "Chat History";
        $data['current_module_name'] = "Chat History";
        $data['module_url'] = route('admin.chat-history.index');
        $data['chats'] = $chats;
        $data['users'] = User::select('id', 'first_name', 'last_name', 'email')->where('is_deleted', 0)->orderBy('first_name')->get();
        $data['bots'] = ChatBot::select('id', 'bot_name')->get();

        return view('Admin/ChatHistory/index', $data);
    }

    /**
     * View a single chat thread (by chat_id)
     */
    public function show($chatId)
    {
        $messages = ChatHistory::with('user')
            ->where('chat_id', $chatId)
            ->where('is_deleted', 0)
            ->orderBy('created_at')
            ->get();

        abort_unless($messages->count(), 404);

        $user = $messages->first()->user;

        $data = [];
        $data['pageTitle'] = "Chat #{$chatId}";
        $data['module_name'] = "Chat History";
        $data['current_module_name'] = "Chat #{$chatId}";
        $data['module_url'] = route('admin.chat-history.index');
        $data['messages'] = $messages;
        $data['user'] = $user;

        return view('Admin/ChatHistory/show', $data);
    }
}

