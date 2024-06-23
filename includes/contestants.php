<?php

add_action('init', function() {
    register_post_type('contestant', [
        'labels' => [
            'name' => 'Contestants',
            'singular_name' => 'Contestant',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => [
            'slug' => 'contestants'
        ],
        'menu_position' => 2,
        'show_in_rest' => false,
        'show_in_menu' => WP_GROUP_SLUG,
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes'],
    ]);
});
