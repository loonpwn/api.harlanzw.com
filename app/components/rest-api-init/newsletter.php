<?php


register_rest_route('newsletter/v1', '/submission', [
    'methods' => 'POST',
    'callback' => function(WP_REST_Request $request) {
        wp_mail('harlan@harlanzw.com', 'New Lead: ' . $request->get_body_params()['email'] . ' from ' . $request->get_body_params()['page'], print_r($request, true), '');

        $response = new WP_REST_Response();
        $response->set_status(201);
        return $response;
    }
]);
