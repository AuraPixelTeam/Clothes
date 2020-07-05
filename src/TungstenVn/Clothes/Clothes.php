<?php

namespace TungstenVn\Clothes;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

use TungstenVn\Clothes\form\clothesForm;
use TungstenVn\Clothes\form\cosplaysForm;
use TungstenVn\Clothes\skinStuff\saveSkin;
use TungstenVn\Clothes\checkStuff\checkRequirement;
use TungstenVn\Clothes\checkStuff\checkClothes;
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

    public function onEnable()
    {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $a = new checkRequirement();
        $a->checkRequirement();

        $a = new checkClothes();
        $a->checkClothes();
        $a->checkCos();
    }

    public function onCommand(CommandSender $sender, Command $command, String $label, array $args): bool
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
            }
        } else {
            $sender->sendMessage("§cOnly work in game");
        }
        return true;
    }

    public function onJoin(PlayerJoinEvent $e)
    {
        $name = $e->getPlayer()->getName();
        $skin = $e->getPlayer()->getSkin();
        if (preg_replace('/\s+/', '', $skin->getGeometryData()) != preg_replace('/\s+/', '', file_get_contents($this->getDataFolder() . "steve.json"))) {
            $this->getServer()->broadcastMessage("§bClothes: §rPlayer §6" . strtolower($name) . "§r is using default/4d skin");
        }elseif (strlen($skin->getSkinData()) == 8192) {
            $this->getServer()->broadcastMessage("§bClothes: §rPlayer §6" . strtolower($name) . "§r is using 64x32 skin size");
        }
        $saveSkin = new saveSkin();
        $saveSkin->saveSkin($skin, $name);
    }
}
