<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\NotificationController;

// use App\Http\Controllers\Admin\NotificationController;


Route::get('/', function () {
    return view('welcome');
});

// Clear cache routes
Route::get('/clear-route-cache', function() {
    Artisan::call('route:clear');
    return 'Route cache cleared successfully!';
});

Route::get('/clear-config-cache', function () {
    Artisan::call('config:cache');
    return response()->json(['message' => 'Configuration cache cleared successfully.']);
});

Route::get('/create-symlink', function () {
    Artisan::call('storage:link');
    return "Symlink created successfully!";
});

// Admin Authentication Routes
Route::get('/admin', [App\Http\Controllers\Admin\AuthController::class, 'index'])->name('adminLogin');
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('adminDoLogin');

// Admin Routes with Middleware
Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin']], function () {
    
    Route::any('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('adminLogout');
    Route::any('/dashboard', [App\Http\Controllers\Admin\AuthController::class, 'dashboard'])->name('adminDashboard');
    Route::get('/change-password', [App\Http\Controllers\Admin\AuthController::class, 'changePassword'])->name('adminChangePassword');
    Route::post('/change-password-store', [App\Http\Controllers\Admin\AuthController::class, 'changePasswordStore'])->name('adminChangePasswordStore');
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('adminSettings');
    Route::post('/save-settings', [App\Http\Controllers\Admin\SettingsController::class, 'updateSettings'])->name('adminSaveSettings');

    // ✅ Notifications Routes (Fixed)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/create', [NotificationController::class, 'create'])->name('admin.notifications.create');
    Route::post('/notifications/store', [NotificationController::class, 'store'])->name('admin.notifications.store');
    Route::post('/admin/notifications/save',[NotificationController::class,'store'])->name('adminSaveNotification');
    Route::post('/notifications/test',[NotificationController::class,'sendFCMToTopic'])->name('sendFCMToTopic');
    

    // ✅ Users Routes
    Route::prefix('users')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('adminUsers');
        Route::any('/getRecords', [App\Http\Controllers\Admin\UsersController::class, 'getRecords'])->name('adminUserGetRecords');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\UsersController::class, 'create'])->name('adminUserEdit');
        Route::post('/store', [App\Http\Controllers\Admin\UsersController::class, 'store'])->name('adminUserStore');
        Route::get('/delete/{id}', [App\Http\Controllers\Admin\UsersController::class, 'delete'])->name('adminUserDelete');
        Route::get('/changeStatus/{id}/{status}', [App\Http\Controllers\Admin\UsersController::class, 'changeStatus'])->name('adminUserChangeStatus');
        Route::get('/get-users', [App\Http\Controllers\Admin\UsersController::class, 'getUsers'])->name('adminGetUsers');
    });
});