<?php

namespace TungstenVn\Clothes\form;

use pocketmine\player\Player;
use TungstenVn\Clothes\Clothes;

use jojoe77777\FormAPI\SimpleForm;
use TungstenVn\Clothes\skinStuff\resetSkin;
use TungstenVn\Clothes\skinStuff\setSkin;

class clothesForm
{
    public $clo;

    public function __construct(Clothes $clo)
    {
        $this->clo = $clo;
    }

    public function mainform(Player $player, string $txt)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            $result = $data;
            if ($result === null) {
                return;
            }
            if ($result == 0) {
                $this->resetSkin($player);
            } else {
                if ($result > count($this->clo->clothesTypes)) {
                    return;
                } else {
                    $this->deeperForm($player, "", $result - 1);
                }
            }
        });
        $form->setTitle("§0Clothes §aMenu");
        $form->setContent($txt);
        $i = 0;
        $form->addButton("Reset Skin", 0, "textures/persona_thumbnails/skin_steve_thumbnail_0");
        foreach ($this->clo->clothesTypes as $value) {
            $form->addButton($value, 0, "textures/items/light_block_" . $i);
            if ($i < 15) {
                $i += 3;
            } else {
                $i = 0;
            }
        }
        $form->addButton("Exit", 0, "textures/ui/redX1");
        $player->sendForm($form);
        return $form;
    }

    public function deeperForm(Player $player, string $txt, int $type)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) use ($type) {
            $result = $data;
            if ($result === null) {
                return;
            }
            $clothesName = $this->clo->clothesTypes[$type];
            if (!array_key_exists($result, $this->clo->clothesDetails[$clothesName])) {
                $this->mainform($player, "");
                return;
            }

            $perms = $this->clo->getConfig()->getNested('perms');
            if (array_key_exists($this->clo->clothesDetails[$clothesName][$result], $perms)) {
                if ($player->hasPermission($perms[$this->clo->clothesDetails[$clothesName][$result]])) { //If you have an op and you still can't use the clothes, don't rush to claim that the clothes are faulty. in PM4 when you have op but you also have to setPermission for yourself to use these clothes.
                    $setskin = new setSkin();
                    $setskin->setSkin($player, $this->clo->clothesDetails[$clothesName][$result], $this->clo->clothesTypes[$type]);
                } else {
                    $this->deeperForm($player, "§cYou dont have that cloth!", $type);
                    return;
                }
            } else {
                $setskin = new setSkin();
                $setskin->setSkin($player, $this->clo->clothesDetails[$clothesName][$result], $this->clo->clothesTypes[$type]);
                $player->sendMessage("§aChange cloth successfull");
            }
        });
        $clothesName = $this->clo->clothesTypes[$type];
        $form->setTitle("§0" . $clothesName . " §aMenu");
        if ($this->clo->clothesDetails[$clothesName] != []) {
            foreach ($this->clo->clothesDetails[$clothesName] as $value) {
                $perms = $this->clo->getConfig()->getNested('perms');
                if (array_key_exists($value, $perms)) {
                    if ($player->hasPermission($perms[$value])) {  //If you have an op and you still can't use the clothes, don't rush to claim that the clothes are faulty. in PM4 when you have op but you also have to setPermission for yourself to use these clothes.
                        $form->addButton($value, 0, "textures/ui/check");
                    } else {
                        $form->addButton($value, 0, "textures/ui/icon_lock");
                    }
                } else {
                    $form->addButton($value, 0, "textures/ui/check");
                }
            }
            $form->setContent($txt);
            $form->addButton("Back", 0, "textures/gui/newgui/undo");
        } else {
            $form->setContent("There is no " . $clothesName . " in here currently");
            #chinh lai image undo, va ktra lai nut back xuong duoi cac option
            $form->addButton("Back", 0, "textures/gui/newgui/undo");
        }
        $player->sendForm($form);
        return $form;
    }

    public function resetSkin(Player $player)
    {

        $player->sendMessage("§aReset to original skin successfull");
        $reset = new resetSkin();
        $reset->setSkin($player);
    }

}