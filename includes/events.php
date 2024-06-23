<?php

add_action('init', function() {
    register_post_type('event', [
        'labels' => [
            'name' => 'Events',
            'singular_name' => 'Event',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => [
            'slug' => 'events'
        ],
        'menu_position' => 3,
        'show_in_rest' => false,
        'show_in_menu' => WP_GROUP_SLUG,
        'supports' => ['title', 'editor'],
    ]);
});

add_filter('template_include', function($template) {
    if (is_singular('event')) {
        $default_template = '/app/wp-content/plugins/tally/single-event.php';
        if ('' != $default_template) {
            return $default_template;
        }
    }
    return $template;
}, 99);
