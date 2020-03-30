<?php

namespace ojy\warp\cmd;

use ojy\warp\Warp;
use ojy\warp\WarpLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use ssss\utils\SSSSUtils;

class warpcategory extends Command
{

    public function __construct()
    {
        parent::__construct("워프카테고리", "워프의 카테고리를 설정합니다.", "/워프카테고리 [워프이름] [카테고리]", []);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission($this->getPermission())) {
            if (isset($args[1])) {
                $warpName = $args[0];
                if (($warp = WarpLoader::getWarp($warpName)) instanceof Warp) {
                    $category = $args[1];
                    $warp->setCategory($category);
                    SSSSUtils::message($sender, "{$warpName} 워프의 카테고리를 {$category}(으)로 설정했습니다.");
                } else {
                    SSSSUtils::message($sender, "{$warpName} 워프를 찾을 수 없습니다.");
                }
            } else {
                SSSSUtils::message($sender, $this->getUsage());
            }
        }
    }
}