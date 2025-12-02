<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;

/* Uses */
use Auth;
use Session;
use Validator;
use File;

class SettingsController extends Controller
{
    function __construct() {
    }

    /**
     * Function for Show settings
     */
    public function index() {
        $data = [];
        $data['settings'] = Settings::first();
        $data['pageTitle'] = "Bot Settings";
        $data['module_name'] = "Admin Settings";
        $data['current_module_name'] = '';
        $data['module_url'] = route('adminSettings');
        return view('Admin/settings', $data);
    }

    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4|max:20|unique:settings,name,1',
            'base_bot' => 'required|string',
            'prompt' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'knowledge_base' => 'nullable|file|mimes:pdf|max:2048',
            'greeting_message' => 'nullable|string',
            'bio' => 'nullable|string',
            'suggest_replies' => 'nullable|boolean',
            'attachment' => 'nullable|boolean',
            'voice' => 'nullable|boolean',
            'attachment_file_size' => 'nullable',
            'public_access' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $setting = Settings::firstOrNew(['id' => 1]);

        if ($request->hasFile('logo')) {
            // if ($setting->logo) {
            //     Storage::disk('public')->delete($setting->logo);
            // }

            if ($setting->logo) {
                // Storage::disk('public')->delete($setting->logo);
                unlink(public_path('assets/'.$setting->logo));
            }

            $logoFile = $request->file('logo');
            $logoFileName = 'logo_' . now()->format('YmdHis') . '.' . $logoFile->getClientOriginalExtension(); // Generate new file name
            // $logoPath = $logoFile->storeAs('settings/logo', $logoFileName, 'public');
            $logoFile->move(public_path('assets/settings/logo'), $logoFileName);
            $setting->logo = 'settings/logo/'.$logoFileName; // Save the path to the database
        }

        // Handle knowledge base PDF upload
        if ($request->hasFile('knowledge_base')) {
            // Delete the old knowledge base PDF if it exists
            if ($setting->knowledge_base) {
                // Storage::disk('public')->delete($setting->knowledge_base);
                unlink(public_path('assets/'.$setting->knowledge_base));
            }
    
            $pdfFile = $request->file('knowledge_base');
            $pdfFileName = 'knowledge_base_' . now()->format('YmdHis') . '.' . $pdfFile->getClientOriginalExtension(); // Generate new file name
            // $pdfPath = $pdfFile->storeAs('settings/knowledge_base', $pdfFileName, 'public');
            $pdfFile->move(public_path('assets/settings/knowledge_base'), $pdfFileName);
            $setting->knowledge_base = 'settings/knowledge_base/'.$pdfFileName; // Save the path to the database
        }

        $setting->name = $request->name;
        $setting->base_bot = $request->base_bot;
        $setting->prompt = $request->prompt;
        $setting->greeting_message = $request->greeting_message;
        $setting->bio = $request->bio;
        $setting->suggest_replies = $request->suggest_replies;
        $setting->attachment = $request->attachment;
        $setting->voice = $request->voice;
        $setting->attachment_file_size = $request->attachment_file_size;
        $setting->public_access = $request->public_access;
        $setting->save();

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Function for Sending Admin Notifications
     */
    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:5|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $adminUsers = User::where('is_admin', 1)->get();
        Notification::send($adminUsers, new AdminNotification($request->message));

        return response()->json(['success' => 'Notification sent successfully!']);
    }
}