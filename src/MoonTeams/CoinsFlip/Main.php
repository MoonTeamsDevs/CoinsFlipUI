<?php

namespace MoonTeams\CoinsFlip;

use MoonTeams\CoinsFlip\commands\CoinsFlip;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    public static self $instance;

    public static array $coins = [];

    public static function getInstance(): self{
        return self::$instance;
    }

    public function onEnable()
    {
        // Main::getInstance()
        self::$instance = $this;

        if (!file_exists($this->getDataFolder() . "lang.yml")){
            $this->saveResource("lang.yml");
        }

        $this->getServer()->getCommandMap()->registerAll("CoinsFlipUI", [
            new CoinsFlip("coinsflip", Main::getInstance()->getConfig()->get("description"), "/coinsflip", ["cf"])
        ]);
    }

}