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

/**
 * Add filter to get excerpt from widget based post.
 */
add_filter('get_the_excerpt', function($excerpt, $post, $length = 35) {
    $widget = \App\get_widgets_type('wysiwyg', $post->ID);
    $content = $excerpt;
    if (!empty($widget)) {
        $content = strip_tags($widget[0]->wysiwyg_content);
    }
    return wp_trim_words($content, $length, '  &hellip;');
}, PHP_INT_MAX, 3);
