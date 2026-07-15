<?php

namespace App\Providers;

use App\Domain\Event\EventDispatcher;
use App\Domain\Event\SignalTransitionFailed;
use App\Domain\Event\SignalTransitionSucceeded;
use App\Infra\Audit\StateTransitionListener;
use WPINT\Core\Foundation\ServiceProvider;

class SignalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EventDispatcher::class, function () {
            return new EventDispatcher();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $dispatcher = app(EventDispatcher::class);
        $dispatcher->listen(SignalTransitionFailed::class, new StateTransitionListener());
        $dispatcher->listen(SignalTransitionSucceeded::class, new StateTransitionListener());
    }
}
