<?php

namespace MoonTeams\CoinsFlip;

use pocketmine\utils\Config;

class Lang {

    public static function getLang(): Config{
        return new Config(Main::getInstance()->getDataFolder() . "lang.yml", Config::YAML);
    }

    public static function get(string $value){
        if (self::getLang()->exists($value)){
            return self::getLang()->get($value);
        }
        return null;
    }

}