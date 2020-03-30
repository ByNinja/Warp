<?php

namespace ojy\warp\cmd;

use ojy\warp\Warp;
use ojy\warp\WarpLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class shortcut extends Command
{

    /** @var Warp */
    protected $warp;

    public function __construct(string $name)
    {
        parent::__construct($name, "{$name}으로 워프하는 명령어입니다.", "/{$name}");
        $this->warp = WarpLoader::getWarp($name);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $this->warp->warp($sender);
        } else {
            $sender->sendMessage(WarpLoader::PREFIX . "인게임에서 실행하세요.");
        }
    }
}