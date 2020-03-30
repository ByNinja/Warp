<?php

namespace ojy\warp;

use ojy\coin\Coin;
use ojy\level\SLevel;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use ssss\utils\SSSSUtils;

class Warp
{

    /** @var string */
    protected $warpName;

    /** @var Position|null */
    protected $position = null;

    /** @var string */
    protected $permission;

    /** @var float */
    protected $requireMoney;

    /** @var Item */
    protected $requireItem;

    protected $x;

    protected $y;

    protected $z;

    protected $worldName;

    protected $category = '';

    protected $minLevel = 0;

    /**
     * Warp constructor.
     * @param string $warpName
     * @param int $x
     * @param int $y
     * @param int $z
     * @param string $worldName
     * @param string $permission
     * @param array|null $itemData
     * @param float $money
     * @param int $minLevel
     * @param string $category
     */
    public function __construct(string $warpName, int $x, int $y, int $z, string $worldName,
                                string $permission = Permission::DEFAULT_TRUE, ?array $itemData = null, float $money = 0,
                                int $minLevel = 0, string $category = '')
    {

        $this->category = $category;
        $this->warpName = $warpName;

        Server::getInstance()->loadLevel($worldName);
        if (($world = Server::getInstance()->getLevelByName($worldName)) instanceof Level) {
            $this->position = new Position($x, $y, $z, $world);
        }

        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->worldName = $worldName;

        $this->permission = $permission;

        if ($itemData !== null)
            $itemData = Item::jsonDeserialize($itemData);
        $this->requireItem = $itemData;

        $this->requireMoney = $money;

        $this->minLevel = $minLevel;
    }

    public function hasCoinPlugin(): bool
    {
        if (Server::getInstance()->getPluginManager()->getPlugin("Coin") !== null)
            return true;
        return false;
    }

    /**
     * @param Player $player
     * @param bool $force
     */
    public function warp(Player $player, bool $force = false)
    {
        if ($this->position instanceof Position) {
            if ($force) {
                $player->teleport($this->position);
            } else {
                if (!$this->hasCoinPlugin()) goto skip;
                if (Coin::getCoin($player) >= $this->requireMoney) {
                    skip:
                    if ($this->permission === Permission::DEFAULT_TRUE || $player->hasPermission($this->permission)) {
                        if ($this->requireItem === null || $player->getInventory()->contains($this->requireItem)) {
                            if (Server::getInstance()->getPluginManager()->getPlugin("SLevel") !== null) {
                                if ($this->minLevel > SLevel::getLevel($player)) {
                                    SSSSUtils::message($player, "{$this->getWarpName()} (으)로 이동하려면 레벨이 {$this->getMinLevel()} 이상이어야 합니다.");
                                    return;
                                }
                            }
                            if ($this->requireMoney > 0)
                                if ($this->hasCoinPlugin())
                                    Coin::reduceCoin($player, $this->requireMoney);
                            if ($this->requireItem !== null)
                                $player->getInventory()->removeItem($this->requireItem);
                            $player->teleport($this->position);
                            $player->sendMessage(WarpLoader::PREFIX . "{$this->warpName} (으)로 이동했습니다.");
                            $player->addTitle("§l§6[ §f{$this->warpName} §6]", "§a{$this->warpName}§7 (으)로 워프했습니다.", 5, 14, 5);

                            WarpLoader::getInstance()->getScheduler()->scheduleTask(new ClosureTask(function (int $currentTick) use ($player): void {
                                if ($player instanceof Player && $player->isConnected()) $player->level->addSound(new EndermanTeleportSound($player->getPosition()));
                            }));
                        } else {
                            $player->sendMessage(WarpLoader::PREFIX . "워프에 필요한 아이템이 부족합니다. ({$this->requireItem->getName()} {$this->requireItem->getCount()}개)");
                        }
                    } else {
                        $player->sendMessage(WarpLoader::PREFIX . "이 워프를 이용할 권한이 없습니다.");
                    }
                } else {
                    $player->sendMessage(WarpLoader::PREFIX . "이 워프를 이용하려면 {$this->requireMoney} 코인이 필요합니다.");
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getWarpName(): string
    {
        return $this->warpName;
    }

    /**
     * @return array
     */
    public function serialize(): array
    {
        return [$this->warpName, $this->x, $this->y, $this->z,
            $this->worldName, $this->permission, $this->requireItem !== null ? $this->requireItem->jsonSerialize() : null,
            $this->requireMoney, $this->minLevel, $this->category];
    }

    /**
     * @param array $data
     * @return Warp
     */
    public static function deserialize(array $data): self
    {
        return new Warp(...$data);
    }

    /**
     * @return int
     */
    public function getMinLevel(): int
    {
        return $this->minLevel;
    }

    /**
     * @param int $minLevel
     */
    public function setMinLevel(int $minLevel): void
    {
        $this->minLevel = $minLevel;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }
}