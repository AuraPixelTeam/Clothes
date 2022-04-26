<?php

namespace TungstenVn\Clothes;

use pocketmine\command\{Command, CommandSender};
use pocketmine\entity\Human;
use pocketmine\event\{
    Listener,
    player\PlayerQuitEvent,
    server\DataPacketReceiveEvent,
    entity\EntityDamageByEntityEvent
};
use pocketmine\network\mcpe\{
    JwtUtils,
    protocol\LoginPacket
};
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use TungstenVn\Clothes\checkStuff\{
    checkClothes,
    checkRequirement,
};
use TungstenVn\Clothes\form\{
    clothesForm,
    cosplaysForm
};
use TungstenVn\Clothes\skinStuff\saveSkin;
use TungstenVn\Clothes\updater\GetUpdateInfo;

class Clothes extends PluginBase implements Listener
{
    /** @var self $instance */
    public static $instance;

    public array $clothesTypes = [];
    public array $cosplaysTypes = []; 
    public array $clothesDetails = [];
    public array $cosplaysDetails = [];

    private array $nannyQueue = [];

    public function onEnable(): void
    {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $a = new checkRequirement();
        $a->checkRequirement();

        $a = new checkClothes();
        $a->checkClothes();
        $a->checkCos();

        $this->checkUpdater();
    }

    protected function checkUpdater() : void {
        $this->getServer()->getAsyncPool()->submitTask(new GetUpdateInfo($this, "https://raw.githubusercontent.com/AngelliaX/Clothes/master/poggit_news.json"));
    }
    /**
     * onCommand function
     *
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return boolean
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($sender instanceof Player) {        
            switch (strtolower($command->getName())) {
                case "clothes":
                    $form = new clothesForm($this);
                    $form->mainform($sender, "");
                    break;
                case "cosplays":
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
    /**
     * onHitEntity function
     *
     * @param EntityDamageByEntityEvent $ev
     * @return void
     */
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
    /**
     * dataReceiveEv function
     *
     * @param DataPacketReceiveEvent $ev
     * @return void
     */
    public function dataReceiveEv(DataPacketReceiveEvent $ev)
    {
        if ($ev->getPacket() instanceof LoginPacket) {
            $data = JwtUtils::parse($ev->getPacket()->clientDataJwt);
            $name = $data[1]["ThirdPartyName"];
            if ($data[1]["PersonaSkin"]) {             
                if (!file_exists($this->getDataFolder() . "saveskin")) {
                    mkdir($this->getDataFolder() . "saveskin", 0777);
                }
                copy($this->getDataFolder()."steve.png",$this->getDataFolder() . "saveskin/$name.png");
                return;
            }
            if ($data[1]["SkinImageHeight"] == 32) {           
            }
            $saveSkin = new saveSkin();
            $saveSkin->saveSkin(base64_decode($data[1]["SkinData"], true), $name);
        }
    }
    /**
     * onQuit function
     *
     * @param PlayerQuitEvent $ev
     * @return void
     */
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

    public static function getInstance(): Clothes{
        return self::$instance;
    }

    public function getFileHack() {
        return $this->getFile();
    }
}
