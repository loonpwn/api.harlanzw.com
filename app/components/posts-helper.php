<?php

namespace App;

function get_latest_posts($limit = 3) {
    $args = [
        'numberposts' => $limit,
        'orderby' => 'post_date',
        'order' => 'DESC',
        'post_type' => 'post',
        'post_status' => 'publish',
    ];

    $recent_posts = \wp_get_recent_posts($args, OBJECT);
    return $recent_posts;
}

/**
 * We replace a posts thumbnail with the call to action image if we have one
 */
add_filter('get_post_metadata', function ($unused, $object_id, $meta_key) {
    if ($meta_key !== '_thumbnail_id') {
        return $unused;
    }
    // get widget cta
    $widgets = get_widget_list($object_id);
    if (empty($widgets)) {
        return $unused;
    }
    if (isset($widgets[0]) && $widgets[0]->slug == 'call-to-action') {
        return $widgets[0]->background_image;
    }
    return $unused;
}, 10, 3);
