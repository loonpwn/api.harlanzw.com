<?php


register_rest_route('massive-monster/v1', '/contact', [
    'methods' => 'POST',
    'callback' => function(WP_REST_Request $request) {

        $params = $request->get_json_params();
        $purpose = $params['purpose'];

        $name = $params['name'];
        $email = $params['email'];
        $message = $params['message'];

        $sentTo = $purpose . '@massivemonster.co';

        if ($purpose === 'bugs' || $purpose === 'business') {
            $sentTo = 'contact+' . $purpose . '@massivemonster.co';
        }

        $success = wp_mail(
            $sentTo,
            $name . ' - ' . $email,
            $message
        );

        $response = new WP_REST_Response();
        $response->set_status(200);
        $response->set_data([
            'success' => $success,
            'sent_to' => $sentTo,
        ]);
        return $response;
    }
]);
