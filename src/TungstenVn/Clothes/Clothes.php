<?php

namespace TungstenVn\Clothes;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use TungstenVn\Clothes\checkStuff\checkClothes;
use TungstenVn\Clothes\checkStuff\checkRequirement;
use TungstenVn\Clothes\form\clothesForm;
use TungstenVn\Clothes\form\cosplaysForm;
use TungstenVn\Clothes\skinStuff\saveSkin;

class Clothes extends PluginBase implements Listener
{
    /** @var self $instance */
    public static $instance;

    // sth like ["wing","lefthand"]:
    public $clothesTypes = [];
    public $cosplaysTypes = [];
    //something like ["wing" =>["wing1","wing2"]]:
    public $clothesDetails = [];
    public $cosplaysDetails = [];
    //Player who is using /nanny will be in here
    private $nannyQueue = [];



    public function onEnable()
    {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $a = new checkRequirement();
        $a->checkRequirement();

        $a = new checkClothes();
        $a->checkClothes();
        $a->checkCos();

        $config = $this->getConfig();
        if ($config->getNested("enableUpdateChecker") != false) {
            $this->getServer()->getAsyncPool()->submitTask(new checkUpdate());
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($sender instanceof Player) {        
            switch (strtolower($command->getName())) {
                case "clo":
                case "clothes":
                    $form = new clothesForm($this);
                    $form->mainform($sender, "");
                    break;
                case "cos":
                case "cosplay":
                    $form = new cosplaysForm($this);
                    $form->mainform($sender, "");
                    break;
                case "nanny":
                    $this->nannyQueue[$sender->getName()] = "OK";
                    $sender->sendMessage("§aTap a slapper to change skin");
                    break;
            }
        } else {
            $sender->sendMessage("§cOnly work in game");
        }
        return true;
    }
    public function onHitEntity(EntityDamageByEntityEvent $ev){
        $entity = $ev->getEntity();
        $player = $ev->getDamager();
        if($player instanceof Player){
            if(array_key_exists($player->getName(),$this->nannyQueue)){
                if($entity instanceof Human and !$entity instanceof Player){
                    $entity->setSkin($player->getSkin());
                    $entity->sendSkin();
                    unset($this->nannyQueue[$player->getName()]);
                    $player->sendMessage("§aSuccessfully changing entity skin");
                }
            }
        }
    }
    public function dataReceiveEv(DataPacketReceiveEvent $ev)
    {
        if ($ev->getPacket() instanceof LoginPacket) {
            $data = $ev->getPacket()->clientData;
            $name = $data["ThirdPartyName"];
            if ($data["PersonaSkin"]) {             
                if (!file_exists($this->getDataFolder() . "saveskin")) {
                    mkdir($this->getDataFolder() . "saveskin", 0777);
                }
                copy($this->getDataFolder()."steve.png",$this->getDataFolder() . "saveskin/$name.png");
                return;
            }
            if ($data["SkinImageHeight"] == 32) {           
            }
            $saveSkin = new saveSkin();
            $saveSkin->saveSkin(base64_decode($data["SkinData"], true), $name);
        }
    }

    public function onQuit(PlayerQuitEvent $ev)
    {
        $name = $ev->getPlayer()->getName();
        unset($this->nannyQueue[$name]);

        $willDelete = $this->getConfig()->getNested('DeleteSkinAfterQuitting');
        if ($willDelete) {
            if (file_exists($this->getDataFolder() . "saveskin/$name.png")) {
                unlink($this->getDataFolder() . "saveskin/$name.png");
            }
        }
    }
}
