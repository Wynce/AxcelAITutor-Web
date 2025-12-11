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
        Route::get('/view/{id}', [App\Http\Controllers\Admin\UsersController::class, 'view'])->name('admin.users.view');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\UsersController::class, 'create'])->name('adminUserEdit');
        Route::post('/store', [App\Http\Controllers\Admin\UsersController::class, 'store'])->name('adminUserStore');
        Route::get('/delete/{id}', [App\Http\Controllers\Admin\UsersController::class, 'delete'])->name('adminUserDelete');
        Route::get('/changeStatus/{id}/{status}', [App\Http\Controllers\Admin\UsersController::class, 'changeStatus'])->name('adminUserChangeStatus');
        Route::post('/bulk-action', [App\Http\Controllers\Admin\UsersController::class, 'bulkAction'])->name('admin.users.bulkAction');
        Route::get('/export', [App\Http\Controllers\Admin\UsersController::class, 'export'])->name('admin.users.export');
        Route::get('/get-users', [App\Http\Controllers\Admin\UsersController::class, 'getUsers'])->name('adminGetUsers');
    });

    // ✅ Admin Management Routes
    Route::prefix('admins')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminsController::class, 'index'])->name('admin.admins.index');
        Route::any('/getRecords', [App\Http\Controllers\Admin\AdminsController::class, 'getRecords'])->name('admin.admins.getRecords');
        Route::get('/create', [App\Http\Controllers\Admin\AdminsController::class, 'create'])->name('admin.admins.create');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\AdminsController::class, 'create'])->name('admin.admins.edit');
        Route::post('/store', [App\Http\Controllers\Admin\AdminsController::class, 'store'])->name('admin.admins.store');
        Route::get('/delete/{id}', [App\Http\Controllers\Admin\AdminsController::class, 'delete'])->name('admin.admins.delete');
        Route::get('/changeStatus/{id}/{status}', [App\Http\Controllers\Admin\AdminsController::class, 'changeStatus'])->name('admin.admins.changeStatus');
    });

    // ✅ Roles Routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\RolesController::class, 'index'])->name('admin.roles.index');
        Route::get('/create', [App\Http\Controllers\Admin\RolesController::class, 'create'])->name('admin.roles.create');
        Route::post('/store', [App\Http\Controllers\Admin\RolesController::class, 'store'])->name('admin.roles.store');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\RolesController::class, 'edit'])->name('admin.roles.edit');
        Route::post('/update/{id}', [App\Http\Controllers\Admin\RolesController::class, 'update'])->name('admin.roles.update');
        Route::get('/delete/{id}', [App\Http\Controllers\Admin\RolesController::class, 'delete'])->name('admin.roles.delete');
    });

    // ✅ Activity Logs Routes
    Route::get('/activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.activity-logs.index');

    // ✅ Chat History Routes
    Route::get('/chat-history', [App\Http\Controllers\Admin\ChatHistoryController::class, 'index'])->name('admin.chat-history.index');
    Route::get('/chat-history/{chatId}', [App\Http\Controllers\Admin\ChatHistoryController::class, 'show'])->name('admin.chat-history.show');

    // ✅ Chatbots Routes
    Route::prefix('chatbots')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ChatBotController::class, 'index'])->name('admin.chatbots.index');
        Route::get('/create', [App\Http\Controllers\Admin\ChatBotController::class, 'create'])->name('admin.chatbots.create');
        Route::post('/store', [App\Http\Controllers\Admin\ChatBotController::class, 'store'])->name('admin.chatbots.store');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\ChatBotController::class, 'edit'])->name('admin.chatbots.edit');
        Route::post('/update/{id}', [App\Http\Controllers\Admin\ChatBotController::class, 'update'])->name('admin.chatbots.update');
        Route::get('/delete/{id}', [App\Http\Controllers\Admin\ChatBotController::class, 'delete'])->name('admin.chatbots.delete');
    });

    // ✅ Simulators Routes
    Route::resource('simulators', App\Http\Controllers\Admin\SimulatorController::class)->names('admin.simulators');
});