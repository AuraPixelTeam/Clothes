<?php

namespace TungstenVn\Clothes\skinStuff;

use pocketmine\entity\Skin;
use TungstenVn\Clothes\Clothes;

class saveSkin {
    public function saveSkin(Skin $skin,$name){
        $path = Clothes::$instance->getDataFolder();
       
        if(!file_exists($path."saveskin")){
            mkdir($path."saveskin", 0777);
        }

        if(file_exists($path."saveskin/".$name.".txt")){
            unlink($path."saveskin/".$name.".txt");
        }

        file_put_contents($path."saveskin/".$name.".txt",$skin->getSkinData());
        
        #var_dump(filesize($path."saveskin/".$name.".txt"));     
        
        $img = null;
        if(filesize($path."saveskin/".$name.".txt") == 65536){
            $img = $this->toImage($skin->getSkinData(),128,128);
        }else{
            $img = $this->toImage($skin->getSkinData(),64,64);
        }
        imagepng($img, $path."saveskin/".$name.".png");
    }
    // taken from https://github.com/thebigsmileXD/skinapi/blob/master/src/xenialdan/skinapi/API.php
    public function toImage($data, $height, $width){
        $pixelarray = str_split(bin2hex($data), 8);
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);//do not touch
        imagesavealpha($image, true);
        $position = count($pixelarray) - 1;
        while (!empty($pixelarray)){
            $x = $position % $width;
            $y = ($position - $x) / $height;
            $walkable = str_split(array_pop($pixelarray), 2);
            $color = array_map(function ($val){ return hexdec($val); }, $walkable);
            $alpha = array_pop($color); // equivalent to 0 for imagecolorallocatealpha()
            $alpha = ((~((int)$alpha)) & 0xff) >> 1; // back = (($alpha << 1) ^ 0xff) - 1
            array_push($color, $alpha);
            imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
            $position--;
        }
        return $image;
    }
}