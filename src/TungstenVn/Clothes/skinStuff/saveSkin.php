<?php

namespace TungstenVn\Clothes\skinStuff;

use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\entity\Skin;
use TungstenVn\Clothes\Clothes;
class saveSkin {
	public function saveSkin(Skin $skin,$name){
		$path = Clothes::$instance->getDataFolder();
		$img = $this->toImage($skin->getSkinData());	
		if(!file_exists($path."saveskin")){
			mkdir($path."saveskin", 0777);
		}
		imagepng($img, $path."saveskin/".$name.".png");
	}
    public function toImage($data, $height = 64, $width = 64){
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