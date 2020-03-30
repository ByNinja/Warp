<?php

namespace ojy\warp;

use ojy\warp\cmd\addwarp;
use ojy\warp\cmd\removewarp;
use ojy\warp\cmd\shortcut;
use ojy\warp\cmd\warpcategory;
use ojy\warp\cmd\warplevel;
use ojy\warp\cmd\warplist;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class WarpLoader extends PluginBase
{

    /** @var string */
    public const PREFIX = "§l§b[워프] §r§7";

    /** @var WarpLoader */
    private static $i;

    /** @var Warp[] */
    private static $warps = [];

    /** @var Config */
    private static $data;

    /** @var shortcut[] */
    private static $shortcuts = [];

    public function onLoad()
    {
        self::$i = $this;
    }

    /**
     * @return WarpLoader
     */
    public static function getInstance(): self
    {
        return self::$i;
    }


    public function onEnable()
    {
        self::$data = new Config($this->getDataFolder() . "WarpData.yml", Config::YAML, []);

        foreach (self::$data->getAll() as $data) {
            $warp = Warp::deserialize($data);
            self::$warps[$warp->getWarpName()] = $warp;
            $shortcut = new shortcut($warp->getWarpName());
            self::$shortcuts[$warp->getWarpName()] = $shortcut;
            Server::getInstance()->getCommandMap()->register("MineWarp", $shortcut);
        }

        foreach ([addwarp::class, removewarp::class, warplist::class, warplevel::class, warpcategory::class] as $c) {
            Server::getInstance()->getCommandMap()->register("MineWarp", new $c);
        }
    }


    /**
     * @return string[]
     */
    public static function getCategories(): array
    {
        $res = [];
        foreach (array_values(self::$warps) as $warp) {
            if (!in_array($warp->getCategory(), $res))
                $res[] = $warp->getCategory();
        }
        return $res;
    }

    /**
     * @param string $category
     * @return Warp[]
     */
    public static function getWarpsByCategory(string $category): array
    {
        $res = [];
        foreach (array_values(self::$warps) as $warp) {
            if ($category === $warp->getCategory())
                $res[] = $warp;
        }
        return $res;
    }

    /**
     * @return Warp[]
     */
    public static function getAllWarp(): array
    {
        return array_values(self::$warps);
    }

    /**
     * @param string $warpName
     * @return Warp|null
     */
    public static function getWarp(string $warpName): ?Warp
    {
        return isset(self::$warps[$warpName]) ? self::$warps[$warpName] : null;
    }

    /**
     * @param string $warpName
     * @param Position $position
     * @return bool
     */
    public static function addWarp(string $warpName, Position $position): bool
    {
        if (!isset(self::$warps[$warpName])) {
            self::$warps[$warpName] = new Warp($warpName, $position->x, $position->y, $position->z, $position->level->getFolderName());
            $shortcut = new shortcut($warpName);
            self::$shortcuts[$warpName] = $shortcut;
            Server::getInstance()->getCommandMap()->register("MineWarp", $shortcut);
            return true;
        }
        return false;
    }

    /**
     * @param string $warpName
     * @return bool
     */
    public static function removeWarp(string $warpName): bool
    {
        if (isset(self::$warps[$warpName])) {
            Server::getInstance()->getCommandMap()->unregister(self::$shortcuts[$warpName]);
            unset(self::$warps[$warpName]);
            unset(self::$shortcuts[$warpName]);
            return true;
        }
        return false;
    }

    public function onDisable()
    {
        $data = [];
        foreach (array_values(self::$warps) as $warp) {
            $data[] = $warp->serialize();
        }
        self::$data->setAll($data);
        self::$data->save();
    }
}