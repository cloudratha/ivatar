<?php

namespace Cuzzy\Ivatar;

use Illuminate\Support\ServiceProvider;

class IvatarServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes( array(
            __DIR__ . '/../../config/config.php' => config_path( 'ivatar.php' )
        ) );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php',
            'ivatar'
        );

        $this->app->bind( 'Ivatar', function ( $app )
        {
            return new Ivatar( $app['config']['ivatar'] );
        } );

    }

    public function provides()
    {
        return [ 'Ivatar' ];
    }
}
