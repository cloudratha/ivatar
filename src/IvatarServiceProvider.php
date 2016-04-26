<?php

namespace Cuzzy\Ivatar;

use Illuminate\Support\ServiceProvider;

class IvatarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Cuzzy\Ivatar\Ivatar', function(){

            return new Ivatar();

        });
    }
}
