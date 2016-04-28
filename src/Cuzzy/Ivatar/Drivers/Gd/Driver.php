<?php

namespace Cuzzy\Ivatar\Drivers\Gd;

use Cuzzy\Ivatar\Color;
use Cuzzy\Ivatar\Drivers\AbstractDriver;

class Driver extends AbstractDriver
{

    public function create( $data )
    {
        $this->prepareData( $data );
        $this->stage();

        return $this;
    }

    public function stage()
    {
        $this->ivatar = imagecreatetruecolor( $this->options['size'], $this->options['size'] );
        $base = new Color( $this->options['color'] );
        $background = $base->getRgb();
        $background = imagecolorallocate( $this->ivatar, $background['r'], $background['g'], $background['b'] );
        imagefill( $this->ivatar, 0, 0, $background );

        $size = 1;

        $color = $this->font;
        $opacity = (int) round( $this->config['default']['opacity'] * 1.27 );
        $color = imagecolorallocatealpha( $this->ivatar, $color['r'], $color['g'], $color['b'], $opacity );

        $initial = imagettfbbox( $size, 0, $this->config['font'], $this->text );
        $height = $this->options['size'] / 100 * $this->config['prop'];

        if ( ( $initial[1] - $initial[7] ) <= $height )
        {
            while ( ( $initial[1] - $initial[7] ) <= $height )
            {
                $size++;
                $initial = imagettfbbox( $size, 0, $this->config['font'], $this->text );
            }
        } else
        {
            while ( ( $initial[1] - $initial[7] ) >= $height )
            {
                $size--;
                $initial = imagettfbbox( $size, 0, $this->config['font'], $this->text );
            }
        }


        $x = ( ( $this->options['size'] - ( $initial[2] - $initial[0] ) ) / 2 ) + $this->config['offset']['x'];
        $y = ( ( $this->options['size'] - ( $initial[1] - $initial[7] ) ) / 2 ) + ( $initial[1] - $initial[7] ) + $this->config['offset']['y'];
        imagettftext( $this->ivatar, $size, 0, $x, $y, $color, $this->config['font'], $this->text );
    }

    public function encode()
    {
        ob_start();
        imagepng( $this->ivatar );
        $buffer = ob_get_contents();
        ob_end_clean();
        $this->encode = $buffer;

        return $this;
    }

}
