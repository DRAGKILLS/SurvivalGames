<?php

/**
 *  ____  ____      _    ____ _  _____ _     _     ____
 * |  _ \|  _ \    / \  / ___| |/ /_ _| |   | |   / ___|
 * | | | | |_) |  / _ \| |  _| ' / | || |   | |   \___ \
 * | |_| |  _ <  / ___ \ |_| | . \ | || |___| |___ ___) |
 * |____/|_| \_\/_/   \_\____|_|\_\___|_____|_____|____/
 *
 * Copyright 2019/2022 DRAGKILLS
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
 * Time: 12:50 PM
 */


declare(strict_types=1);

namespace DRAGKILLS;


use DRAGKILLS\arena\SG;
use DRAGKILLS\commands\SGCommands;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\item\Sign;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use function in_array;

/**
 * Class SurvivalGames
 * @package DRAGKILLS
 */
class SurvivalGames extends PluginBase implements Listener
{

    /** @var SG[] $arenas */
    public $arenas = [];

    /** @var SG[] $editors */
    public $editors = [];

    public $setup = [];

    public function onEnable(): void
    {
	@mkdir($this->getDataFolder(), 0777, true);
	if(!is_dir($this->getDataFolder()."arenas")){
	    @mkdir($this->getDataFolder()."arenas");
	}
	if(!is_dir($this->getDataFolder()."maps")){
	    @mkdir($this->getDataFolder()."maps");
	}
	foreach(glob($this->getDataFolder()."arenas/*.yml") as $arenasFile){
	     $arenaName = basename($arenasFile, ".yml");
	     if(!isset($this->arenas[$arenaName])){
		 $this->arenas[$arenaName] = new SG($this, $this->getServer()->getLevelByName($arenaName));
	     }
	}
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("SurvivalGames_v1", new SGCommands($this));
        $this->getLogger()->alert("Enabled By DRAGKILLS");
    }

    /**
     * @param Player $player
     * @param $arena
     */
    public function set(Player $player, $arena): void
    {
        $this->editors[$player->getName()] = $this->arenas[$arena];
        $player->sendMessage("§aYou are now in setup mode\n§6type help to get list of help");
    }

    public function getArenaFile($path): Config
    {
        return new Config($this->getDataFolder()."arenas/{$path}.yml", Config::YAML);
    }

    public function getPlayerArena(Player $player): SG
    {
	return isset($this->arenas[$player->getLevel()->getFolderName()]) ? $this->arenas[$player->getLevel()->getFolderName()] : null;
    }

    public function onChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        if(!isset($this->editors[$player->getName()])){
            return false;
        }
        $event->setCancelled(true);
        switch($message){
            case "help":
                $player->sendMessage("SurvivalGames Setup Commands:\nmode <team : solo>\njoinsign : addd join sign\nmaxplayers <int, [max players]>\nspawn <int, 1,2,3...>\n>\ndone : finish setup");
                break;
            case "mode":
                echo "not now";
                break;
            case "maxplayers":
                if(is_numeric($message[1])){
                    $this->editors[$player->getName()]->setMaxPlayers($message[1]);
                    $player->sendMessage("max player set to {$message[1]}");
                } else {
                    $player->sendMessage("type numeric");
                }
                break;
            case "spawn":
                if(!is_numeric($message[1])){
                    $player->sendMessage("{$message[1]} is not numeric");
                    return false;
                }
                $this->editors[$player->getName()]->setSpawn($message[1], $player);
                $player->sendMessage("added spawn {$message[1]}");
                break;
            case "done":
                if(!isset($this->editors[$player->getName()]->data["maxplayers"])){
                    $player->sendMessage("setup maxplayers first");
                    return false;
                }
                unset($this->editors[$player->getName()]);
                $player->sendMessage("setup completed\n");
                break;
            case "joinsign":
                $this->setup[$player->getName()] = 1;
                $player->sendMessage('Break block to set join sign');
                break;
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if(isset($this->setup[$player->getName()])){
            switch ($this->set){
                case 1:
                    $event->setCancelled(true);
                    if($event->getBlock() instanceof Sign){
                        $this->editors[$player->getName()]->data["joinsign"] = [
                            "x" => $event->getBlock()->getX(),
                            "y" => $event->getBlock()->getY(),
                            "z" => $event->getBlock()->getZ(),
                            "Arena" => $this->editors[$player->getName()]->level->getFolderName()
                        ];
                        unset($this->setup[$player->getName()]);
                        $player->sendMessage('join sign added');
                    } else {
                        $player->sendMessage('This is not sign try in sign');
                    }
                    break;
            }
        }
    }
}
