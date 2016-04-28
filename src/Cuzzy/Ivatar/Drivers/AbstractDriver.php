<?php

namespace Cuzzy\Ivatar\Drivers;

use Cuzzy\Ivatar\Drivers\Contracts\IvatarDriverInterface;
use Cuzzy\Ivatar\Color;
use Cuzzy\Ivatar\Exception;

abstract class AbstractDriver implements IvatarDriverInterface
{
    public $ivatar;
    public $encode;
    protected $config;
    protected $text;
    protected $font;
    protected $options = [
        'size'  => '',
        'color' => ''
    ];

    public function __construct( array $config )
    {
        $this->config = $config;
    }

    public function prepareData( $data )
    {
        if ( is_array( $data ) )
        {
            if ( isset( $data['text'] ) )
            {
                $this->formatText( $data['text'] );
            }
            foreach ( $this->options as $key => $value )
            {
                if ( isset( $data[$key] ) )
                {
                    $this->options[$key] = $data[$key];
                }
                $this->options[$key] = $this->getOption( $key );
            }
        } elseif ( is_string( $data ) )
        {
            $this->formatText( $data );
        }

        $this->getMethod();
    }

    public function formatText( $text )
    {
        if ( is_string( $text ) )
        {
            $text = explode( ' ', $text );
        }

        if ( is_array( $text ) )
        {
            if ( count( $text ) > 1 )
            {
                $this->text = strtoupper( substr( $text[0], 0, 1 ) . substr( $text[1], 0, 1 ) );
            } else
            {
                if ( strlen( $text[0] ) >= 2 )
                {
                    $this->text = strtoupper( substr( $text[0], 0, 2 ) );
                } else
                {
                    $this->text = strtoupper( substr( $text[0], 0, 1 ) );
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
        $group = $option . 's';
        if ( isset( $this->options[$option] ) )
        {
            if ( array_has( $this->config[$group], $this->options[$option] ) )
            {
                $value = array_get( $this->config[$group], $this->options[$option] );
                if ( is_array( $value ) )
                {
                    shuffle( $value );
                    $value = reset( $value );
                }

                return $value;
            }
        }

        return $this->config['default'][$option];

    }

    public function getMethod()
    {
        $method = 'resolve' . ucfirst( $this->config['method'] );
        if ( method_exists( $this, $method ) )
        {
            return $this->$method();
        }

        throw new Exception\NotSupportedException(
            "Ivatar - ({$this->config['method']}) is not a supported method."
        );
    }

    public function resolveStandard()
    {
        $color = new Color($this->config['default']['font']);
        $this->font = $color->getRgb();
    }

    public function resolveOpposite()
    {
        $color = new Color($this->options['color']);
        $this->font = $color->inverse('rgb');
    }

}
