<?php

namespace ojy\money\cmd;

use ojy\money\MoneyPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use ssss\utils\SSSSUtils;

class MyMoneyCommand extends Command
{

    public function __construct()
    {
        parent::__construct('내돈', '내 돈 현황을 확인합니다.', '/내돈', ['mymoney']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $t = count(MoneyPlugin::$data->getAll());
            $r = MoneyPlugin::getRank($sender);
            SSSSUtils::message($sender, "{$t}명 중 {$r}위");
            SSSSUtils::message($sender, '보유중인 돈: ' . MoneyPlugin::getMoney($sender) . MoneyPlugin::getMoneyUnit());
        }
    }
}