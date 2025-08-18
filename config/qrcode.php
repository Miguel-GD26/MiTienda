<?php

/*
 * This file is part of the Simple QrCode package.
 *
 * (c) SimpleSoftwareIO <support@simplesoftware.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Renderer
    |--------------------------------------------------------------------------
    |
    | This option controls the default renderer that will be used when
    | creating a QrCode. The default is 'imagick' which has the widest
    | support of all rendering drivers.
    |
    */

    'renderer' => 'gd', // <--- ¡¡¡HEMOS FORZADO EL USO DE GD AQUÍ!!!

    /*
    |--------------------------------------------------------------------------
    | Renderers
    |--------------------------------------------------------------------------
    |
    | The list of renderers that are available for use.
    |
    */

    'renderers' => [
        'imagick' => [
            'driver' => \SimpleSoftwareIO\QrCode\Imagick::class,
        ],
        'gd' => [
            'driver' => \SimpleSoftwareIO\QrCode\Gd::class,
        ],
        'eps' => [
            'driver' => \SimpleSoftwareIO\QrCode\Eps::class,
        ],
        'svg' => [
            'driver' => \SimpleSoftwareIO\QrCode\Svg::class,
        ],
    ],

    'writer' => \BaconQrCode\Writer::class,
    'writer_options' => [],
    'error_correction' => 'M', 
    'round_block_size' => true,
    'disable_box_size_warning' => false,
];