<?php

namespace Cuzzy\Ivatar\Drivers\Imagick;

use Cuzzy\Ivatar\Color;
use Cuzzy\Ivatar\Drivers\AbstractDriver;
use Cuzzy\Ivatar\Exception;

class Driver extends AbstractDriver
{
    private function init()
    {
        $this->ivatar = new \Imagick();
        $base = new Color( $this->options['group'] );
        $this->ivatar->newImage( $this->options['size'], $this->options['size'], new \ImagickPixel( $base->getHex() ), 'jpg');
    }

    private function background()
    {
        if ( ( $this->config['method'] === 'image' ) && ( is_file( $this->config['image'] ) ) )
        {
            $image = new \Imagick();

            try {
                $image->readImage($this->config['image']);
                $image->setImageType(\Imagick::IMGTYPE_TRUECOLORMATTE);
            } catch (\ImagickException $e) {
                throw new Exception\NotSupportedException(
                    "Ivatar - ({$this->config['image']}) unable to open image."
                );
            }
            $image->cropThumbnailImage( $this->options['size'], $this->options['size']);
            $this->ivatar->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 0, 0);
        }
    }

    public function stage()
    {
        $this->init();
        $this->background();

        $color = $this->font;
        $color = new \ImagickPixel(sprintf('rgba(%d, %d, %d, %.2f)',
            $color['r'],
            $color['g'],
            $color['b'],
            (100 - $this->config['opacity']) / 100
        ));

        $text = new \ImagickDraw();
        $text->setFillColor($color);
        $text->setFont($this->config['font']);
        $size = 1;
        $text->setFontSize($size);
        $height = $this->options['size'] / 100 * $this->config['prop'];

        $metrics = $this->ivatar->queryFontMetrics($text, $this->text, false);

        while (( $metrics['boundingBox']['y2'] - $metrics['boundingBox']['y1'] ) <= $height )
        {
            $size++;
            $text->setFontSize($size);
            $metrics = $this->ivatar->queryFontMetrics($text, $this->text, false);
        }

        $x = ( ( $this->options['size'] - ( $metrics['textWidth'] ) ) / 2 ) +  ( $this->config['offset']['x'] / 100 * $this->options['size'] );
        $y = ( ( $this->options['size'] - ( $metrics['boundingBox']['y2'] - $metrics['boundingBox']['y1'] ) ) / 2 ) + ( $metrics['boundingBox']['y2'] - $metrics['boundingBox']['y1'] ) + ( $this->config['offset']['y'] / 100 * $this->options['size'] );

        $this->ivatar->annotateImage($text, $x, $y, null, $this->text);
    }

    public function encode()
    {
        $this->encode = $this->ivatar;
        return $this;
    }
}
