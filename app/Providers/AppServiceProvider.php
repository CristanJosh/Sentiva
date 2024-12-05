<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('dashboard', function ($view) {
            $user = auth()->user();

            if ($user) { // Check if the user is logged in
                $view->with([
                    'totalSentiments' => $user->sentiments()->count(),
                    'positiveCount' => $user->sentiments()->where('grade', 'Positive')->count(),
                    'neutralCount' => $user->sentiments()->where('grade', 'Neutral')->count(),
                    'negativeCount' => $user->sentiments()->where('grade', 'Negative')->count(),
                ]);
            } else {
                // Default values if no user is logged in
                $view->with([
                    'totalSentiments' => 0,
                    'positiveCount' => 0,
                    'neutralCount' => 0,
                    'negativeCount' => 0,
                ]);
            }
        });
    }
}
