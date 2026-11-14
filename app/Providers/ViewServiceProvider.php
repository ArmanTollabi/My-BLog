<?php

namespace App\Providers;

use App\View\Composers\AdminSidebarComposer;
use App\View\Composers\HeaderComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // هر بار که این ویوها رندر شوند، Composer داده‌شان را آماده می‌کند
        View::composer('layouts.header', HeaderComposer::class);
        View::composer('admin._sidebar', AdminSidebarComposer::class);
    }
}
