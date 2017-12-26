<?php


return
    [
        [
            'key' => 'field_59aa16173ac37',
            'label' => 'Title',
            'name' => 'title',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'default_value' => 'Latest Posts',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ],
        [
            'key' => 'field_59aa14f63ac44',
            'label' => 'Posts Per Row',
            'name' => 'posts_per_row',
            'type' => 'number',
            'instructions' => 'How many posts will be displayed in a row',
            'required' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'default_value' => 3,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'min' => '2',
            'max' => '4',
            'step' => 1,
        ],
        [
            'key' => 'field_59aa151d3ac34',
            'label' => 'Type Filter',
            'name' => 'type_filter',
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
                'recent' => 'Recent',
                'choose' => 'Choose',
            ],
            'default_value' => [
                0 => 'recent',
            ],
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => '',
        ],
        [
            'key' => 'field_59aa14f63ac33',
            'label' => 'Posts To Show',
            'name' => 'limit',
            'type' => 'number',
            'instructions' => 'How many posts will be displayed in the widget',
            'required' => 0,
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_59aa151d3ac34',
                        'operator' => '==',
                        'value' => 'recent',
                    ],
                ],
            ],
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'default_value' => 3,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'min' => '',
            'max' => '',
            'step' => 1,
        ],
        [
            'key' => 'field_59aa155f3ac35',
            'label' => 'Posts To Show',
            'name' => 'posts_to_show',
            'type' => 'relationship',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_59aa151d3ac34',
                        'operator' => '==',
                        'value' => 'choose',
                    ],
                ],
            ],
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'post_type' => [
                0 => 'post',
            ],
            'taxonomy' => [
            ],
            'filters' => [
                0 => 'search',
                2 => 'taxonomy',
            ],
            'elements' => '',
            'min' => '',
            'max' => '',
            'return_format' => 'object',
        ],

    ];