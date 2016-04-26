<?php

namespace Cuzzy\Ivatar;

class Ivatar
{
    public $config = array(
        'driver' => 'gd',
        'width' => 300,
        'background' => '#999999',
        'height' => 300,
        'font' => '/opensans.ttf',
        'color' => '#d25349',
        'opacity' => 0,
        'size' => 30,
        'offset' => [
            'x' => 0,
            'y' => 0
        ]
    );

    public function __construct( array $config = array() )
    {
        $this->configure( $config );
        $this->validateFont();
    }

    public function configure( array $config = array() )
    {
        $this->config = array_replace($this->config, $config);
        return $this;
    }

    public function create( $data )
    {
        return $this->driver()->create($data);
    }
    
    public function driver()
    {
        $drivername = ucfirst($this->config['driver']);
        $driverclass = sprintf('Cuzzy\\Ivatar\\Drivers\\%s\\Driver', $drivername);

        if (class_exists($driverclass))
        {
            return new $driverclass($this->config);
        }

        throw new \Cuzzy\Ivatar\Exception\NotSupportedException(
            "Driver ({$drivername}) could not be instantiated."
        );
    }

    public function validateFont()
    {
        $this->config['font'] = public_path() . $this->config['font'];
        if ( file_exists( $this->config['font'] ) )
        {
            return true;
        }

        throw new \Cuzzy\Ivatar\Exception\NotFoundException(
            "Font not found ({$this->config['font']})"
        );
    }
}