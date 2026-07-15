<?php

namespace App\Infra\WP;

class RoleManager
{
    public const ROLE_NAME = 'signal_publisher';

    public function register()
    {
        add_role(
            self::ROLE_NAME,
            'Signal Publisher',
            [
                'read' => true,

                'edit_signal' => true,
                'read_signal' => true,
                'delete_signal' => true,

                'edit_signals' => true,
                'edit_others_signals' => true,
                'publish_signals' => true,
                'read_private_signals' => true,

                'delete_signals' => true,
                'delete_private_signals' => true,
                'delete_published_signals' => true,
                'delete_others_signals' => true,

                'edit_private_signals' => true,
                'edit_published_signals' => true,

                'create_signals' => true,
            ],
        );

        $admin = get_role('administrator');

        $caps = [
            'edit_signal',
            'read_signal',
            'delete_signal',

            'edit_signals',
            'edit_others_signals',
            'publish_signals',
            'read_private_signals',

            'delete_signals',
            'delete_private_signals',
            'delete_published_signals',
            'delete_others_signals',

            'edit_private_signals',
            'edit_published_signals',

            'create_signals',
        ];

        foreach ($caps as $cap) {
            $admin->add_cap($cap);
        }
    }
}
