<?php

namespace Cuzzy\Ivatar\Drivers;

use Cuzzy\Ivatar\Exception\NotSupportedException;

abstract class IvatarDriver
{
    protected $config;

    protected $data;

    public function __construct( array $config )
    {
        $this->config = $config;
    }

    public function validateColor ($color)
    {
        $color = trim($color);
        if ( $color == "transparent" )
        {
            return $color;
        }

        $hex = str_replace('#', '', $color);
        if ( preg_match('/^[a-f0-9]{6}$/i', $hex))
        {
            return $this->hex2rgb( $hex );
        }

        if ( preg_match('/([01]?\d\d?|2[0-4]\d|25[0-5])(\W+)([01]?\d\d?|2[0-4]\d|25[0-5])\W+(([01]?\d\d?|2[0-4]\d|25[0-5]))$/i', $color))
        {
            return explode(',', $color);
        }

        throw new NotSupportedException(
            "({$color}) is not a valid color format"
        );

    }

    public function hex2rgb ( $hex )
    {

        if ( strlen( $hex ) == 6 )
        {
            $rgb['r'] = hexdec( substr( $hex, 0, 2 ) );
            $rgb['g'] = hexdec( substr( $hex, 2, 2 ) );
            $rgb['b'] = hexdec( substr( $hex, 4, 2 ) );
        } else
        {
            if ( strlen( $hex ) == 3 )
            {
                $rgb['r'] = hexdec( str_repeat( substr( $hex, 0, 1 ), 2 ) );
                $rgb['g'] = hexdec( str_repeat( substr( $hex, 1, 1 ), 2 ) );
                $rgb['b'] = hexdec( str_repeat( substr( $hex, 2, 1 ), 2 ) );
            } else
            {
                $rgb['r'] = '0';
                $rgb['g'] = '0';
                $rgb['b'] = '0';
            }
        }

        return $rgb;
    }

}