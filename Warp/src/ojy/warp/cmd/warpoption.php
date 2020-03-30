<?php

namespace ojy\warp\cmd;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class warpoption extends Command
{

    public function __construct()
    {
        parent::__construct("워프옵션", "워프옵션을 추가합니다.", "/워프옵션 [워프이름] [옵션] ...", ["warpoption"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        // TODO: Implement execute() method.
    }
}