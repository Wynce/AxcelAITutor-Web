<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OpenAIController extends Controller
{
    public function processImageAndChat(Request $request)
    {
        // 1. Retrieve the base64 image from the request
        $base64Image = $request->input('file');
        
        if (!$base64Image) {
            return response()->json(['error' => 'No image uploaded'], 400);
        }

        // 2. Optionally, you can save or process the image here
        // For example, extract text using an OCR tool (optional, based on your needs)

        // 3. Now, we'll create a prompt to send to ChatGPT
        $prompt = "I just uploaded an image. Can you provide a description of typical things I might expect in such an image?";

        // 4. Make a request to the OpenAI Chat Completion API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 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',
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ]
            ]
        ]);

        // 5. Return the response from ChatGPT to the frontend
        return response()->json($response->json());
    }
}