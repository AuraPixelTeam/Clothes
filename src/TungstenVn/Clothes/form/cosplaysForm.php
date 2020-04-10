<?php

namespace TungstenVn\Clothes\form;

use pocketmine\Player;
use TungstenVn\Clothes\Clothes;

use jojoe77777\FormAPI\SimpleForm;
use TungstenVn\Clothes\skinStuff\resetSkin;
use TungstenVn\Clothes\skinStuff\setSkin;

class cosplaysForm
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
                if ($result > count($this->clo->cosplaysTypes)) {
                    return;
                } else {
                    $this->deeperForm($player, "", $result - 1);
                }
            }
        });
        $form->setTitle("§0Cosplays §aMenu");
        $form->setContent($txt);
        $i = 0;
        $form->addButton("Reset Skin", 0, "textures/persona_thumbnails/skin_steve_thumbnail_0");
        foreach ($this->clo->cosplaysTypes as $value) {
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
            $cosplaysName = $this->clo->cosplaysTypes[$type];
            if (!array_key_exists($result, $this->clo->cosplaysDetails[$cosplaysName])) {
                $this->mainform($player, "");
                return;
            }

            $perms = $this->clo->getConfig()->getNested('perms');
            if (array_key_exists($this->clo->cosplaysDetails[$cosplaysName][$result], $perms)) {
                if ($player->hasPermission($perms[$this->clo->cosplaysDetails[$cosplaysName][$result]])) {
                    $setskin = new setSkin();
                    $setskin->cosplays_setSkin($player, $this->clo->cosplaysDetails[$cosplaysName][$result], $this->clo->cosplaysTypes[$type]);
                } else {
                    $this->deeperForm($player, "§cYou dont have that cosplay!", $type);
                    return;
                }
            } else {
                $setskin = new setSkin();
                $setskin->cosplays_setSkin($player, $this->clo->cosplaysDetails[$cosplaysName][$result], $this->clo->cosplaysTypes[$type]);
                $player->sendMessage("§aCosplay successfull");
            }
        });
        $cosplaysName = $this->clo->cosplaysTypes[$type];
        $form->setTitle("§0" . $cosplaysName . " §aMenu");
        if ($this->clo->cosplaysDetails[$cosplaysName] != []) {
            foreach ($this->clo->cosplaysDetails[$cosplaysName] as $value) {
                $perms = $this->clo->getConfig()->getNested('perms');
                if (array_key_exists($value, $perms)) {
                    if ($player->hasPermission($perms[$value])) {
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
            $form->setContent("There is no " . $cosplaysName . " in here currently");
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