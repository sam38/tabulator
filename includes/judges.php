<?php

add_action('init', function() {
    register_post_type('judge', [
        'labels' => [
            'name' => 'Judges',
            'singular_name' => 'Judge',
        ],
        'public' => true,
        'has_archive' => false,
        'rewrite' => [
            'slug' => 'judges'
        ],
        'menu_position' => 1,
        'show_in_rest' => false,
        'show_in_menu' => WP_GROUP_SLUG,
        'supports' => ['title', 'editor', 'page-attributes'],
    ]);
});
