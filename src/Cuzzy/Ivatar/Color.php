<?php

namespace Cuzzy\Ivatar;

class Color
{
    public $hex;
    public $rgb;

    public function __construct( $color )
    {
        $this->parse($color);
    }

    public function parse( $color )
    {
        $color = trim( $color );

        $hex = str_replace( '#', '', $color );
        if ( preg_match( '/^[a-f0-9]{6}$/i', $hex ) )
        {
            $this->hex = '#' . $hex;
            $this->rgb = $this->convertHexToRgb( $this->hex );
            return true;
        }

        if ( preg_match( '/([01]?\d\d?|2[0-4]\d|25[0-5])(\W+)([01]?\d\d?|2[0-4]\d|25[0-5])\W+(([01]?\d\d?|2[0-4]\d|25[0-5]))$/i', $color ) )
        {
            $rgb = explode( ',', $color );
            $this->rgb = [
                'r' => $rgb[0],
                'g' => $rgb[1],
                'b' => $rgb[2]
            ];
            $this->hex = $this->convertRgbToHex( $this->rgb );
            return true;
        }

        throw new Exception\NotSupportedException(
            "Ivatar - ({$color}) is not a valid color format."
        );
    }

    public function convertHexToRgb( $hex )
    {
        $hex = ltrim($hex, '#');
        $hex = (strlen($hex) == 3) ? $hex.$hex : $hex;
        list($r,$g,$b) = array_map('hexdec',str_split($hex,2));
        return [
            'r' => $r,
            'g' => $g,
            'b' => $b
        ];
    }

    public function convertRgbToHex( $rgb )
    {
        $hex = "#";
        $hex .= str_pad(dechex($rgb['r']), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb['g']), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb['b']), 2, "0", STR_PAD_LEFT);
        return $hex;
    }

    public function inverse( $type = 'hex')
    {
        $inverse = $this->rgb;
        foreach($inverse as $key => $value)
        {
            $inverse[$key] = 255 - $value;
        }
        if ($type === 'hex')
        {
            $inverse = $this->convertRgbToHex( $inverse );
        }
        return $inverse;
    }

    public function getHex()
    {
        return $this->hex;
    }

    public function getRgb()
    {
        return $this->rgb;
    }
}
