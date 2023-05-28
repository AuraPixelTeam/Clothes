<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\form;

use pocketmine\player\Player;
use TungstenVn\Clothes\Clothes;
use TungstenVn\Clothes\skinStuff\resetSkin;
use Vecnavium\FormsUI\SimpleForm;

class cosplaysForm
{

    public function send(Player $player, string $txt = ""): void
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            if ($data === null) {
                return;
            }
            if ($data == 0) {
                $this->resetSkin($player);
            } else {
                if ($data > count(Clothes::getInstance()->cosplaysTypes)) {
                    return;
                } else {
                    $deepForm = new deepForm();
                    $deepForm->send($player, "", $data - 1, "cosplays");
                }
            }
        });
        $form->setTitle("§0Cosplays §aMenu");
        $form->setContent($txt);
        $i = 0;
        $form->addButton("Reset Skin", 0, "textures/persona_thumbnails/skin_steve_thumbnail_0");
        foreach (Clothes::getInstance()->cosplaysTypes as $value) {
            $form->addButton($value, 0, "textures/items/light_block_" . $i);
            if ($i < 15) {
                $i += 3;
            } else {
                $i = 0;
            }
        }
        $form->addButton("Exit", 0, "textures/ui/redX1");
        $player->sendForm($form);
    }

    public function resetSkin(Player $player): void
    {
        $player->sendMessage("§aReset to original skin successfull");
        $reset = new resetSkin();
        $reset->setSkin($player);
    }
}
