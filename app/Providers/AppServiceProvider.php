<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- 1. Tambahkan baris ini

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
// Pastikan blok ini ada!
    if($this->app->environment('production') || !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
    }
}