<?php

namespace Cuzzy\Ivatar\Drivers;

use Illuminate\Http\Request;
use Cuzzy\Ivatar\Drivers\Contracts\IvatarDriverInterface;
use Cuzzy\Ivatar\Exception;

abstract class AbstractDriver implements IvatarDriverInterface
{
    protected $request;
    protected $config;
    protected $text;
    protected $font;
    protected $options = [
        'size' => '',
        'color' => ''
    ];

    public function __construct( Request $request, array $config )
    {
        $this->request = $request;
        $this->config = $config;
    }

    public function prepareData( $data )
    {
        if ( is_array($data) )
        {
            if (isset($data['text']))
            {
                $this->formatText( $data['text'] );
            }
            foreach ($this->options as $key => $value)
            {
                if (isset($data[$key]))
                {
                    $this->options[$key] = $data[$key];
                }
                $this->options[$key] = $this->getOption( $key );
            }
        } elseif (is_string( $data) )
        {
            $this->formatText( $data );
        }

        $this->getMethod();
    }

    public function formatText( $text )
    {
        if (is_string( $text ))
        {
            $text = explode(' ', $text);
        }

        if (is_array( $text ))
        {
            if (count($text) > 1)
            {
                $this->text = strtoupper(substr($text[0], 0, 1).substr($text[1], 0, 1));
            } else
            {
                if (strlen($text[0]) >= 2)
                {
                    $this->text = strtoupper(substr($text[0], 0, 2));
                } else
                {
                    $this->text = strtoupper(substr($text[0], 0, 1));
                }
            }

            return true;
        }

        throw new Exception\InvalidArgumentException(
            "Ivatar - The Text is not defined."
        );
    }

    public function getOption( $option )
    {
        $group = $option .'s';
        if (isset($this->options[$option]))
        {
            if (array_has($this->config[$group], $this->options[$option] ) )
            {
                $value = array_get($this->config[$group], $this->options[$option]);
                if (is_array($value))
                {
                    shuffle($value);
                    $value = reset($value);
                }

                return $value;
            }
        }

        return $this->config['default'][$option];

    }

    public function getMethod()
    {
        $method = 'resolve' . ucfirst($this->config['method']);
        if (method_exists($this, $method))
        {
            return $this->$method();
        }

        throw new Exception\NotSupportedException(
            "Ivatar - ({$this->config['method']}) is not a supported method."
        );
    }

    public function resolveStandard()
    {
        $this->font = $this->config['default']['font'];
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

        throw new Exception\NotSupportedException(
            "Ivatar - ({$color}) is not a valid color format."
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
