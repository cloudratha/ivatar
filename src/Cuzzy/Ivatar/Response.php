<?php

namespace Cuzzy\Ivatar;

class Response
{
    public $ivatar;

    public function __construct( $ivatar )
    {
        $this->ivatar = $ivatar;
    }

    public function make()
    {
        $mime = finfo_buffer( finfo_open( FILEINFO_MIME_TYPE ), $this->ivatar->encode );
        $length = strlen( $this->ivatar->encode );
        $response = \Response::make( $this->ivatar->encode );
        $response->header( 'Content-Type', $mime );
        $response->header( 'Content-Length', $length );

        return $response;
    }
}
