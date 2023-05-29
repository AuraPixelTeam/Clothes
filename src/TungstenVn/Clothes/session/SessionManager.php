<?php

declare(strict_types=1);

namespace TungstenVn\Clothes\session;

use pocketmine\player\Player;

class SessionManager
{
    private static array $session = [];

    /**
     * @param Player $player
     * @return bool|null
     */
    public static function getPlayer(Player $player): ?bool {
        $playerName = strtolower($player->getName());
        return self::$session[$playerName];
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function addPlayer(Player $player): void {
        $playerName = strtolower($player->getName());
        self::$session[$playerName] = true;
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function removePlayer(Player $player): void {
        $playerName = strtolower($player->getName());
        if (self::getPlayer($player) !== null) {
            unset(self::$session[$playerName]);
        }
    }
}