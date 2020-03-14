<?php

namespace TungstenVn\Clothes;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use TungstenVn\Clothes\copyResource\copyResource;
use TungstenVn\Clothes\skinStuff\resetSkin;
use TungstenVn\Clothes\skinStuff\saveSkin;
use TungstenVn\Clothes\skinStuff\setSkin;
use TungstenVn\Clothes\checkStuff\checkRequirement;
use TungstenVn\Clothes\checkStuff\checkClothes;
class Clothes extends PluginBase implements Listener {
    /** @var self $instance */
    public static $instance;

    // sth like ["wing","lefthand"]:
    public $clothesTypes = []; 

    //something like ["wing" =>["wing1","wing2"]]:
    public $clothesDetails = []; 

    public function onEnable() {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        
        $a = new checkRequirement();
        $a->checkRequirement();

        $a = new checkClothes();
        $a->checkClothes();
    }

    public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
        if($sender instanceof Player) {
            switch(strtolower($command->getName())) {
                case "clo":
                case "clothes":
                    $this->mainform($sender, "");
                break;
            }
        }else {
            $sender->sendMessage("§cOnly work in game");
        }
        return true;
    }

    public function mainform(Player $player, string $txt) {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            $result = $data;
            if($result === null) {
                return;
            }
            if($result == 0){
            	$this->resetSkin($player);
            }else{         	
            	if($result > count($this->clothesTypes)){
            		return;
            	}else{       
            		$this->deeperForm($player, "",$result -1);
            	}
            }
        });
        $form->setTitle("§0Clothes §aMenu");
        $form->setContent($txt);
        $i = 0;
        $form->addButton("Reset Skin", 0, "textures/persona_thumbnails/skin_steve_thumbnail_0");
        foreach ($this->clothesTypes as $value) {
        	$form->addButton($value, 0, "textures/items/light_block_".$i);
        	if($i < 15){	
        	    $i += 3;
        	}else{
        		$i = 0;
        	}
        }
        $form->addButton("Exit", 0, "textures/ui/redX1");
        $player->sendForm($form);
        return $form;
    }
    public function deeperForm(Player $player, string $txt,int $type) {
        $form = new SimpleForm(function(Player $player, int $data = null) use ($type){
            $result = $data;
            if($result === null) {
                return;
            }
            $clothesName = $this->clothesTypes[$type];
            if(!array_key_exists($result, $this->clothesDetails[$clothesName])) {
                $this->mainform($player, "");
                return;
            }

            $perms = $this->getConfig()->getNested('perms');
            if(array_key_exists($this->clothesDetails[$clothesName][$result], $perms)) {
                if($player->hasPermission($perms[$this->clothesDetails[$clothesName][$result]])) {
                    $setskin = new setSkin();
                    $setskin->setSkin($player, $this->clothesDetails[$clothesName][$result],$this->clothesTypes[$type]);
                }else {
                    $this->deeperForm($player, "§cYou dont have that cloth!",$type);
                    return;
                }
            }else {
                $setskin = new setSkin();
                $setskin->setSkin($player, $this->clothesDetails[$clothesName][$result], $this->clothesTypes[$type]);
                $player->sendMessage("§aChange cloth successfull");
              }
            });
            $clothesName = $this->clothesTypes[$type];
            $form->setTitle("§0".$clothesName." §aMenu");
            if($this->clothesDetails[$clothesName] != []){
                foreach ($this->clothesDetails[$clothesName] as $value) {
                    $perms = $this->getConfig()->getNested('perms');
                    if(array_key_exists($value, $perms)) {
                        if($player->hasPermission($perms[$value])) {
                            $form->addButton($value, 0, "textures/ui/check");
                        }else {
                            $form->addButton($value, 0, "textures/ui/icon_lock");
                        }
                    }else {
                        $form->addButton($value, 0, "textures/ui/check");
                    }
                }
                $form->setContent($txt);
                $form->addButton("Back", 0, "textures/gui/newgui/undo");
            }else {
                $form->setContent("There is no ".$clothesName." in here currently");
                #chinh lai image undo, va ktra lai nut back xuong duoi cac option
                $form->addButton("Back", 0, "textures/gui/newgui/undo");
            }
        $player->sendForm($form);
        return $form;
    }

    public function resetSkin(Player $player) {
        $player->sendMessage("§aReset to original skin successfull");
        $reset = new resetSkin();
        $reset->setSkin($player);
    }

    public function onJoin(PlayerJoinEvent $e) {
        $name = $e->getPlayer()->getName();
        $skin = $e->getPlayer()->getSkin();

        $saveSkin = new saveSkin();
        $saveSkin->saveSkin($skin, $name);
    }
    
}
