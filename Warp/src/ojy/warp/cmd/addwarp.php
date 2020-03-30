<?php

namespace ojy\warp\cmd;

use ojy\warp\WarpLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;

class addwarp extends Command
{

    public function __construct()
    {
        parent::__construct("워프생성", "해당 자리에 워프를 추가합니다.", "/워프생성 [워프이름]", ["워프추가", "addwarp"]);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if ($sender->hasPermission($this->getPermission())) {
                if (isset($args[0])) {
                    $warpName = implode(" ", $args);
                    if (WarpLoader::addWarp($warpName, $sender->getPosition())) {
                        $sender->sendMessage(WarpLoader::PREFIX . "§7\"{$warpName}§r§7\" 워프를 추가했습니다.");
                    } else {
                        $sender->sendMessage(WarpLoader::PREFIX . "이미 존재하는 워프입니다.");
                    }
                } else {
                    $sender->sendMessage(WarpLoader::PREFIX . $this->getUsage());
                }
            } else {
                $sender->sendMessage(WarpLoader::PREFIX . "이 명령어를 실행할 권한이 없습니다.");
            }
        } else {
            $sender->sendMessage(WarpLoader::PREFIX . "인게임에서 실행하세요.");
        }
    }
}