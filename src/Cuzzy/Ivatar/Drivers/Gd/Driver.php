<?php

namespace Cuzzy\Ivatar\Drivers\Gd;

use Cuzzy\Ivatar\Color;
use Cuzzy\Ivatar\Drivers\AbstractDriver;
use Cuzzy\Ivatar\Exception;

class Driver extends AbstractDriver
{
    private function init()
    {
        $this->ivatar = imagecreatetruecolor( $this->options['size'], $this->options['size'] );
        imagealphablending( $this->ivatar, true );
    }

    private function background()
    {
        $base = new Color( $this->options['group'] );
        $background = $base->getRgb();
        $background = imagecolorallocate( $this->ivatar, $background['r'], $background['g'], $background['b'] );
        imagefill( $this->ivatar, 0, 0, $background );

        if ( ( $this->config['method'] === 'image' ) && ( is_file( $this->config['image'] ) ) )
        {
            $info = getimagesize( $this->config['image'] );
            switch ( $info[2] )
            {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg( $this->config['image'] );
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng( $this->config['image'] );
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif( $this->config['image'] );
                    break;
                default:
                    throw new Exception\NotSupportedException(
                        "Ivatar - ({$this->config['image']}) must be of type JPEG, PNG, or GIF."
                    );
            }
            $prop = $info[0] / ( $info[1] / $this->options['size'] );

            imagecopyresampled( $this->ivatar, $image, 0, 0, ( $prop - $this->options['size'] ) / 2, 0, $prop, $this->options['size'], $info[0], $info[1] );
        }
    }

    public function stage()
    {
        $this->init();
        $this->background();
        $size = 1;
        $color = $this->font;
        $opacity = round( $this->config['opacity'] * 1.27 );
        $color = imagecolorallocatealpha( $this->ivatar, $color['r'], $color['g'], $color['b'], $opacity );

        $initial = imagettfbbox( $size, 0, $this->config['font'], $this->text );
        $height = $this->options['size'] / 100 * $this->config['prop'];

        while ( ( $initial[1] - $initial[7] ) <= $height )
        {
            $size++;
            $initial = imagettfbbox( $size, 0, $this->config['font'], $this->text );
        }

        $x = ( ( $this->options['size'] - ( $initial[2] - $initial[0] ) ) / 2 ) + ( $this->config['offset']['x'] / 100 * $this->options['size'] );
        $y = ( ( $this->options['size'] - ( $initial[1] - $initial[7] ) ) / 2 ) + ( $initial[1] - $initial[7] ) + ( $this->config['offset']['y'] / 100 * $this->options['size'] );
        imagettftext( $this->ivatar, $size, 0, $x, $y, $color, $this->config['font'], $this->text );
    }

    public function encode()
    {
        if ( !$this->exists() )
        {
            ob_start();
            imagejpeg( $this->ivatar, null, 100 );
            $buffer = ob_get_contents();
            ob_end_clean();

        } else
        {
            $path = $this->getExport();
            $buffer = file_get_contents( $path['path'] );
        }

        $this->encode = $buffer;

        return $this->encode;
    }

    public function save()
    {
        $path = $this->getExport();
        if ( !file_exists( $path['path'] ) )
        {
            imagejpeg( $this->ivatar, $path['path'], 100 );
            $this->destroy();
        }

        return $path;
    }

    public function destroy()
    {
        imagedestroy( $this->ivatar );
    }

}
