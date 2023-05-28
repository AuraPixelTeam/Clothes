<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use TungstenVn\Clothes\Clothes as Loader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use TungstenVn\Clothes\form\cosplaysForm;

class CosplaysCommand extends Command implements PluginOwned
{
    public function __construct() {
        parent::__construct(
            name: "cosplays",
            description: "Clothes command",
            usageMessage: "/cosplays",
            aliases: [
                "cos"
            ]
        );
        $this->setPermission("cosplays.command");
    }


    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            return;
        }
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cPlease use this command in-game");
            return;
        }
        $form = new cosplaysForm();
        $form->send($sender);
    }

    /**
     * @return Plugin
     */
    public function getOwningPlugin(): Plugin
    {
        return Loader::getInstance();
    }
}