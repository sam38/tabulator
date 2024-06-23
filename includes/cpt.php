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

include 'judges.php';
include 'contestants.php';
include 'events.php';
