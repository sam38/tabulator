<?php

add_action('init', function() {
    register_post_type('score', [
        'labels' => [
            'name' => 'Scores',
            'singular_name' => 'Score',
        ],
        'public' => false,
        'has_archive' => false,
        'rewrite' => [
            'slug' => 'scores'
        ],
        'menu_position' => 1,
        'show_in_rest' => false,
        'show_in_menu' => WP_GROUP_SLUG,
        'supports' => ['title'],
    ]);
});

// AJAX endpoint to store scores. Event ID, judge ID, contestant ID, scores.
add_action('rest_api_init', function() {
    register_rest_route('v1', '/score', [
        'methods' => 'POST',
        'callback' => 'scoreUpdate',
        'permission_callback' => '__return_true'
    ]);
});

function scoreUpdate($request)
{
    $brand = $request->get_header('x-brand-id');
    if (! $brand) {
        return new WP_Error('bad_request', 'White label not found', [
            'status' => 400
        ]);
    }

    $body = json_decode($request->get_body());
    if (! is_object($body)) {
        return new WP_Error('bad_request', 'Bad request body', [
            'status' => 400
        ]);
    }

    global $wpdb;
    $query = "SELECT ID FROM {$wpdb->prefix}posts ";
    $query.= "WHERE post_type='score' AND post_title=%d AND post_excerpt=%d AND post_content_filtered=%d";
    $result = $wpdb->get_row($wpdb->prepare($query, $body->event, $body->judge, $body->contestant));

    $args = [
        'post_type' => 'score',
        'post_title' => $body->event,
        'post_excerpt' => $body->judge,
        'post_content_filtered' => $body->contestant,
        'post_content' => serialize(@$body->scores),
    ];
    $postId = !$result ? wp_insert_post($args) : wp_update_post([
        'ID' => $result->ID,
        'post_content' => serialize(@$body->scores),
    ]);
    return $postId;
}
