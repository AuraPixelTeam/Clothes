<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\listener;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\player\Player;
use TungstenVn\Clothes\Clothes;
use TungstenVn\Clothes\session\SessionManager;
use TungstenVn\Clothes\skinStuff\saveSkin;

class EventListener implements Listener
{

    public function getLoader(): Clothes{
        return Clothes::getInstance();
    }

    /**
     * onHitEntity function
     *
     * @param EntityDamageByEntityEvent $ev
     * @return void
     */
    public function onHitEntity(EntityDamageByEntityEvent $ev): void
    {
        $entity = $ev->getEntity();
        $player = $ev->getDamager();
        $clothes = $this->getLoader();
        if($player instanceof Player){
            if(SessionManager::getPlayer($player)){
                if($entity instanceof Human && !$entity instanceof Player){
                    $entity->setSkin($player->getSkin());
                    $entity->sendSkin();
                    SessionManager::removePlayer($player);
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
    public function onDataReceive(DataPacketReceiveEvent $ev): void
    {
        $clothes = $this->getLoader();
        if ($ev->getPacket() instanceof LoginPacket) {
            $data = JwtUtils::parse($ev->getPacket()->clientDataJwt);
            $name = $data[1]["ThirdPartyName"];
            if ($data[1]["PersonaSkin"]) {
                if (!file_exists($clothes->getDataFolder() . "saveskin")) {
                    mkdir($clothes->getDataFolder() . "saveskin", 0777);
                }
                copy($clothes->getDataFolder()."steve.png",$clothes->getDataFolder() . "saveskin/$name.png");
                return;
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
    public function onQuit(PlayerQuitEvent $ev): void
    {
        $name = $ev->getPlayer()->getName();
        $clothes = $this->getLoader();
        unset($clothes->nannyQueue[$name]);

        $willDelete = $clothes->getConfig()->getNested('DeleteSkinAfterQuitting');
        if ($willDelete) {
            if (file_exists($clothes->getDataFolder() . "saveskin/$name.png")) {
                unlink($clothes->getDataFolder() . "saveskin/$name.png");
            }
        }
    }
}