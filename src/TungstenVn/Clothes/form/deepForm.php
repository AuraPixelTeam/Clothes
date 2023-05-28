<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\form;

use pocketmine\player\Player;
use TungstenVn\Clothes\Clothes;
use TungstenVn\Clothes\skinStuff\setSkin;
use Vecnavium\FormsUI\SimpleForm;

class deepForm
{

    public function send(Player $player, string $txt, int $type, string $itemName): void
    {
        $saveName = $itemName;
        $clothes = Clothes::getInstance();
        $form = new SimpleForm(function (Player $player, ?int $data) use ($clothes, $type, $itemName, $saveName) {
            if ($data === null) {
                return;
            }

            $itemTypes = ($itemName === "cosplays") ? $clothes->cosplaysTypes : $clothes->clothesTypes;
            $itemDetails = ($itemName === "cosplays") ? $clothes->cosplaysDetails : $clothes->clothesDetails;
            $itemName = $itemTypes[$type];
            $itemDetails = $itemDetails[$itemName];

            $errorMessage = "§cYou don't have that $itemName!";
            $formInstance = ($saveName === 'clothes') ? new clothesForm() : new cosplaysForm();

            if (!array_key_exists($data, $itemDetails)) {
                $formInstance->send($player, $errorMessage);
                return;
            }

            $perms = $clothes->getConfig()->getNested('perms');
            $selectedItem = $itemDetails[$data];

            if (array_key_exists($selectedItem, $perms)) {
                if ($player->hasPermission($perms[$selectedItem])) {
                    $setSkin = new setSkin();
                    if ($saveName === "cosplays") {
                        $setSkin->cosplay_setSkin($player, $selectedItem, $itemName);
                    } else {
                        $setSkin->setSkin($player, $selectedItem, $itemName);
                    }
                } else {
                    $this->send($player, $errorMessage, $type, $itemName);
                }
            } else {
                $setSkin = new setSkin();
                if ($saveName === "cosplays") {
                    $setSkin->cosplay_setSkin($player, $selectedItem, $itemName);
                } else {
                    $setSkin->setSkin($player, $selectedItem, $itemName);
                }
                $player->sendMessage("§aChange $itemName successful");
            }
        });

        $itemName = ($saveName === "cosplays") ? $clothes->cosplaysTypes[$type] : $clothes->clothesTypes[$type];
        $form->setTitle("§0" . $itemName . " §aMenu");

        $itemDetails = ($saveName === "cosplays") ? $clothes->cosplaysDetails[$itemName] : $clothes->clothesDetails[$itemName];

        if (!empty($itemDetails)) {
            foreach ($itemDetails as $value) {
                $perms = $clothes->getConfig()->getNested('perms');
                if (array_key_exists($value, $perms) && !$player->hasPermission($perms[$value])) {
                    $form->addButton($value, 0, "textures/ui/icon_lock");
                } else {
                    $form->addButton($value, 0, "textures/ui/check");
                }
            }
            $form->setContent($txt);
            $form->addButton("Back", 0, "textures/gui/newgui/undo");
        } else {
            $form->setContent("There is no $itemName currently.");
            $form->addButton("Back", 0, "textures/gui/newgui/undo");
        }

        $player->sendForm($form);
    }
}