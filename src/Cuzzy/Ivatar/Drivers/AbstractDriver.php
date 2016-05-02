<?php

namespace Cuzzy\Ivatar\Drivers;

use Cuzzy\Ivatar\Drivers\Contracts\IvatarDriverInterface;
use Cuzzy\Ivatar\Color;
use Cuzzy\Ivatar\Exception;

abstract class AbstractDriver implements IvatarDriverInterface
{
    public $ivatar;
    public $encode;
    public $md5;
    protected $config;
    protected $text;
    protected $font;
    protected $size;
    protected $group;
    protected $options = [
        'size'  => '',
        'group' => ''
    ];

    public function __construct( array $config )
    {
        $this->config = $config;
    }

    public function create( $data )
    {
        $this->prepareData( $data );
        if ( !$this->exists() )
        {
            $this->stage();
        }

        return $this;
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
                    $this->$key = $data[$key];
                    $this->options[$key] = $data[$key];
                }
                $this->options[$key] = $this->getOption( $key );
            }
        } elseif ( is_string( $data ) )
        {
            $this->formatText( $data );
        }

        $this->getMethod();

        $this->md5 = md5( 'text=' . $this->text . '&method=' . $this->config['method'] . '&size=' . $this->size . '&color=' . $this->group );
    }

    public function formatText( $text )
    {
        if ( is_string( $text ) )
        {
            $text = explode( ' ', $text );
        }

        if ( is_array( $text ) )
        {
            if ( $this->config['initials'] > 1 )
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
            } else
            {
                $this->text = strtoupper( substr( $text[0], 0, 1 ) );
            }

            return true;
        }

        throw new Exception\InvalidArgumentException(
            "Ivatar - The Text is not defined."
        );
    }

    public function format( $format, $param = null )
    {
        switch ( $format )
        {
            case 'base64':
                $encode = $this->encode();

                return 'data:image/jpeg;base64,' . base64_encode( $encode );
            case 'tag':
                switch ( $param )
                {
                    case 'circle':
                        $style = 'style="border-radius:' . ( ( $this->options['size'] / 2 ) + 1 ) . 'px"';
                        break;
                    case 'rounded':
                        $style = 'style="border-radius:' . ( $this->options['size'] / 8 ) . 'px"';
                        break;
                    default:
                        $style = '';
                }

                return '<img src="' . $this->format( 'base64' ) . '" ' . $style . ' />';
            default:
                throw new Exception\NotSupportedException(
                    "Ivatar - ({$format}) unsupported format."
                );
        }
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

    public function exists()
    {
        $path = $this->getExport();

        return is_file( $path['path'] );
    }

    public function getExport()
    {
        $filename = $this->getMd5() . '.jpg';
        $path = $this->config['export'] . '/' . $filename;
        $pattern = '/(?:public)(.*)/';
        preg_match( $pattern, $path, $url );

        return [ 'filename' => $filename, 'path' => $path, 'url' => asset( $url[1] ) ];
    }

    public function getMd5()
    {
        return $this->md5;
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
        $color = new Color( $this->config['default']['font'] );
        $this->font = $color->getRgb();
    }

    public function resolveOpposite()
    {
        $color = new Color( $this->options['group'] );
        $this->font = $color->inverse( 'Rgb' );
    }

    public function resolveDarken()
    {
        $color = new Color( $this->options['group'] );
        $this->font = $color->darken( 30, 'Rgb' );
    }

    public function resolveLighten()
    {
        $color = new Color( $this->options['group'] );
        $this->font = $color->lighten( 30, 'Rgb' );
    }

    public function resolveImage()
    {
        $this->resolveStandard();
    }

}
