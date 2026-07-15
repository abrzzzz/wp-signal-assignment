<?php

namespace App\Providers;

use App\Domain\Event\EventDispatcher;
use App\Domain\Event\SignalTransitionFailed;
use App\Domain\Event\SignalTransitionSucceeded;
use App\Infra\Audit\StateTransitionListener;
use App\Infra\Repository\SignalRepository;
use App\Infra\WP\CronManager;
use App\Infra\WP\PostMetaManager;
use App\Infra\WP\PostTypeManager;
use App\Infra\WP\RoleManager;
use WPINT\Core\Foundation\ServiceProvider;
use Wpint\WPAPI\Hook\Enum\HookTypeEnum;
use Wpint\WPAPI\WPAPI;

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
        // Add post type
        $postTypeManager = new PostTypeManager();
        $postTypeManager->register();
        // Add Meta
        $metaManager = new PostMetaManager();
        $metaManager->register();
        // Add signal publiser role
        $roleManager = new RoleManager();
        $roleManager->register();

        // Register Corn
        CronManager::register();
        CronManager::activate();

        /* remove_role('signal_publisher'); */
        $dispatcher = app(EventDispatcher::class);
        $dispatcher->listen(SignalTransitionFailed::class, new StateTransitionListener());
        $dispatcher->listen(SignalTransitionSucceeded::class, new StateTransitionListener());

        WPAPI::hook()
            ->name('map_meta_cap')
            ->type(HookTypeEnum::FILTER)
            ->acceptedArgs(4)
            ->callback(function ($caps, $cap, $user_id, $args) {

                if ($cap !== 'edit_post') {
                    return $caps;
                }

                if (! $args) {
                    return;
                }

                $user = get_user($user_id);

                if (in_array('administrator', $user->roles)) {
                    return;
                }

                $post = get_post($args[0]);

                if (
                    $post
                    && $post->post_type == 'signal'
                    && $post->post_status == 'publish'
                ) {
                    return [
                        'do_not_allow',
                    ];
                }
            })
            ->register();

        WPAPI::hook()
            ->name('map_meta_cap')
            ->type(HookTypeEnum::FILTER)
            ->acceptedArgs(4)
            ->callback(function ($caps, $cap, $user_id, $args) {
                if ($cap !== 'publish_signals') {
                    return $caps;
                }

                $signalRepository = app(SignalRepository::class);
                $exists = $signalRepository->checkActiveByUserId($user_id);
                if ($exists) {
                    return [
                        'do_not_allow',
                    ];
                }
            })
            ->register();

        WPAPI::hook()
            ->name('map_meta_cap')
            ->type(HookTypeEnum::FILTER)
            ->acceptedArgs(4)
            ->callback(function ($caps, $cap, $user_id, $args) {
                if ($cap !== 'delete_post') {
                    return $caps;
                }

                if (! $args) {
                    return;
                }

                $user = get_user($user_id);
                if (in_array('administrator', $user->roles)) {
                    return;
                }

                $post = get_post($args[0]);

                if (
                    $post
                    && $post->post_type == 'signal'
                    && $post->post_status == 'publish'
                ) {
                    return [
                        'do_not_allow',
                    ];
                }

            })
            ->register();
    }
}
