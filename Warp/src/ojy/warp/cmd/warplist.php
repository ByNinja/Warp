<?php

namespace ojy\warp\cmd;

use ojy\warp\Warp;
use ojy\warp\WarpLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class warplist extends Command
{


    public function __construct()
    {
        parent::__construct("워프목록", "워프목록을 확인합니다.", "/워프목록 [페이지]", ["warplist"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $all = WarpLoader::getAllWarp();
        if (count($all) > 0) {
            $maxPage = ceil(count($all) / 6);
            if (!isset($args[0]))
                $args[0] = 1;
            if (is_numeric($args[0])) {
                $page = ceil($args[0]);
                if ($page > $maxPage)
                    $page = $maxPage;
                $index1 = $page * 6 - 1;
                $index2 = $page * 6 - 6;
                $c = 0;
                $sender->sendMessage(WarpLoader::PREFIX . "워프목록을 표시합니다. ({$page}/{$maxPage})");
                foreach ($all as $warp)
                    if ($warp instanceof Warp)
                        if ($index1 >= $c && $index2 <= $c)
                            $sender->sendMessage("§a> §7{$warp->getWarpName()}");
            } else {
                $sender->sendMessage(WarpLoader::PREFIX . "페이지는 숫자로 써주세요.");
            }
        } else {
            $sender->sendMessage(WarpLoader::PREFIX . "워프가 존재하지 않습니다.");
        }
    }
}