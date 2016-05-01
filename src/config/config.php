<?php
return array(

    'driver' => 'gd',

    /*
     * Path to TTF font.
     * Defaults to OpenSans Bold https://www.google.com/fonts/specimen/Open+Sans
     */

    'font' => '',

    /*
     * Set the method type for font color generation.
     * Supported: "standard", "opposite", "darken", "lighten"
     *
     * Standard : Uses the default font color
     * Opposite : Uses the color inverse of the background.
     * Darken : Uses a darker tone of the background.
     * Lighten : Uses a lighter tone of the background.
     * Image : Uses the defined image as the background with the default font color
     */

    'method' => 'standard',

    /*
     * Path to background image.
     * Make sure the image is the same resolution as your largest defined size.
     */

    'image' => '',

    /*
     * Define you defaults.
     * Defaults are used when identifiers are not set.
     */

    'default' => [
        'size'       => 150,
        'group'      => '#999999',
        'font'       => '#ffffff'
    ],

    /*
     * Define the percentage that the height of the text should be relative to the image size.
     * 0 - 100
     */

    'prop' => 30,

    /*
     * Define the opacity of the text.
     * 0 - 100
     */

    'opacity' => 0,

    /*
     * Define custom size groups.
     * Can be nested.
     */

    'sizes' => [
        'small' => 50,
        'medium' => 150,
        'large' => 300
    ],


    /*
     * Define your color groups.
     * Can be nested.
     */

    'groups' => [
        'male' => [
            '#2196F3', '#4CAF50'
        ],
        'female' => [
            '#E91E63', '#9C27B0'
        ]
    ],

    /*
     * Define offsets for text placement.
     * Some fonts can have incorrect starting positions.
     */

    'offset' => [
        'x' => 0,
        'y' => 0
    ]
);
