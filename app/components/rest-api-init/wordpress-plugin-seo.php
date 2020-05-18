<?php

register_rest_route('wp-seo/v1', '/meta', [
    'methods' => 'POST',
    'callback' => function(WP_REST_Request $request) {

        $plugin = $request->get_body_params()['plugin_url'];

        // try get the slg from the full url
        if (str_contains($plugin, 'https://wordpress.org/plugins')) {
            $plugin = str_replace(['https://wordpress.org/plugins/', '/'], '', $plugin);
        }

        $service = new \App\services\WordPressPluginService($plugin);
        $service->fetch_all();
        $service->es_index();
        $meta = $service->meta;

        wp_mail('harlan@harlanzw.com', 'WPA New Plugin: ' . $plugin, print_r($meta, true));

        $response = new WP_REST_Response();
        $response->set_data($meta);
        $response->set_status(200);
        return $response;
    }
]);


register_rest_route('wp-seo/v1', '/keyword', [
    'methods' => 'POST',
    'callback' => function(WP_REST_Request $request) {

        $plugin = $request->get_body_params()['plugin_url'];
        $keyword = $request->get_body_params()['keyword'];

        // try get the slg from the full url
        if (str_contains($plugin, 'https://wordpress.org/plugins')) {
            $plugin = str_replace(['https://wordpress.org/plugins/', '/'], '', $plugin);
        }

        $service = new \App\services\WordPressPluginService($plugin);

        $service->get_plugin_meta();
        $service->get_seo();
        $data = $service->get_search_term_score($keyword);

        $data['explain'] = es_decode_explain($service->meta->id, $keyword);
        $rank = $data['rank']['rank'];

        $data['competitor_plugins'] = [];
        if ($rank !== 'Not Found' && $rank >= 3) {
            for ($i = 1; $i < 3; $i++) {
                $plugin = $data['rank']['results'][$rank - $i];
                // index the 2 plugins in front of the rank
                $service = new \App\services\WordPressPluginService($plugin);
                $service->fetch_all();
                if ($service->es_index()) {
                    $service->meta->explain = es_decode_explain($service->meta->id, $keyword);
                    $data['competitor_plugins'][$service->slug] = $service->meta;
                }
            }
        }

        $data['es'] = es_search($keyword);


        wp_mail('harlan@harlanzw.com', 'WPA New Keyword: ' . $plugin . ' - ' . $keyword, print_r($data, true));

        $response = new WP_REST_Response();
        $response->set_data($data);
        $response->set_status(200);
        return $response;
    }
]);
