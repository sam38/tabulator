<?php

define('WP_GROUP_SLUG', 'tally');

// Register root menu
add_action('admin_menu', function() {
    add_menu_page(
        'Tally',
        'Tally',
        'manage_options',
        WP_GROUP_SLUG,
        null,
        'dashicons-awards',
        29
    );
});

function postTypeHandleMatches($handle, $hook='post.php', $options=[])
{
    global $post_type;
    if (! isset($post_type) || $handle != $post_type) {
        return false;
    }

    if (! is_array($options) || empty($options)) {
        $options = [
            'post.php',
            'post-new.php',
        ];
    }
    return in_array($hook, $options);
}

include 'judges.php';
include 'contestants.php';
include 'events.php';
