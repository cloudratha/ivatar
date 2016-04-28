<?php

namespace Cuzzy\Ivatar;

class Ivatar implements Contracts\Factory
{
    protected $drivers = [
        'gd'
    ];

    protected $config = array();

    public function __construct( array $config = array() )
    {
        $this->configure( $config );
        $this->validateFont();
    }

    public function configure( array $config = array() )
    {
        $this->config = array_replace( $this->config, $config );

        return $this;
    }

    public function create( $data )
    {
        $ivatar = $this->driver()->create( $data );
        $response = new Response( $ivatar->encode() );

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
            "Driver ({$drivername}) could not be instantiated."
        );
    }

    public function validateFont()
    {
        $this->config['font'] = ( $this->config['font'] === '' ) ? $this->config['font'] = __DIR__ . '/Assets/OpenSans-Bold.ttf' : base_path( $this->config['font'] );

        if ( is_file( $this->config['font'] ) )
        {
            return true;
        }

        throw new Exception\NotFoundException(
            "Font not found ({$this->config['font']})"
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
