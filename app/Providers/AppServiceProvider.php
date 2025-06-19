<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (env('APP_ENV') === 'production') {
        URL::forceScheme('https');
    }

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => Blade::render("@vite('resources/js/app.js')")
        );
        //
        Gate::define('viewApiDocs', function (User $user) {
            return true;
        });
        // Gate::policy()
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('discord', \SocialiteProviders\Google\Provider::class);
        });
    }
}
