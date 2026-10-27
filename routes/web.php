<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminCommentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Middleware\EnsureAdmin;
use function Pest\Laravel\put;

Route::get('/', [PostController::class, 'home'])->name('home');

Route::get('/blog', function () {
    return view('blog');
})->name('blog');

//posts
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');



Route::resource('posts', PostController::class)->only(['index', 'show']);

Route::middleware('auth')->group(function () { //users & posts auth
    Route::resource('posts', PostController::class)->except(['index', 'show']);
    Route::resource('users', UserController::class);
    //notifs
    Route::get('/notifications/{notification}', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::middleware('auth')->group(function () { //upload
    Route::post('/posts/upload-image', [PostController::class, 'uploadImage'])->name('posts.upload-image');
    Route::post('/users/upload-avatar', [UserController::class, 'uploadAvatar'])->name('users.upload-avatar');
});

Route::middleware('auth')->group(function () { //comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    //reply
    Route::post('comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
});

// ───────── پنل ادمین (فقط role === 'admin') ─────────
Route::middleware(['auth', EnsureAdmin::class])->prefix('admin')->name('admin.')->group(function () {

    // آمار
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // پست‌ها
    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');

    // کامنت‌ها
    Route::prefix('comments')->name('comments.')->controller(AdminCommentController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/trash', 'trash')->name('trash');
        Route::patch('/{comment}/approve', 'approve')->name('approve');
        Route::patch('/{comment}/unapprove', 'unapprove')->name('unapprove');
        Route::delete('/{comment}', 'destroy')->name('destroy');
        Route::patch('/{comment}/restore', 'restore')->name('restore')->withTrashed();
        Route::delete('/{comment}/force', 'forceDelete')->name('force')->withTrashed();
    });
});


Route::middleware('auth')->group(function () { //categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/categories/',[CategoryController::class, 'store'])->name('categories.store');
    Route::delete('category/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

Route::middleware('auth')->group(function () { //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile');
    Route::post('/dashboard/avatar', [DashboardController::class, 'updateAvatar'])->name('dashboard.avatar');
    Route::delete('/dashboard/avatar', [DashboardController::class, 'destroyAvatar'])->name('dashboard.avatar.destroy');
    Route::put('/dashboard/password', [DashboardController::class, 'updatePassword'])->name('dashboard.password');
});

Route::middleware('auth')->group(function () { //tags
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create');
    Route::get('tags/{tag}', [TagController::class, 'show'])->name('tags.show');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('auth/register',[RegisterController::class,'ShowRegisterForm'])->name('register');
    Route::post('auth/register',[RegisterController::class,'Register'])->name('register');

    Route::get('auth/login',[LoginController::class,'ShowLoginForm'])->name('login');
    Route::post('auth/login',[LoginController::class,'Login'])->name('login');

    Route::get('auth/forgot-password',[PasswordResetLinkController::class,'create'])->name('password.request');
    Route::post('auth/forgot-password',[PasswordResetLinkController::class,'store'])->name('password.email');

    Route::get('auth/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('auth/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout',[LoginController::class,'Logout'])->name('logout');
});
