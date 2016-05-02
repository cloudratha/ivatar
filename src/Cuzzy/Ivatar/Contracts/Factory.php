<?php

namespace Cuzzy\Ivatar\Contracts;

interface Factory
{
    public function driver();
    public function create( array $data );
    public function format( $format, $param = null );
    public function save();
    public function response();
    public function fetch( array $data );
    public function serve( array $data );
}
