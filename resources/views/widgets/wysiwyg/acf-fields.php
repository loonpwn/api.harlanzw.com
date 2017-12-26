<?php

return [
    [
        'key' => 'field_583ba277c225c',
        'label' => 'Content',
        'name' => 'wysiwyg_content',
        'type' => 'wysiwyg',
        'allow_null' => 0,
    ],
    [
        'key' => 'field_58e6e1dd76a71',
        'label' => 'Background Colour',
        'name' => 'wysiwyg_background_colour',
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
        ],
        'choices' => [
            'bg-opaque' => 'None',
            'bg-white' => 'White',
            'bg-grey' => 'Grey',
        ],
        'default_value' => 'bg-white',
        'allow_null' => 1,
        'multiple' => 0,
        'ui' => 1,
        'ajax' => 0,
        'return_format' => 'value',
        'placeholder' => '',
    ],
    [
        'key' => 'field_59f6edf411387',
        'label' => 'Width',
        'name' => 'width',
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
        ],
        'choices' => [
            'contained' => 'Contained',
            'full-width' => 'Full Width',
        ],
        'default_value' => 'contained',
        'multiple' => 0,
        'ui' => 1,
        'ajax' => 0,
        'return_format' => 'value',
        'placeholder' => '',
    ],

];
