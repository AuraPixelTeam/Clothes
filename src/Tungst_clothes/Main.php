<?php

namespace Tungst_clothes;

use pocketmine\plugin\PluginBase;
use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerJoinEvent;
use Tungst_clothes\skinStuff\saveSkin;
use Tungst_clothes\skinStuff\resetSkin;
use Tungst_clothes\skinStuff\setSkin;
use Tungst_clothes\copyResource\copyResource;
class Main extends PluginBase implements Listener {
    public static $instance;
    public $wing = [],$leftHand = [],$tail =[];
	public function onEnable(){
		self::$instance = $this;	
		$this->getServer()->getPluginManager()->registerEvents($this, $this);	
		$this->checkRequirement();
		
	}
	public function checkRequirement(){
		if (!extension_loaded("gd")) {
            $this->getServer()->getLogger()->error("Clothes: Please uncomment gd2.dll (remove symbol ';' in ';extension=php_gd2.dll') in bin/php/php.ini to help the plugin working");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        if($this->getServer()->getPluginManager()->getPlugin("FormAPI") == null){
	        $this->getServer()->getLogger()->error("Clothes: Install FormAPI plugin to help the plugin working");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        if(!file_exists($this->getDataFolder()."saveskin")){
        	if(file_exists("plugins/Clothes/resources")){
			   $var = new copyResource();
			   $var->recurse_copy("plugins/Clothes/resources",$this->getDataFolder());
		    }else{
		    	$this->getServer()->getLogger()->error("Clothes: Resources not found");
		    	return;
		    }		    
		}
		
		$this->checkAvailableClothes($this->getDataFolder()."wing","wing");
		$this->checkAvailableClothes($this->getDataFolder()."lefthand","lefthand");
		$this->checkAvailableClothes($this->getDataFolder()."tail","tail");
		
        $this->getLogger()->info("§aClothes enable successfull");
	}

    public function onJoin(PlayerJoinEvent $e){
    	$name = $e->getPlayer()->getName();
    	$skin = $e->getPlayer()->getSkin();
    	#if(!file_exists($this->getDataFolder()."/saveskin/".$name.".png"){
    	file_put_contents("steve.json", $e->getPlayer()->getSkin()->getGeometryData());
    	$saveSkin = new saveSkin();
    	$saveSkin->saveSkin($skin,$name);
	}
	public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
        if($sender instanceof Player){
		switch(strtolower($command->getName())){
            case "clo":
            case "clothes":
              $this->mainform($sender,"");
            break;
        }
		}else{
		  $sender->sendMessage("§cOnly work in game");
		};
		return true;
	}
	
	
   public function mainform($player,$txt){
	   $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, int $data = null){
			$result = $data;
			if($result === null){
				return true;
			}
			switch($result){				
					case "0":
                     $this->resetSkin($player);					
					break;	
					case "1":				
					 $this->wing($player,"");
					break;
					case "2":
					 $this->leftHand($player,"");	
					break;
					case "3":
					 $this->tail($player,"");
					break;
					default:
					break;
			}
			});			
			$form->setTitle("§0Clothes §aMenu");
			$form->setContent($txt);
			$form->addButton("Reset Skin",0,"textures/persona_thumbnails/skin_steve_thumbnail_0");
			$form->addButton("Wing",0,"textures/items/light_block_1");
			$form->addButton("LeftHand",0,"textures/items/light_block_6");
			$form->addButton("Tail",0,"textures/items/light_block_9");
			$form->addButton("Exit",0,"textures/ui/redX1");
			$form->sendToPlayer($player);
			return $form;
   } 
   public function resetSkin($player){
	  $player->sendMessage("§aReset to original skin successfull");
	  $reset = new resetSkin();
	  $reset->setSkin($player);
   }
   public function wing($player,$txt){
	    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, int $data = null){
			  $result = $data;
			  if($result === null){
			  	  return true;
			  }
			  if(!array_key_exists($result,$this->wing)){
			  	$this->mainform($player,"");
			  	return;
			  }
			  $perms = $this->getConfig()->getNested('perms');
			  if(array_key_exists($this->wing[$result],$perms)){
			  	if($player->hasPermission($perms[$this->wing[$result]])){
			  		$setskin = new setSkin();
			        $setskin->setSkin($player,$this->wing[$result],"wing");
			  	}else{
			  		$this->wing($player,"§cYou dont have that wing!");
			  		return;
			  	}
			  }else{
			  	$setskin = new setSkin();
			    $setskin->setSkin($player,$this->wing[$result],"wing");
			    $player->sendMessage("§aChange cloth successfull");
			  }
			});			
			$form->setTitle("§0Wing §aMenu");
			if($this->wing != []){
				foreach ($this->wing as $value) {
					$perms = $this->getConfig()->getNested('perms');
					if(array_key_exists($value,$perms)){
	    	           if($player->hasPermission($perms[$value])){
	    	             $form->addButton($value,0,"textures/ui/check");
	    	           }else{
	    	           	$form->addButton($value,0,"textures/ui/icon_lock");
	    	           }
	                }else{
	                	$form->addButton($value,0,"textures/ui/check");
	                }
                }
				$form->setContent($txt);
				$form->addButton("Back",0,"textures/gui/newgui/undo");
			}else{
				$form->setContent("There is no wing currently");
				#chinh lai image undo, va ktra lai nut back xuong duoi cac option
				$form->addButton("Back",0,"textures/gui/newgui/undo");
			}
			$form->sendToPlayer($player);
			return $form; 
   }	
   public function leftHand($player,$txt){
	    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, int $data = null){
			  $result = $data;
			  if($result === null){
			  	  return true;
			  }
			  if(!array_key_exists($result,$this->leftHand)){
			  	$this->mainform($player,"");
			  	return;
			  }
			  $perms = $this->getConfig()->getNested('perms');
			  if(array_key_exists($this->leftHand[$result],$perms)){
			  	if($player->hasPermission($perms[$this->leftHand[$result]])){
			  		$setskin = new setSkin();
			        $setskin->setSkin($player,$this->leftHand[$result],"lefthand");
			  	}else{
			  		$this->leftHand($player,"§cYou dont have that thing!");
			  		return;
			  	}
			  }else{
			  	$setskin = new setSkin();
			    $setskin->setSkin($player,$this->leftHand[$result],"lefthand");
			    $player->sendMessage("§aChange cloth successfull");
			  }
			});			
			$form->setTitle("§0LeftHand §aMenu");
			if($this->leftHand != []){
				foreach ($this->leftHand as $value) {
					$perms = $this->getConfig()->getNested('perms');
					if(array_key_exists($value,$perms)){
	    	           if($player->hasPermission($perms[$value])){
	    	             $form->addButton($value,0,"textures/ui/check");
	    	           }else{
	    	           	$form->addButton($value,0,"textures/ui/icon_lock");
	    	           }
	                }else{
	                	$form->addButton($value,0,"textures/ui/check");
	                }
                }
				$form->setContent($txt);
				$form->addButton("Back",0,"textures/gui/newgui/undo");
			}else{
				$form->setContent("There is nothing currently");
				$form->addButton("Back",0,"textures/gui/newgui/undo");
			}
			$form->sendToPlayer($player);
			return $form; 
   }
   public function tail($player,$txt){
	    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, int $data = null){
			  $result = $data;
			  if($result === null){
			  	  return true;
			  }
			  if(!array_key_exists($result,$this->tail)){
			  	$this->mainform($player,"");
			  	return;
			  }
			  $perms = $this->getConfig()->getNested('perms');
			  if(array_key_exists($this->tail[$result],$perms)){
			  	if($player->hasPermission($perms[$this->tail[$result]])){
			  		$setskin = new setSkin();
			        $setskin->setSkin($player,$this->tail[$result],"tail");
			  	}else{
			  		$this->tail($player,"§cYou dont have that thing!");
			  		return;
			  	}
			  }else{
			  	$setskin = new setSkin();
			    $setskin->setSkin($player,$this->tail[$result],"tail");
			    $player->sendMessage("§aChange cloth successfull");
			  }
			});			
			$form->setTitle("§0Tail §aMenu");
			if($this->tail != []){
				foreach ($this->tail as $value) {
					$perms = $this->getConfig()->getNested('perms');
					if(array_key_exists($value,$perms)){
	    	           if($player->hasPermission($perms[$value])){
	    	             $form->addButton($value,0,"textures/ui/check");
	    	           }else{
	    	           	$form->addButton($value,0,"textures/ui/icon_lock");
	    	           }
	                }else{
	                	$form->addButton($value,0,"textures/ui/check");
	                }
                }
				$form->setContent($txt);
				$form->addButton("Back",0,"textures/gui/newgui/undo");
			}else{
				$form->setContent("There is nothing currently");
				$form->addButton("Back",0,"textures/gui/newgui/undo");
			}
			$form->sendToPlayer($player);
			return $form; 
   }
   public function checkAvailableClothes($dirAddress,$varname){	
   	  $list = scandir($dirAddress);$result = [];
   	  foreach ($list as $value) {
   	  	if(strpos($value,".png")){
   	  		array_push($result,str_replace('.png', '', $value));
   	  	}
   	  }
   	  foreach ($result as $value) {
   	  	if(!in_array($value.".json", $list)){
   	  		unset($result[array_search($value,$result)]);
   	  	}
   	  }
   	  sort($result);
   	  if($varname == "wing"){
   	  	$this->wing = $result;
   	  }else if($varname == "lefthand"){
   	  	$this->leftHand = $result;
   	  }else if($varname == "tail"){
   	  	$this->tail = $result;
   	  }
   }
}