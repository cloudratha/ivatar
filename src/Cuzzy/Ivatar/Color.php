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

    public function convertRgbToHsl( $rgb )
    {
        $rgb = array_map( function( $value )
        {
            return $value / 255;
        }, $rgb);

        $min = min($rgb);
        $max = max($rgb);
        $diff = $max - $min;

        $hsl = [
            'l' => ($max + $min) /2,
        ];

        if ($diff == 0)
        {
            $hsl['h'] = $hsl['s'] = 0;
        } else
        {
            $hsl['s'] = ($hsl['l'] < 0.5) ? $diff / ($max + $min) : $diff / (2 - $max - $min);

            $diffrgb = array_map( function( $value ) use ($max, $diff)
            {
                return ( ( ( $max - $value ) / 6 ) + ( $diff / 2 ) ) / $diff;
            }, $rgb);

            switch ($max)
            {
                case $rgb['r']:
                    $hsl['h'] = $diffrgb['b'] - $diffrgb['g'];
                    break;
                case $rgb['g']:
                    $hsl['h'] = (1 / 3) + $diffrgb['r'] - $diffrgb['b'];
                    break;
                case $rgb['b']:
                    $hsl['h'] = (2 / 3) + $diffrgb['g'] - $diffrgb['r'];
            }
            if ($hsl['l'] < 0) $hsl['h']++;
            if ($hsl['l'] > 0) $hsl['h']--;

            $hsl['l'] = $hsl['l'] * 360;
        }
        return $hsl;
    }

    public function convertHslToRgb( $hsl )
    {
        $hsl['h'] = $hsl['h'] / 360;
        if ($hsl['s'] === 0)
        {
            $rgb['r'] = $hsl['l'] * 255;
            $rgb['g'] = $hsl['l'] * 255;
            $rgb['b'] = $hsl['l'] * 255;
        } else
        {
            $arg2 = ($hsl['l'] < 0.5) ? $hsl['l'] * (1 + $hsl['s']) : ($hsl['l'] + $hsl['s']) - ($hsl['s'] * $hsl['l']);
            $arg1 = 2 * $hsl['l'] - $arg2;

            $rgb['r'] = round( 255 * $this->convertHueToRgb( [ $arg1, $arg2, $hsl['h'] + ( 1 / 3 ) ] ) );
            $rgb['g'] = round( 255 * $this->convertHueToRgb( [ $arg1, $arg2, $hsl['h'] ] ) );
            $rgb['b'] = round( 255 * $this->convertHueToRgb( [ $arg1, $arg2, $hsl['h'] - ( 1 / 3 ) ] ) );
        }
        return $rgb;
    }

    public function convertHslToHex( $hsl )
    {
        $rgb = $this->convertHslToRgb( $hsl );
        return $this->convertRgbToHex( $rgb );
    }

    private function convertHueToRgb( $hue ) {
        if( $hue[2] < 0 )
        {
            $hue[2] += 1;
        }
        if( $hue[2] > 1 )
        {
            $hue[2] -= 1;
        }
        if ( ( 6 * $hue[2] ) < 1 )
        {
            return ($hue[0] + ($hue[1] - $hue[0]) * 6 * $hue[2]);
        }
        if ( ( 2 * $hue[2] ) < 1 )
        {
            return $hue[1];
        }
        if ( ( 3 * $hue[2] ) < 2 )
        {
            return ( $hue[0] + ( $hue[1] - $hue[0] ) * ( ( 2 / 3 ) - $hue[2] ) * 6 );
        }

        return $hue[0];
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

    public function darken( $amount, $type = 'hex' )
    {
        $darken = $this->convertRgbToHsl( $this->rgb );
        $darken['l'] = ($darken['l'] * 100) - $amount;
        $darken['l'] = ($darken['l'] < 0) ? 0 : $darken['l'] / 100;
        return $this->response( $darken, $type);
    }

    public function lighten( $amount, $type = 'hex' )
    {
        $lighten = $this->convertRgbToHsl( $this->rgb );
        $lighten['l'] = ($lighten['l'] * 100) + $amount;
        $lighten['l'] = ($lighten['l'] < 0) ? 1 : $lighten['l'] / 100;
        return $this->response( $lighten, $type);
    }

    private function response( $value, $type )
    {
        $original = $this->determineColor( $value );
        $method = 'convert' . $original . 'To' . $type;
        if ( method_exists( $this, $method ) )
        {
            return $this->$method( $value );
        }

        throw new Exception\NotSupportedException(
            "No method for conversion exists ({$method})."
        );
    }

    private function determineColor( $color )
    {
        if (is_string($color))
        {
            return 'Hex';
        }
        if (is_array($color) && isset($color['r']))
        {
            return 'Rgb';
        }

        return 'Hsl';
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
