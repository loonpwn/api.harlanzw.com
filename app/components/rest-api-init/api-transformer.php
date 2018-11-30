<?php

use ACFWidgets\acf\WidgetACF;

register_rest_field(['post', 'page'], 'widgets', [
    'get_callback' => function($page) {
        return collect(get_field(WidgetACF::FIELD_ID, $page['id']))
            ->filter(function($widget) {
                return $widget['enabled'];
            })
            ->transform(function($widget) {
                $widget['component'] = $widget['acf_fc_layout'];
                foreach ($widget as $key => $value) {
                    if (str_contains($key, 'image') && is_numeric($value)) {
                        $widget[$key] = wp_get_attachment_image_src($value, 'full');
                    }
                }
                unset($widget['enabled'], $widget['acf_fc_layout']);
                return $widget;
            })->toArray();
    },
]);

register_rest_field(['post', 'page'], 'read_time', [
    'get_callback' => function($page) {
        return \App\get_page_reading_time($page['id']);
    },
]);

function wpseo_page_title($post_id) {
    $fixed_title = WPSEO_Meta::get_value('title', $post_id);
    if ($fixed_title !== '') {
        return $fixed_title;
    }

    $post = get_post($post_id);

    if (is_object($post) && WPSEO_Options::get('title-' . $post->post_type, '') !== '') {
        $title_template = WPSEO_Options::get('title-' . $post->post_type);
        $title_template = str_replace(' %%page%% ', ' ', $title_template);

        return wpseo_replace_vars($title_template, $post);
    }

    return wpseo_replace_vars('%%title%%', $post);
}


register_rest_field(['post', 'page'], 'seo', [
    'get_callback' => function($page) {
        $seo = [];
        $post  = get_post($page['id'], ARRAY_A);
        $title = wpseo_replace_vars(wpseo_page_title($page['id']), $post);
        $title = apply_filters('wpseo_title', $title);

        $post         = get_post($page['id'], ARRAY_A);
        $metadesc_val = wpseo_replace_vars(WPSEO_Meta::get_value('metadesc', $page['id']), $post);
        $metadesc_val = apply_filters('wpseo_metadesc', $metadesc_val);


        $seo['title'] = $title;
        $seo['description'] = $metadesc_val;
        return $seo;
    },
]);
