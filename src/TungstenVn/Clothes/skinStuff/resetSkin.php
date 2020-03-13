<?php

namespace TungstenVn\Clothes\skinStuff;

use pocketmine\entity\Skin;
use pocketmine\Player;
use TungstenVn\Clothes\Clothes;

class resetSkin {


    public function setSkin(Player $player) {
        $skin = $player->getSkin();
        $name = $player->getName();
        $path = Clothes::$instance->getDataFolder()."saveskin/".$name.".png";
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
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry.humanoid.custom",file_get_contents(Clothes::$instance->getDataFolder(). "steve.json")));
        $player->sendSkin();
    }
}