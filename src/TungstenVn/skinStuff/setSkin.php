<?php

namespace TungstenVn\skinStuff;

use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\entity\Skin;
use TungstenVn\Clothes;
class setSkin {
	public function setSkin($player,$stuffName,$locate){
        $skin = $player->getSkin();$name = $player->getName();
        $path = $this->imgTricky($name,$stuffName,$locate);
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
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry.".$locate,file_get_contents(Clothes::$instance->getDataFolder().$locate."/". $stuffName.".json")));
        $player->sendSkin();
    }
    public function imgTricky($playername,$stuffName,$locate){
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