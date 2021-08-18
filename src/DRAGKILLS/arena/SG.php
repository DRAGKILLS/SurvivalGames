<?php

/**
 *  ____  ____      _    ____ _  _____ _     _     ____
 * |  _ \|  _ \    / \  / ___| |/ /_ _| |   | |   / ___|
 * | | | | |_) |  / _ \| |  _| ' / | || |   | |   \___ \
 * | |_| |  _ <  / ___ \ |_| | . \ | || |___| |___ ___) |
 * |____/|_| \_\/_/   \_\____|_|\_\___|_____|_____|____/
 *
 * Copyright 2019/2021 DRAGKILLS
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Created by PhpStorm.
 * User: DRAGKILLS
 * Date: 7/9/2021
 * Time: 12:56 PM
 */


declare(strict_types=1);

namespace DRAGKILLS\arena;


use DRAGKILLS\SurvivalGames;
use DRAGKILLS\tasks\GameTask;
use DRAGKILLS\tasks\SignUpdateTask;
use pocketmine\event\Listener;
use pocketmine\level\Level;
use pocketmine\Player;

/**
 * Class SG
 * @package DRAGKILLS\arena
 */
class SG implements Listener
{

    /**
     * @var SurvivalGames
     */
    public $plugin;
    /**
     * @var Level
     */
    public $level;
    /**
     * @var array|string[]
     */
    public $data;
    /**
     * @var Player $players
     */
    public $players = [];

    public $phase;

    const GAME_LOBBY = 1;
    const GAME_STARING = 2;
    const GAME_RESTARTING = 3;
    const GAME_PVP = 4;
    const GAME_STARTED = 5;
    const TYPE_TITLE = 404;
    const TYPE_MESSAGE = 504;
    const TYPE_TIP = 403;
    const TYPE_POPUP = 100;
    /**
     * @var GameTask
     */
    public $gameTask;

    public function __construct(SurvivalGames $plugin, Level $level)
    {
        $this->plugin = $plugin;
        $this->level = $level;
        $arenaFileConfig = $plugin->getArenaFile($level->getFolderName());
        $this->data = $arenaFileConfig->getAll(\false);
	    $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	    $plugin->getScheduler()->scheduleRepeatingTask(new SignUpdateTask($this), 20);
	    $plugin->getScheduler()->scheduleRepeatingTask($this->gameTask = new GameTask($this), 20);
	    $this->phase = 0;
    }

    public function setMaxPlayers(int $maxPlayers)
    {
        $this->data["maxplayers"] = $maxPlayers;
    }

    public function setSpawn(int $message, Player $player)
    {
        if($this->data["maxplayers"] < $message){
            $player->sendMessage("spawns need to be like maxplayers");
            return;
        }
        $this->data["spawn-{$message}"] = [
            "x" => $player->getX(),
            "y" => $player->getY(),
            "x" => $player->getZ()
        ];
    }

    public function joinToArena(Player $player)
    {
        if($this->isPlaying($player)){
            $player->sendMessage("You already in arena");
            return;
        }
        if(!count($this->players) <= $this->data["maxplayers"]){
            $player->sendMessage("Arena is full");
            return;
        }
        if($this->phase == self::GAME_STARTED || $this->phase == self::GAME_RESTARTING || $this->phase == self::GAME_PVP){
            $player->sendMessage("Game is started");
            return;
        }
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getCursorInventory()->clearAll();
        $player->setFireTicks(0);
        $player->setBreathing(true);
        $player->setGamemode(2);
        $player->setImmobile(true);
        $this->broadcast("{$player->getName()} joined! [" . count($this->players) . "/" . $this->data["maxplayers"] . "]");
        $this->players[$player->getName()] = $player;
        $this->phase = self::GAME_LOBBY;
    }

    public function leaveFromArena(Player $player){
        if(!$this->isPlaying($player))return;
        if ($this->phase == self::GAME_STARTED || $this->phase == self::GAME_RESTARTING || $this->phase == self::GAME_PVP)return;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getCursorInventory()->clearAll();
        $player->setFireTicks(0);
        $player->setBreathing(true);
        $player->setGamemode($this->plugin->getServer()->getDefaultGamemode());
        $player->setImmobile(false);
        $this->broadcast("{$player->getName()} leave! [" . count($this->players) . "/" . $this->data["maxplayers"] . "]");
        unset($this->players[$player->getName()]);
        if(count($this->players) < $this->data["maxplayers"]){
            $this->gameTask->resetTime();
        }
    }

    public function isPlaying(Player $player): bool
    {
        return isset($this->players[$player->getName()]);
    }

    public function startGame()
    {
        foreach ($this->players as $player){
            $player->setImmobile(false);
        }
        $this->broadcast("Game Started!", self::TYPE_TITLE);
        $this->phase = self::GAME_STARTED;
    }

    public function broadcast(string $message, int $type = self::TYPE_MESSAGE){
        switch ($type){
            case self::TYPE_MESSAGE:
                foreach ($this->players as $player){
                    $player->sendMessage($message);
                }
                break;
            case self::TYPE_TITLE:
                foreach ($this->players as $player){
                    $player->addTitle($message);
                }
                break;
            case self::TYPE_POPUP:
                foreach ($this->players as $player){
                    $player->sendPopup($message);
                }
                break;
            case self::TYPE_TIP:
                foreach ($this->players as $player){
                    $player->sendTip($message);
                }
                break;
            default:
                $this->plugin->getLogger()->error("UNKNOWN MESSAGE TYPE");
                break;
        }
    }
}
