<?php

namespace TungstenVn\Clothes\skinStuff;

use pocketmine\entity\Skin;
use pocketmine\Player;
use TungstenVn\Clothes\Clothes;

class setSkin {
    public function setSkin(Player $player, string $stuffName, string $locate) {
        $skin = $player->getSkin();
        $name = $player->getName();
        $path = $this->imgTricky($name, $stuffName, $locate);
        #$path = Clothes::$instance->getDataFolder()."wing/".$stuffName.".png";
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        $s = (int)@getimagesize($path)[1];

        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < 64; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r).chr($g).chr($b).chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry.".$locate, file_get_contents(Clothes::$instance->getDataFolder().$locate."/".$stuffName.".json")));
        $player->sendSkin();
    }

    public function imgTricky(string $playername, string $stuffName, string $locate) {
        $path = Clothes::$instance->getDataFolder();
        $down = imagecreatefrompng($path.'saveskin/'.$playername.".png");
        $upper = imagecreatefrompng($path.$locate.'/'.$stuffName.".png");
        imagealphablending($down, true);
        imagesavealpha($down, true);
        imagecopy($down, $upper, 0, 0, 0, 0, 100, 100);
        imagepng($down, $path.'temp.png');
        return Clothes::$instance->getDataFolder().'temp.png';
    }
}