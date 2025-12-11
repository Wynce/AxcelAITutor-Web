<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatBot;
use Validator;
use Session;

class ChatBotController extends Controller
{
    /**
     * List chatbots
     */
    public function index()
    {
        $data = [];
        $data['pageTitle'] = "Chatbots";
        $data['module_name'] = "Chatbots";
        $data['current_module_name'] = "Chatbots";
        $data['module_url'] = route('admin.chatbots.index');
        $data['bots'] = ChatBot::orderBy('created_at', 'desc')->get();

        return view('Admin/ChatBots/index', $data);
    }

    /**
     * Create form
     */
    public function create()
    {
        $data = [];
        $data['pageTitle'] = "Add Chatbot";
        $data['module_name'] = "Chatbots";
        $data['current_module_name'] = "Add Chatbot";
        $data['module_url'] = route('admin.chatbots.index');
        $data['bot'] = null;

        return view('Admin/ChatBots/create', $data);
    }

    /**
     * Store chatbot
     */
    public function store(Request $request)
    {
        $rules = [
            'bot_name' => 'required|string|max:255',
            'base_bot' => 'required|string|max:255',
            'prompt' => 'required|string',
            'greeting_message' => 'nullable|string',
            'bot_bio' => 'nullable|string',
            'public_access' => 'nullable|boolean',
            'bot_recommendations' => 'nullable|string',
            'show_prompt' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'bot_name',
            'bot_type',
            'base_bot',
            'prompt',
            'knowledge_base',
            'greeting_message',
            'bot_bio',
            'public_access',
            'bot_recommendations',
            'show_prompt',
            'bot_profile',
            'is_default',
        ]);

        // Cast boolean checkboxes
        $data['public_access'] = $request->boolean('public_access');
        $data['show_prompt'] = $request->boolean('show_prompt');
        $data['is_default'] = $request->boolean('is_default');

        $bot = ChatBot::create($data);

        Session::flash('success', 'Chatbot created successfully.');
        return redirect()->route('admin.chatbots.index');
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $id = base64_decode($id);
        $bot = ChatBot::findOrFail($id);

        $data = [];
        $data['pageTitle'] = "Edit Chatbot";
        $data['module_name'] = "Chatbots";
        $data['current_module_name'] = "Edit Chatbot";
        $data['module_url'] = route('admin.chatbots.index');
        $data['bot'] = $bot;

        return view('Admin/ChatBots/edit', $data);
    }

    /**
     * Update chatbot
     */
    public function update(Request $request, $id)
    {
        $id = base64_decode($id);
        $bot = ChatBot::findOrFail($id);

        $rules = [
            'bot_name' => 'required|string|max:255',
            'base_bot' => 'required|string|max:255',
            'prompt' => 'required|string',
            'greeting_message' => 'nullable|string',
            'bot_bio' => 'nullable|string',
            'public_access' => 'nullable|boolean',
            'bot_recommendations' => 'nullable|string',
            'show_prompt' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'bot_name',
            'bot_type',
            'base_bot',
            'prompt',
            'knowledge_base',
            'greeting_message',
            'bot_bio',
            'public_access',
            'bot_recommendations',
            'show_prompt',
            'bot_profile',
            'is_default',
        ]);

        $data['public_access'] = $request->boolean('public_access');
        $data['show_prompt'] = $request->boolean('show_prompt');
        $data['is_default'] = $request->boolean('is_default');

        $bot->update($data);

        Session::flash('success', 'Chatbot updated successfully.');
        return redirect()->route('admin.chatbots.index');
    }

    /**
     * Delete chatbot
     */
    public function delete($id)
    {
        $id = base64_decode($id);
        $bot = ChatBot::findOrFail($id);
        $bot->delete();

        Session::flash('success', 'Chatbot deleted successfully.');
        return redirect()->back();
    }
}

