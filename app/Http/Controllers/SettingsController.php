<?php
namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{

	public function getSettings(Request $request)
    {
        // Retrieve the settings (assuming there is only one settings record)
        $settings = Settings::first();

        if (!$settings) {
            return response()->json([
                'message' => 'Settings not found',
            ], 404);
        }

        return response()->json($settings, 200);
    }
    
    public function updateSettings(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4|max:20|unique:settings,name,1',
            'base_bot' => 'required|string',
            'prompt' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Optional logo field
            'knowledge_base' => 'nullable|file|mimes:pdf|max:2048', // Optional PDF field
            'greeting_message' => 'nullable|string',
            'bio' => 'nullable|string',
            'suggest_replies' => 'nullable|boolean',
            'public_access' => 'nullable|boolean',
        ]);
    
        // return $request->all();
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Save or update the settings
        // return $request->all();
        $setting = Settings::firstOrNew(['id' => 1]); // Assuming you're updating a single settings row
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete the old logo if it exists
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
    
        // Save other fields
        $setting->name = $request->name;
        $setting->base_bot = $request->base_bot;
        $setting->prompt = $request->prompt;
        $setting->greeting_message = $request->greeting_message;
        $setting->bio = $request->bio;
        $setting->suggest_replies = $request->suggest_replies;
        $setting->public_access = $request->public_access;
    
        $setting->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully!',
            'data' => $setting
        ]);
    }

}