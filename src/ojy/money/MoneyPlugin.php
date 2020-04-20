<?php

namespace ojy\money;

use ojy\money\cmd\MyMoneyCommand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class MoneyPlugin extends PluginBase implements Listener
{

    /** @var Config */
    public static $config;

    /** @var Config */
    public static $data;

    /** @var self */
    public static $instance;

    public function onLoad()
    {
        self::$instance = $this;
    }

    public static function getConfigFile(): Config
    {
        return new Config(self::$instance->getDataFolder() . 'Settings.yml', Config::YAML);
    }

    public static function getDataFile(): Config
    {
        return new Config(self::$instance->getDataFolder() . 'Moneys.yml', Config::YAML);
    }

    public function onEnable()
    {
        self::$config = new Config($this->getDataFolder() . 'Settings.yml', Config::YAML, [
            'money-unit' => '러쉬',
            'default-money' => 1000
        ]);
        self::$data = new Config($this->getDataFolder() . 'Moneys.yml', Config::YAML, []);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        foreach ([
                     MyMoneyCommand::class
                 ] as $c)
            Server::getInstance()->getCommandMap()->register('MoneyPlugin', new $c);
    }

    public function onLogin(PlayerLoginEvent $event)
    {
        if (self::existsCheck($event->getPlayer())) {
            Server::getInstance()->getLogger()->info("§b{$event->getPlayer()->getName()}§f 님의 돈 데이터를 생성했습니다.");
        }
    }

    public static function getRank(Player $player): int
    {
        return self::getRankForName($player->getName(), false);
    }

    public static function getRankForName(string $playerName, bool $check = true): int
    {
        if ($check)
            if (($player = Server::getInstance()->getPlayer($playerName)))
                $playerName = $player->getName();

        $playerName = strtolower($playerName);
        $dataF = self::getDataFile();
        if ($dataF->exists($playerName)) {
            $data = $dataF->getAll();
            arsort($data);
            return array_search($playerName, array_keys($data)) + 1;
        }
        return -1;
    }

    public static function getMoney(Player $player): float
    {
        return self::getMoneyForName($player->getName(), false);
    }

    public static function getMoneyForName(string $playerName, bool $check = true): float
    {
        if ($check)
            if (($player = Server::getInstance()->getPlayer($playerName)))
                $playerName = $player->getName();

        $playerName = strtolower($playerName);
        $dataF = self::getDataFile();
        if ($dataF->exists($playerName)) {
            return $dataF->get($playerName);
        }
        return -1;
    }

    public static function setMoney(Player $player, float $money): bool
    {
        return self::setMoneyForName($player->getName(), $money, false);
    }

    public static function setMoneyForName(string $playerName, float $money, bool $check = true): bool
    {
        if ($check)
            if (($player = Server::getInstance()->getPlayer($playerName)))
                $playerName = $player->getName();

        $playerName = strtolower($playerName);
        $dataF = self::getDataFile();
        if ($dataF->exists($playerName)) {
            if ($money < 0)
                $money = 0;
            $dataF->set($playerName, $money);
            $dataF->save();
            return true;
        }
        return false;
    }

    public static function reduceMoney(Player $player, float $money): bool
    {
        return self::reduceMoneyForName($player->getName(), $money, false);
    }

    public static function reduceMoneyForName(string $playerName, float $money, bool $check = true)
    {
        if ($check)
            if (($player = Server::getInstance()->getPlayer($playerName)))
                $playerName = $player->getName();

        $playerName = strtolower($playerName);
        $dataF = self::getDataFile();
        if ($dataF->exists($playerName)) {
            $beforeMoney = $dataF->get($playerName);
            $afterMoney = $beforeMoney - $money;
            if ($afterMoney < 0)
                $afterMoney = 0;
            $dataF->set($playerName, $afterMoney);
            $dataF->save();
            return true;
        }
        return false;
    }

    public static function addMoney(Player $player, float $money): bool
    {
        return self::addMoneyForName($player->getName(), $money, false);
    }

    public static function addMoneyForName(string $playerName, float $money, bool $check = true): bool
    {
        if ($check)
            if (($player = Server::getInstance()->getPlayer($playerName)))
                $playerName = $player->getName();

        $playerName = strtolower($playerName);
        $dataF = self::getDataFile();
        if ($dataF->exists($playerName)) {
            $beforeMoney = $dataF->get($playerName);
            $afterMoney = $beforeMoney + $money;
            if ($afterMoney < 0)
                $afterMoney = 0;
            $dataF->set($playerName, $afterMoney);
            $dataF->save();
            return true;
        }
        return false;
    }

    public static function existsCheck(Player $player): bool
    {
        $playerName = strtolower($player->getName());
        $dataF = self::getDataFile();
        if (!$dataF->get($playerName)) {
            $dataF->set($playerName, self::getDefaultMoney());
            $dataF->save();
            return true;
        }
        return false;
    }

    public static function getDefaultMoney(): float
    {
        return floatval(self::getConfigFile()->get('default-money'));
    }

    public static function getMoneyUnit(): string
    {
        return self::getConfigFile()->get('money-unit');
    }
}
