<?php

$eventsHandle = 'event';
$eventsNonce = 'tally_nonce';
$eventsData = 'event_data';

add_action('init', function() {
    global $eventsHandle;
    register_post_type($eventsHandle, [
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

// Custom page for events page
add_filter('template_include', function($template) {
    if (is_singular('event')) {
        $default_template = '/app/wp-content/plugins/tally/single-event.php';
        if ('' != $default_template) {
            return $default_template;
        }
    }
    return $template;
}, 99);

// add meta box
add_action('add_meta_boxes', function() {
    global $eventsHandle, $post_type;
    if (! postTypeHandleMatches($eventsHandle)) { return; }

    add_meta_box(
        'event_detail',
        'Event Detail',
        'eventDetailCallback',
        $eventsHandle);
});

add_action('admin_enqueue_scripts', function() {
    global $eventsHandle;
    if (! postTypeHandleMatches($eventsHandle)) { return; }

    $path = "/wp-content/plugins/tally/assets/";

    // https://select2.org/getting-started/basic-usage
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_style('select-css', "{$path}select.min.css");
    wp_enqueue_script('select-js', "{$path}select.min.js");

    wp_enqueue_style('events-css', "{$path}event-admin.css");
    wp_enqueue_script('events-js', "{$path}event-admin.js",
        ['jquery', 'jquery-ui-sortable', 'select-js']);
});

add_action('save_post', function($postId) {
    global $eventsHandle, $eventsData, $eventsNonce;
    if (get_post_type($postId) !== $eventsHandle || ! isset($_POST[$eventsNonce])) { return $postId; }

    // verify nonce is valid
    if (! wp_verify_nonce($_POST[$eventsNonce], basename(__FILE__))) { return $postId; }

    // ignore for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $postId; }

    $criteria = [];
    $totalCriteria = count($_POST['criteria']);
    for ($i=0; $i<$totalCriteria; $i++) {
        $weight = intval(@$_POST['weight'][$i]);
        $criteria[] = [
            'id' => @$_POST['id'][$i],
            'title' => @$_POST['criteria'][$i],
            'weight' => $weight > 100 ? 100 : max($weight, 1),
        ];
    }

    $data = [
        'contestants' => @$_POST['contestants'],
        'judges' => @$_POST['judges'],
        'criteria' => $criteria
    ];

    // WP will serialize and esc_attr array items automatically
    update_post_meta($postId, $eventsData, $data);
});

function eventDetailCallback($post)
{
    global $eventsData, $eventsNonce;

    wp_nonce_field(basename(__FILE__), $eventsNonce);

    $data = get_post_meta($post->ID, $eventsData, true);
    if (! is_array($data)) {
        $data = [
            'judges' => [],
            'contestants' => [],
            'criteria' => [],
        ];
    }

    $judges = '';
    foreach (get_posts([
        'post_type' => 'judge',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ]) as $item) {
        $select = in_array($item->ID, @$data['judges']) ? 'selected' : '';
        $judges.= "<option value='{$item->ID}' {$select}>{$item->post_title}</option>";
    }

    $contestants = '';
    foreach (get_posts([
        'post_type' => 'contestant',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ]) as $item) {
        $select = in_array($item->ID, @$data['contestants']) ? 'selected' : '';
        $contestants.= "<option value='{$item->ID}' {$select}>{$item->post_title}</option>";
    }

    $criteriaFields = '';
    foreach (@$data['criteria'] as $item) {
        $id = esc_attr(@$item['id']);
        if (! $id) { $id = 'id' . rand(10000, 9999999); }
        $title = esc_attr(@$item['title']);
        $weight = esc_attr(@$item['weight']);
        $criteriaFields.= '<div>' .
            '<span class="dashicons dashicons-sort"></span>' .
            '<input type="text" name="criteria[]" size="40" placeholder="Title" value="' . $title . '" required>' .
            '<input type="hidden" name="weight[]" size="8" placeholder="Weight %" value="' . $weight . '" required>' .
            '<input type="hidden" name="id[]" value="' . $id . '">' .
            '<button type="button" class="delete-criteria">Delete</button></div>';
    }

    echo <<<EOD
<div id="tallyEvents">
    <div class="form-group">
        <label for="judges">Judges</label>
        <div class="form-content">
            <select id="judges" class="select-multi" 
                name="judges[]" multiple>{$judges}</select>
        </div>
    </div>
    <div class="form-group">
        <label for="contestants">Contestants</label>
        <div class="form-content">
            <select id="contestants" class="select-multi" 
                name="contestants[]" multiple>{$contestants}</select>
        </div>    
    </div>
    <div class="form-group">
        <label for="contestants">Criteria</label>
        <div class="form-content">
            <div id="criteria">{$criteriaFields}</div>
            <button type="button" class="add-criteria">+ Add Criteria</button>
        </div>
    </div>
</div>
EOD;
}
