<?php


register_rest_route('wp-seo/v1', '/submission', [
    'methods' => 'POST',
    'callback' => function(WP_REST_Request $request) {

        $plugin = $request->get_body_params()['plugin_url'];

        $service = new \App\services\WordPressPluginService($plugin);

        $meta = $service->get_plugin_meta();
        $data['plugin'] = $meta;
        $data['terms'] = [];
        foreach ($meta->tags as $tag) {
            $data['terms'][$tag] = $service->get_search_term_score($meta, $tag);
        }

        $data['recommendations'] = $service->get_plugin_recommendations($meta);

        wp_mail('harlan@harlanzw.com', 'New WPA: ' . $plugin, '', '');

        $response = new WP_REST_Response();
        $response->set_data($data);
        $response->set_status(200);
        return $response;
    }
]);
