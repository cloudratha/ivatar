<?php

namespace Cuzzy\Ivatar\Drivers\Gd;

use Cuzzy\Ivatar\Drivers\IvatarDriver;
use Cuzzy\Ivatar\Drivers\Contracts\IvatarDriverInterface;

class Driver extends IvatarDriver implements IvatarDriverInterface
{
    public $ivatar;

    private $required = [
        'width' => 'required|integer',
        'height' => 'required|integer'
    ];

    public function create( array $data )
    {
        ob_start();
        $data = array_replace($this->config, $data);

        $this->stage( $data );
        imagepng($this->ivatar);
        imagedestroy($this->ivatar);
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
    
    public function stage( array $data = null )
    {
        $this->ivatar = imagecreatetruecolor( $data['width'], $data['height']);
        $background = $this->validateColor($data['background']);

        if ($background == "transparent")
        {
            $transparent = imagecolorallocate($this->ivatar, 0, 0, 0);
            imagecolortransparent($this->ivatar, $transparent);
        } else
        {
            $background = imagecolorallocate($this->ivatar, $background['r'], $background['g'], $background['b']);
            imagefill($this->ivatar, 0, 0, $background);
        }
        $size = 34;
        $color = $this->validateColor($data['color']);
        $opacity = (int) round( $data['opacity'] * 1.27 );
        $color = imagecolorallocatealpha( $this->ivatar, $color['r'], $color['g'], $color['b'], $opacity);

        $initial = imagettfbbox($size, 0, $data['font'], $data['text']);
        $height = $data['height'] / 100 * $data['size'];

        if (($initial[1] - $initial[7]) <= $height )
        {
            while (($initial[1] - $initial[7]) <= $height )
            {
                $size++;
                $initial = imagettfbbox($size, 0, $data['font'], $data['text']);
            }
        } else{
            while (($initial[1] - $initial[7]) >= $height )
            {
                $size--;
                $initial = imagettfbbox($size, 0, $data['font'], $data['text']);
            }
        }


        $x = (($data['width'] - ($initial[2] - $initial[0])) / 2) + $data['offset']['x'];
        $y = (($data['height'] - ($initial[1] - $initial[7])) / 2) + ($initial[1] - $initial[7]) + $data['offset']['y'];
        imagettftext($this->ivatar, $size, 0, $x, $y, $color, $data['font'], $data['text']);
    }

}