<?php


register_rest_route('menus/v1', '/menus', [
    'methods' => 'GET',
    'callback' => function() {
        $menus = [];
        foreach (get_registered_nav_menus() as $slug => $description) {
            $obj              = new stdClass;
            $obj->slug        = $slug;
            $obj->description = $description;
            $menus[]          = $obj;
        }
        return $menus;
    }
]);

register_rest_route('menus/v1', '/menus/(?P<id>[a-zA-Z0-9_-]+)', array(
    'methods' => 'GET',
    'callback' => function($data) {
        $menu = new stdClass;
        $menu->items = [];
        if (( $locations = get_nav_menu_locations() ) && isset($locations[ $data['id'] ])) {
            $menu = get_term($locations[ $data['id'] ]);
            $menu->items = collect(wp_get_nav_menu_items($menu->term_id))->transform(function($menuItem) {
                $menuItem->slug = \App\relative_url($menuItem->url);
                return $menuItem;
            });
        }
        return $menu;
    }
));
