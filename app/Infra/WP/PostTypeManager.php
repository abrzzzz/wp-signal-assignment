<?php

namespace App\Infra\WP;

class PostTypeManager
{
    public const POST_TYPE = 'signal';

    public function register()
    {
        add_action('init', [$this, 'registerPostType']);
    }

    public function registerPostType()
    {
        register_post_type(self::POST_TYPE, $this->postTypeArgs());
    }

    private function postTypeArgs()
    {
        $supports = [
            'title',
            'author',
            'revisions',
        ];
        $labels = [
            'name' => _x('signals', 'plural'),
            'singular_name' => _x('signal', 'singular'),
            'menu_name' => _x('Signals', 'admin menu'),
            'name_admin_bar' => _x('Signal', 'admin bar'),
            'add_new' => _x('Add Signal', 'add new signal'),
            'add_new_item' => __('Add New Signal'),
            'new_item' => __('New signal', 'New Signal'),
            'edit_item' => __('Edit signal'),
            'view_item' => __('View signal'),
            'all_items' => __('All signals'),
            'search_items' => __('Search signals'),
            'not_found' => __('No signals found.'),
        ];

        return [
            'supports' => $supports,
            'labels' => $labels,
            'public' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'signal'],
            'has_archive' => true,
            'hierarchical' => false,
            'capability_type' => 'signal',
            'map_meta_cap' => true,
        ];
    }
}
