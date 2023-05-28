<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\skinStuff;

use pocketmine\entity\Skin;
use pocketmine\player\Player;
use TungstenVn\Clothes\Clothes;

class setSkin
{
    /**
     * @throws \JsonException
     */
    public function cosplay_setSkin(Player $player, string $stuffName, string $locate): void
    {
        $locate = "cosplays/" . $locate;
        $skin = $player->getSkin();
        $path = Clothes::getInstance()->getDataFolder() . $locate . "/" . $stuffName . ".png";
        $size = getimagesize($path);

        $this->extracted($path, $size, $player, $skin, $locate, $stuffName);
    }

    public function setSkin(Player $player, string $stuffName, string $locate): void
    {
        $locate = "clothes/" . $locate;
        $skin = $player->getSkin();
        $name = $player->getName();
        $name = str_replace("_", " ", $name);
        $path = Clothes::getInstance()->getDataFolder() . "saveskin/" . $name . ".png";

        $size = getimagesize($path);

        $path = $this->imgTricky($path, $stuffName, $locate, [$size[0], $size[1], 4]);
        $this->extracted($path, $size, $player, $skin, $locate, $stuffName);
    }

    // Add the player's skin under the clothes
    public function imgTricky(string $skinPath, string $stuffName, string $locate, array $size): string
    {
        $path = Clothes::getInstance()->getDataFolder();
        $down = imagecreatefrompng($skinPath);
        $upper = null;
        if ($size[0] * $size[1] * $size[2] == 65536) {
            $upper = $this->resize_image($path . $locate . "/" . $stuffName . ".png", 128, 128);
        } else {
            $upper = $this->resize_image($path . $locate . "/" . $stuffName . ".png", 64, 64);
        }
        //Remove black color out of the png
        imagecolortransparent($upper, imagecolorallocatealpha($upper, 0, 0, 0, 127));

        imagealphablending($down, true);
        imagesavealpha($down, true);
        imagecopymerge($down, $upper, 0, 0, 0, 0, $size[0], $size[1], 100);
        imagepng($down, $path . 'temp.png');
        return Clothes::getInstance()->getDataFolder() . 'temp.png';

    }

    public function resize_image($file, $w, $h, $crop = FALSE)
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $new_width = $w;
            $new_height = $h;
        } else {
            if ($w / $h > $r) {
                $new_width = $h * $r;
                $new_height = $h;
            } else {
                $new_height = $w / $r;
                $new_width = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        return $dst;
    }

    /**
     * @param string $path
     * @param bool|array $size
     * @param Player $player
     * @param Skin $skin
     * @param string $locate
     * @param string $stuffName
     * @return void
     * @throws \JsonException
     */
    public function extracted(string $path, bool|array $size, Player $player, Skin $skin, string $locate, string $stuffName): void
    {
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        for ($y = 0; $y < $size[1]; $y++) {
            for ($x = 0; $x < $size[0]; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry." . $locate, file_get_contents(Clothes::getInstance()->getDataFolder() . $locate . "/" . $stuffName . ".json")));
        $player->sendSkin();
    }
}