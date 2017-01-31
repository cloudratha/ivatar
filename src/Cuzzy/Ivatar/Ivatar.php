<?php

namespace Cuzzy\Ivatar;

class Ivatar implements Contracts\Factory
{
    protected $config = array();

    private $ivatar;

    public function __construct( array $config = array() )
    {
        $this->configure( $config );
    }

    public function configure( array $config = array() )
    {
        $this->config = array_replace( $this->config, $config );
        $this->validateFont();
        $this->validateExport();
        if ( $this->config['method'] === 'image' )
        {
            $this->validateImage();
        }

        return $this;
    }

    public function create( array $data )
    {
        $this->ivatar = $this->driver()->create( $data );

        return $this;
    }

    public function format( $format, $param = null )
    {
        return $this->ivatar->format( $format, $param );
    }

    public function save()
    {
        return $this->ivatar->save();
    }

    public function response()
    {
        $response = new Response( $this->ivatar->encode() );

        return $response->make();
    }

    public function fetch( array $data )
    {
        $this->ivatar = $this->driver()->create( $data );
        $path = $this->ivatar->save();

        return $path;
    }

    public function serve( array $data )
    {
        $this->ivatar = $this->driver()->create( $data );
        $response = new Response( $this->ivatar->encode() );

        return $response->make();
    }

    public function driver()
    {
        $drivername = ucfirst( $this->config['driver'] );
        $driverclass = sprintf( 'Cuzzy\\Ivatar\\Drivers\\%s\\Driver', $drivername );
        if ( class_exists( $driverclass ) )
        {
            return new $driverclass( $this->config );
        }
        throw new Exception\NotSupportedException(
            "Ivatar - Driver ({$drivername}) could not be instantiated."
        );
    }

    private function validateFont()
    {
        $this->config['font'] = ( $this->config['font'] === '' ) ? $this->config['font'] = __DIR__ . '/Assets/OpenSans-Bold.ttf' : base_path( $this->config['font'] );

        if ( is_file( $this->config['font'] ) )
        {
            return true;
        }

        throw new Exception\NotFoundException(
            "Ivatar - Font not found ({$this->config['font']})"
        );
    }

    private function validateExport()
    {
        $this->config['export'] = ( $this->config['export'] === '' ) ? public_path( 'ivatar' ) : public_path( $this->config['export'] );

        if ( !is_dir( $this->config['export'] ) )
        {
            if ( mkdir( $this->config['export'], 0777, true ) )
            {
                return true;
            }
        } else
        {
            return true;
        }

        throw new Exception\NotFoundException(
            "Ivatar - Export folder could not be instantiated ({$this->config['export']})"
        );
    }

    private function validateImage()
    {
        $this->config['image'] = base_path( $this->config['image'] );

        if ( is_file( $this->config['font'] ) )
        {
            return true;
        }

        throw new Exception\NotFoundException(
            "Ivatar - Image not found ({$this->config['font']})"
        );
    }

    public function buildDriver( $driver )
    {
        return new $driver( $this->app['request'], $this->config );
    }

    public function getDefaultDriver()
    {
        return $this->buildDriver( 'Cuzzy\Ivatar\Drivers\Gd\Driver' );
    }
}
