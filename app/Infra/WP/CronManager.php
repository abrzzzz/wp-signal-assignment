<?php

namespace App\Infra\WP;

use App\Usecase\Write\SignalProccessUsecase;

class CronManager
{
    public const EVENT_NAME = 'signal_evaluation_event';

    public static function register(): void
    {
        add_action(self::EVENT_NAME, [self::class, 'handle']);
    }

    public static function handle(): void
    {
        // running the usecase
        error_log('[Signal Plugin][info] Running every 5 minutes for proccessing the price ');
        $usecase = new SignalProccessUsecase();
        $usecase->execute();
    }

    public static function activate(): void
    {

        if (! wp_next_scheduled(self::EVENT_NAME)) {
            wp_schedule_event(
                time(),
                'five_seconds',
                self::EVENT_NAME,
            );
        }

    }

    public static function deactivate(): void
    {
        wp_clear_scheduled_hook(self::EVENT_NAME);
    }
}
