<?php
namespace App;

/**
 * Checks we have the add-on enabled
 */
if (!\function_exists('acf_add_options_page')) {
    return;
}

const OPTION_PAGE_SLUG = 'hzw-settings';

$fields = [
    [
        'key' => 'field_5aceb45eaae72',
        'label' => 'Branding',
        'name' => '',
        'type' => 'tab',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
        ],
        'placement' => 'left',
        'endpoint' => 0,
    ],
    [
        'key' => 'field_58d331f7fc040',
        'label' => 'Author Image',
        'name' => 'author_image',
        'type' => 'image',
        'instructions' => '',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
        ],
        'return_format' => 'id',
        'preview_size' => 'medium',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
    ],

];


// Add in our option pages
acf_add_options_page([
    'page_title'    => 'HZW Settings',
    'menu_title'    => 'HZW Settings',
    'menu_slug'     => OPTION_PAGE_SLUG,
    'position' => 70,
    'icon_url' => 'dashicons-admin-customizer'
]);

$fields = apply_filters('hzw/options-page-fields', $fields);

acf_add_local_field_group([
    'key' => 'group_58d331ebd36cc',
    'title' => 'Global Settings',
    'fields' => $fields,
    'location' => [
        [
            [
                'param' => 'options_page',
                'operator' => '==',
                'value' => OPTION_PAGE_SLUG,
            ],
        ],
    ],
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
]);


function get_option_page_value($option) {
    return get_field($option, 'option');
}
