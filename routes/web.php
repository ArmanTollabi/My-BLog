<?php

use App\Http\Controllers\Admin\AdminCommentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\PostSaveController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoController;

Route::get('robots.txt', [SeoController::class, 'robots']);
Route::get('sitemap.xml', [SeoController::class, 'sitemap']);

/*
|--------------------------------------------------------------------------
| صفحات عمومی
|--------------------------------------------------------------------------
*/

//خانه
Route::get('/', [PostController::class, 'home'])->name('home');

//سرچ
Route::get('posts/search', [PostController::class, 'search'])->name('posts.search')->middleware('throttle:40,1');

/*
|--------------------------------------------------------------------------
| مهمان (guest)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->prefix('auth')->group(function () {
    Route::get('register', [RegisterController::class, 'ShowRegisterForm'])->name('register');
    Route::post('register', [RegisterController::class, 'Register'])->middleware('throttle:3,30')->name('register');

    Route::get('login', [LoginController::class, 'ShowLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'Login'])->name('login');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:3,30')->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:3,30')->name('password.update');
});

/*
|--------------------------------------------------------------------------
| کاربر لاگین‌کرده (auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('logout', [LoginController::class, 'Logout'])->name('logout');

    // ── پست‌ها (create/store/edit/update/destroy) ──
    // نکته: باید قبل از index/show عمومی ثبت بشه تا posts/create با posts/{post} قاطی نشه.
    Route::resource('posts', PostController::class)->except(['index', 'show']);

    Route::prefix('posts')->name('posts.')->group(function () {
        Route::post('upload-image', [PostController::class, 'uploadImage'])->name('upload-image');
        Route::post('{post}/save', [PostSaveController::class, 'toggle'])->middleware('throttle:20,1')->name('save');
        Route::post('{post}/like', [PostLikeController::class, 'toggle'])->name('like');
    });

    // ── کامنت‌ها ──
    Route::post('posts/{post}/comments', [CommentController::class, 'store'])->middleware('throttle:5,1')->name('comments.store');
    Route::put('comments/{comment}', [CommentController::class, 'update'])->middleware('throttle:5,1')->name('comments.update');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('comments/{comment}/reply', [CommentController::class, 'reply'])->middleware('throttle:5,1')->name('comments.reply');

    // ── نوتیفیکیشن‌ها ──
    Route::get('notifications/{notification}', [NotificationController::class, 'read'])->name('notifications.read');

    // ── دسته‌بندی‌ها (عملیات مدیریتی) ──
    Route::resource('categories', CategoryController::class)->only(['create', 'store', 'destroy']);

    // ── تگ‌ها ──
    Route::resource('tags', TagController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // ── داشبورد کاربر ──
    Route::prefix('dashboard')->name('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('comments', [DashboardController::class, 'comments'])->name('.comments');
        Route::get('posts', [DashboardController::class, 'posts'])->name('.posts');
        Route::get('bookmarks', [DashboardController::class, 'bookmarks'])->name('.bookmarks');

        Route::post('avatar', [DashboardController::class, 'updateAvatar'])->name('.avatar');
        Route::delete('avatar', [DashboardController::class, 'destroyAvatar'])->name('.avatar.destroy');

        Route::put('profile', [DashboardController::class, 'updateProfile'])->name('.profile');
        Route::put('password', [DashboardController::class, 'updatePassword'])->name('.password');
    });
});

/*
|--------------------------------------------------------------------------
| پست‌ها و دسته‌بندی‌ها (عمومی)
| باید بعد از گروه auth بیان تا create با {post} / {category} قاطی نشه.
|--------------------------------------------------------------------------
*/

Route::resource('posts', PostController::class)->only(['index', 'show']);
Route::resource('categories', CategoryController::class)->only(['index', 'show']);

/*
|--------------------------------------------------------------------------
| پنل ادمین (فقط role === 'admin')
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', EnsureAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('posts', [AdminPostController::class, 'index'])->name('posts.index');

        // ── کاربران ──
        Route::post('users/upload-avatar', [UserController::class, 'uploadAvatar'])->name('users.upload-avatar');
        Route::resource('users', UserController::class);
        // ── کامنتها ──
        Route::prefix('comments')->name('comments.')->controller(AdminCommentController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('trash', 'trash')->name('trash');
            Route::patch('{comment}/approve', 'approve')->name('approve');
            Route::patch('{comment}/unapprove', 'unapprove')->name('unapprove');
            Route::delete('{comment}', 'destroy')->name('destroy');
            Route::patch('{comment}/restore', 'restore')->name('restore')->withTrashed();
            Route::delete('comments/trash/empty', [AdminCommentController::class, 'emptyTrash'])->name('trash.empty');
            Route::delete('{comment}/force', 'forceDelete')->name('force')->withTrashed();
           // Route::delete('{comments}/forceAll', 'destroyAll')->name('forceAll')->withTrashed();
        });
    });
