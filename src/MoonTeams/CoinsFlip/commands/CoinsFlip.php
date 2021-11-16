<?php

namespace MoonTeams\CoinsFlip\commands;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use MoonTeams\CoinsFlip\Coins;
use MoonTeams\CoinsFlip\Lang;
use MoonTeams\CoinsFlip\Main;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class CoinsFlip extends Command {

    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            $this->sendCoinsMenu($sender);
        }else{
            $sender->sendMessage(Lang::get("prefix") . Lang::get("not-player"));
            return;
        }
    }

    public function sendCoinsMenu(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            switch ($data){
                case 0:
                    $this->sendCreateCoinsFlip($player);
                    break;
                case 1:
                    $this->sendViewCoinFlip($player);
                    break;
            }
        });
        $ui->setTitle(Lang::get("title"));
        $ui->addButton(Lang::get("create-coins-flip"));
        $ui->addButton(Lang::get("view-coins-flip"));
        $ui->sendToPlayer($player);
    }

    public function sendCreateCoinsFlip(Player $player){
        $ui = new CustomForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            $coins = Main::getInstance()->getConfig()->get("values")[0];
            if (Coins::getCoins()->exists($player->getName())){
                $player->sendMessage(Lang::get("prefix") . Lang::get("already-create"));
                return;
            }
            if (EconomyAPI::getInstance()->myMoney($player) >= $coins){
                EconomyAPI::getInstance()->reduceMoney($player, $coins);
                Coins::setPlayer($player->getName(), $coins);
                $player->sendMessage(Lang::get("prefix") . str_replace(["{coins}"], [$coins], Lang::get("create-with-succes")));
                return;
            }else{
                $player->sendMessage(Lang::get("prefix") . Lang::get("not-have-money"));
                return;
            }
        });
        $ui->setTitle(Lang::get("title"));
        $ui->addDropdown(Lang::get("choose-values"), Main::getInstance()->getConfig()->get("values"));
        $ui->sendToPlayer($player);
    }

    public function sendViewCoinFlip(Player $player){
        $ui = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            if (!empty(Coins::getCoins()->getAll())) {
                $res = [];
                foreach (Coins::getCoins()->getAll() as $ply => $value) {
                    $res[] = ["player" => $ply, "coins" => (float)$value];
                }
                if (!empty($res[$data])){
                    if ($res[$data]["player"] === $player->getName()){
                        $player->sendMessage(Lang::get("prefix") . Lang::get("not-yourself"));
                        return;
                    }
                    EconomyAPI::getInstance()->reduceMoney($player, $res[$data]["coins"]);
                    $rand = mt_rand(1, 2);
                    switch ($rand){
                        case 1:
                            $config = Coins::getCoins();
                            $config->remove($res[$data]["player"]);
                            $config->save();
                            EconomyAPI::getInstance()->addMoney($res[$data]["player"], $res[$data]["coins"] * 2);
                            Server::getInstance()->broadcastMessage(Lang::get("prefix") . str_replace(["{winner}", "{loser}", "{coins}"], [$res[$data]["player"], $player->getName(), $res[$data]["coins"] * 2], Lang::get("win-coins-flip")));
                            break;
                        case 2:
                            $config = Coins::getCoins();
                            $config->remove($res[$data]["player"]);
                            $config->save();
                            EconomyAPI::getInstance()->addMoney($player, $res[$data]["coins"] * 2);
                            Server::getInstance()->broadcastMessage(Lang::get("prefix") . str_replace(["{winner}", "{loser}", "{coins}"], [$player->getName(), $res[$data]["player"], $res[$data]["coins"] * 2], Lang::get("win-coins-flip")));
                            break;
                    }
                }
            }
        });
        $ui->setTitle(Lang::get("title"));
        if (!empty(Coins::getCoins()->getAll())) {
            foreach (Coins::getCoins()->getAll() as $play => $value) {
                $ui->addButton(str_replace(["{player}", "{coins}"], [$play, $value], Lang::get("coins-flip-infos")));
            }
        }else{
            $ui->setContent(Lang::get("no-coins-flip"));
        }
        $ui->sendToPlayer($player);
    }

}