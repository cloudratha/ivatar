<?php

namespace Cuzzy\Ivatar\Facades;

use Illuminate\Support\Facades\Facade;

class Ivatar extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Ivatar';
    }
}
