<?php

namespace App\Providers;

use WPINT\Core\Foundation\ServiceProvider;

class WPServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register your service
    }

    /**
     * Bootstrap any application service
     */
    public function boot(): void
    {

        add_filter('cron_schedules', function ($schedules) {
            $schedules['five_seconds'] = [
                'interval' => 5,
                'display' => __('Every 5 Seconds'),
            ];

            return $schedules;
        });

    }
}
