<?php

declare(strict_types=1);

namespace TungstenVn\Clothes;

use pocketmine\plugin\PluginBase;
use TungstenVn\Clothes\checkStuff\checkClothes;
use TungstenVn\Clothes\checkStuff\checkRequirement;
use TungstenVn\Clothes\commands\ClothesCommand;
use TungstenVn\Clothes\commands\CosplaysCommand;
use TungstenVn\Clothes\commands\NannyCommand;
use pocketmine\utils\SingletonTrait;
use TungstenVn\Clothes\listener\EventListener;
use TungstenVn\Clothes\updater\CheckUpdateTask;

class Clothes extends PluginBase
{
    use SingletonTrait;

    public array $clothesTypes = [];
    public array $cosplaysTypes = []; 
    public array $clothesDetails = [];
    public array $cosplaysDetails = [];

    public function onEnable(): void
    {
        self::setInstance($this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $a = new checkRequirement();
        $a->checkRequirement();

        $a = new checkClothes();
        $a->checkClothes();
        $a->checkCos();

        $this->getServer()->getCommandMap()->register("clothes", new ClothesCommand());
        $this->getServer()->getCommandMap()->register("cosplays", new CosplaysCommand());
        $this->getServer()->getCommandMap()->register("nanny", new NannyCommand());

        $this->checkUpdater();
    }

    protected function checkUpdater() : void {
        $this->getServer()->getAsyncPool()->submitTask(new CheckUpdateTask(
            $this->getDescription()->getName(),
            $this->getDescription()->getVersion())
        );
    }
}
