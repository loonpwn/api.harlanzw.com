<?php

return
    [
        [
            'key' => 'field_59aa16173ac48',
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
            'default_value' => 'Instagram<small> // <a href="https://instagram.com/bensanfordmedia/">@bensanfordmedia</a></small>',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ],
        [
            'key' => 'field_59aa14f63ac66',
            'label' => 'Images to Show',
            'name' => 'limit',
            'type' => 'number',
            'instructions' => 'How many images to display',
            'required' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
            'default_value' => 20,
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'step' => 5,
        ],
        [
            'key' => 'field_59aa151d3ac69',
            'label' => 'Sort By',
            'name' => 'sort_by',
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
                'none' => 'None',
                'most-recent' => 'Most Recent',
                'least-recent' => 'Least Recent',
                'most-liked' => 'Most Liked',
                'least-liked' => 'Least Liked',
                'most-commented' => 'Most Commented',
                'least-commented' => 'Least Commented',
                'random' => 'Random'
            ],
            'default_value' => [
                0 => 'most-liked',
            ],
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => '',
        ],

    ];