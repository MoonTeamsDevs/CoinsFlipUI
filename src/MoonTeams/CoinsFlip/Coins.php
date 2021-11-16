<?php

namespace MoonTeams\CoinsFlip;

use pocketmine\utils\Config;

class Coins {

    public static function getCoins(): Config{
        return new Config(Main::getInstance()->getDataFolder() . "coins.json", Config::JSON);
    }

    public static function setPlayer(string $player, int $value): void{
        $config = self::getCoins();
        $config->set($player, $value);
        $config->save();
    }

}