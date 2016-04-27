
<?php
return array(

    'driver' => 'gd',

    /*
     * Path to TTF font.
     * Defaults to OpenSans https://www.google.com/fonts/specimen/Open+Sans
     */

    'font' => '',

    /*
     * Define you defaults.
     * Defaults are used when identifiers are not found.
     */

    'default' => [
        'size'       => 150,
        'color'      => '#999999',
        'font'       => '#ffffff',
        'opacity'    => 0
    ],

    /*
     * Define custom size groups.
     */

    'sizes' => [
        'small' => 50,
        'medium' => 150,
        'large' => 300
    ],


    /*
     * Define your color groups.
     */

    'colors' => [
        'male' => [
            '#2196F3', '#4CAF50'
        ],
        'female' => [
            '#E91E63', '#9C27B0'
        ]
    ],

    /*
     * Define the proportion that the height of the text should be relative to the image size.
     * 0 - 100
     */

    'prop' => 30,


    /*
     * Set the method type for font color generation.
     * Supported: "standard", "opposite", "darker", "lighter"
     *
     * Standard : Uses the default font color
     * Opposite : Uses the color inverse of the background.
     * Darker : Uses a darker tone of the background.
     * Lighter : Uses a lighter tone of the background.
     */

    'method' => 'standard',


    /*
     * Define offsets for text placement.
     * Some fonts can have incorrect starting positions.
     */

    'offset' => [
        'x' => 0,
        'y' => 0
    ]
);
