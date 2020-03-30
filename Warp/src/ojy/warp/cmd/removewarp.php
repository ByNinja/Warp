<?php

namespace ojy\warp\cmd;

use ojy\warp\WarpLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;

class removewarp extends Command
{

    public function __construct()
    {
        parent::__construct("워프제거", "워프를 제거합니다.", "/워프제거 [워프이름]", ["워프삭제", "removewarp"]);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission($this->getPermission())) {
            if (isset($args[0])) {
                $warpName = implode(" ", $args);
                if (WarpLoader::removeWarp($warpName)) {
                    $sender->sendMessage(WarpLoader::PREFIX . "성공적으로 \"{$warpName}§r§7\" 워프를 제거했습니다.");
                } else {
                    $sender->sendMessage(WarpLoader::PREFIX . "존재하지 않는 워프입니다.");
                }
            } else {
                $sender->sendMessage(WarpLoader::PREFIX . $this->getUsage());
            }
        } else {
            $sender->sendMessage(WarpLoader::PREFIX . "이 명령어를 실행할 권한이 없습니다.");
        }
    }
}