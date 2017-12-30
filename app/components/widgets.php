<?php

/**
 * Only if our plugin is registered
 */

use ACFWidgets\Helpers\WidgetHelper;

if (!class_exists('ACFWidgets\Loader')) {
    return;
}
/**
 * Load in our registeres widgets
 */
add_filter('acf-widgets/register', function () {
    //load in classes
    return [
        'wysiwyg',
        'call-to-action',
        'content-columns',
        'posts',
        'full-width-image',
        'clients',
        'two-columns',
        'contact-form-7'
    ];
});

/**
 * When creating a new page it should auto-populate widgets for us to improve the accuracy and efficiency in creating content.
 */
add_action('wp_insert_post', function ($post_id, $post) {
    // Auto-draft is the status for new empty pages
    if ($post->post_status != 'auto-draft') {
        return;
    }
    // Only if the post type is supported
    if (!in_array($post->post_type, WidgetHelper::DEFAULT_SUPPORTED_POST_TYPES)) {
        return;
    }
    // The list of widgets to have shown by default
    $enabled_widgets = [
        'call-to-action',
        'wysiwyg',
        'accreditations',
    ];
    // Sets the widgets as shown
    update_post_meta($post_id, '_acf_widgets', \ACFWidgets\acf\WidgetACF::FIELD_KEY);
    update_post_meta($post_id, 'acf_widgets', serialize($enabled_widgets));

    // Extra explicit configuration
    update_post_meta($post_id, '_acf_widgets_1_wysiwyg_content', 'field_583ba277c225c');
    update_post_meta($post_id, 'acf_widgets_1_wysiwyg_content', '<h2>Change Me</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias, amet corporis cupiditate dolor illum magni repudiandae. Aliquam amet consectetur dolor ex, facere inventore, odit quae quasi qui quos reiciendis rerum?</p>');

}, 10, 2);


add_filter('acf-widgets/widgets-dir', function () {
    return ROOT_DIR . '/resources/views/widgets/';
});


/**
 * Helper function for getting a list of active widgets for a post
 * @param bool $post_id
 * @return \ACFWidgets\model\Widget[]
 */
function get_widget_list($post_id = false) {
    if (empty($post_id) && is_home()) {
        $post_id = get_option('page_for_posts');
    }
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }
    return WidgetHelper::get_widgets_for_post($post_id);
}

function get_widgets_type($type, $post_id = false) {
    return collect(get_widget_list($post_id))->filter(function ($w) use ($type) {
        return $w->slug == $type;
    })->values()->all();
}

/**
 * Checks if the first widget is call-to-action
 * @return bool
 */
function is_widget_cta_first($post = false) {
    foreach (get_widget_list($post) as $widget) {
        if (($widget->slug === 'call-to-action' || $widget->slug === 'slider') && $widget->isFirst()) {
            return true;
        }
    }
    return false;
}
