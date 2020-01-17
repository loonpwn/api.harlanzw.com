<?php


register_rest_route('massive-monster/v1', '/contact', [
    'methods' => 'POST',
    'callback' => function(WP_REST_Request $request) {

        $toEmail = $request->get_body_params()['purpose'];

        $name = $request->get_body_params()['name'];
        $email = $request->get_body_params()['email'];
        $message = $request->get_body_params()['message'];

        wp_mail(
            $toEmail,
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
