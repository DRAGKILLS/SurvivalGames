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
    /**
     * @var SignUpdateTask
     */
    public $signTask;
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
	    $plugin->getScheduler()->scheduleRepeatingTask(new GameTask($this), 20);
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
}
