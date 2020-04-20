<?php

namespace ojy\money\cmd;

use ojy\money\MoneyPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use ssss\utils\SSSSUtils;

class SetMoneyCommand extends Command
{

    public function __construct()
    {
        parent::__construct('돈설정', '유저의 돈을 설정합니다.', '/돈설정 (플레이어) (돈)', ['setmoney']);
        $this->setPermission ('OP');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (! $sender->hasPermission ($this->getPermission()))
        {
            SSSSUtils::message($sender, "명령어 사용 권한이 없습니다.");
            return;
        }
        if (! isset ($args[1]))
        {
            SSSSUtils::message($sender, "돈설정 (플레이어) (돈) | 유저의 돈을 설정합니다.");
            return;
        }
        if (! is_numeric ($args[1]))
        {
            SSSSUtils::message($sender, "돈은 숫자로 입력해주세요.");
            return;
        }
        if ($args[1] < 0)
        {
            SSSSUtils::message($sender, "돈을 음수로 설정할 수 없습니다.");
            return;
        }
        $beforeMoney = MoneyPlugin::getMoney ($args[0]);
        if ($beforeMoney < 0)
        {
            SSSSUtils::message($sender, "{$args[0]}(이)라는 유저는 서버에 접속한 적이 없습니다.");
            return;
        }
        MoneyPlugin::setMoney ($args[0], (float) $args[1]);
        if (($target = Server::getInstance()->getPlayerExact ($args[0])) instanceof Player)
        {
            SSSSUtils::message($target, "돈이 " . number_format ($beforeMoney) . ($unit = MoneyPlugin::getMoneyUnit()) . "에서 " . number_format ($args[1]) . $unit . "(으)로 설정되었습니다.");
        }
    }
}
