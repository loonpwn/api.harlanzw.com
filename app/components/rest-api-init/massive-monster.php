<?php


register_rest_route('massive-monster/v1', '/contact', [
    'methods' => 'POST',
    'callback' => function(WP_REST_Request $request) {

        $params = $request->get_json_params();
        $purpose = $params['purpose'];

        $name = $params['name'];
        $email = $params['email'];
        $message = $params['message'];

        wp_mail(
            $purpose . '@massivemonster.co',
            $name . ' - ' . $email,
            $message,
            [
                'Bcc: harlan@harlanzw.com'
            ]
        );

        $response = new WP_REST_Response();
        $response->set_status(201);
        return $response;
    }
]);
