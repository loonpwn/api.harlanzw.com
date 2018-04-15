<?php

add_action('sage/template/wpa/data', function($data) {

    if (empty($_GET['action']) || $_GET['action'] !== 'plugin-search') {
        return $data;
    }

    $plugin = $_GET['plugin-url'];

    $service = new \App\services\WordPressPluginService($plugin);

    $meta = $service->get_plugin_meta();
    $data['plugin'] = $meta;
    $data['terms'] = [];
    foreach ($meta->tags as $tag) {
        $data['terms'][$tag] = $service->get_search_term_score($meta, $tag);
    }

    $data['recommendations'] = $service->get_plugin_recommendations($meta);
    return $data;
});
