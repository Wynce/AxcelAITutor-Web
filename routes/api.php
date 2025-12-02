<?php
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatHistoryController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ChatBotController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\OpenAIController;


use App\Http\Controllers\Admin\NotificationController;

// use App\Http\Controllers\Admin\NotificationController;

Route::post('login', [AuthController::class, 'login']);
// Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::post('register', [AuthController::class, 'register']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::any('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');

Route::get('/password/reset/response', [ForgotPasswordController::class, 'showPasswordResetResponse'])->name('password.reset.response');
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');
  
Route::post('/email/resend-verification', [AuthController::class, 'resendVerificationLink']);
  
Route::get('/settings', [SettingsController::class, 'getSettings']);
Route::post('/settings/update', [SettingsController::class, 'updateSettings']);

/*
Route::post('/create-bot', [ChatBotController::class, 'create']);  // Create new bot
Route::put('/update/{id}', [ChatBotController::class, 'update']);  // Update bot
Route::get('/chatbot/{id}', [ChatBotController::class, 'show']);  // Get specific bot data
Route::get('/chatbots', [ChatBotController::class, 'index']);  // Get list of all bots*/


//notification 


Route::get('/notifications', [NotificationController::class, 'getNotifications']);



// Route::get('/notifications', [NotificationController::class, 'getNotifications']);


    Route::get('/chatbot/{id}', [ChatBotController::class, 'show']);  // Get specific bot data

// Admin Middleware
// Route::middleware(['auth:api'])->group(function () {
//     Route::post('/create-bot', [ChatBotController::class, 'create']);  // Create new bot
//     Route::post('/update/{id}', [ChatBotController::class, 'update']);  // Update bot
//     Route::get('/chatbot/{id}', [ChatBotController::class, 'show']);  // Get specific bot data
//     Route::get('/chatbots', [ChatBotController::class, 'index']);  // Get list of all bots
//     Route::delete('/bots/{id}', [ChatBotController::class, 'destroy']);
// });
Route::middleware('is_admin')->get('/admin-test', function () {
    return response()->json(['message' => 'Admin access granted']);
});

    Route::post('/upload', [OpenAIController::class, 'processImageAndChat']);
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::delete('delete-account', [UserController::class, 'deleteAccount']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('profile-update', [UserController::class, 'updateProfile']);
    Route::put('change-password', [UserController::class, 'changePassword']);
    // Save chat history
    Route::post('save-chat-history', [ChatHistoryController::class, 'saveChatHistory']);
    // Get all chat history summaries for sidebar display
    Route::get('chat-summaries', [ChatHistoryController::class, 'getChatSummaries']);
    // Get full conversation for a specific chat (based on chat ID)
    Route::get('full-conversation/{chatId}', [ChatHistoryController::class, 'getFullConversation']);
    Route::any('/chat-history/{chat_id}', [ChatHistoryController::class, 'updateChatHistory']);
    // Search chat history
    
    Route::get('search-chat-history', [ChatHistoryController::class, 'searchChatHistory']);
    Route::post('/save-bot', [UserController::class, 'saveBot']);
    Route::get('/get-bot', [UserController::class, 'getBot']);
    Route::post('/upload-file', [UserController::class, 'uploadFile']);
    Route::post('/upload-file-pdf', [UserController::class, 'uploadFileNew']);
    
    
    
    Route::post('/create-bot', [ChatBotController::class, 'create']);  // Create new bot
    Route::post('/update-bot/{id}', [ChatBotController::class, 'update']);  // Update bot
    Route::post('/update/{id}', [ChatBotController::class, 'update']);  // Update bot
    Route::get('/chatbot/{id}', [ChatBotController::class, 'show']);  // Get specific bot data
    Route::get('/chatbots', [ChatBotController::class, 'index']);  // Get list of all bots
    Route::delete('/bots/{id}', [ChatBotController::class, 'destroy']);
});
 