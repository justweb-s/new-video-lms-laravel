<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Http\View\Composers\SeoComposer;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('partials.seo', SeoComposer::class);
    }
}
