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
 * Time: 12:57 PM
 */


declare(strict_types=1);

namespace DRAGKILLS\commands;


use DRAGKILLS\arena\SG;
use DRAGKILLS\SurvivalGames;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

/**
 * Class SGCommands
 * @package DRAGKILLS\commands
 */
class SGCommands extends PluginCommand
{

    /**
     * @var SurvivalGames
     */
    public $plugin;

    public function __construct(SurvivalGames $owner)
    {
        $this->plugin = $owner;
        parent::__construct("sg", $owner);
        parent::setDescription("SurvivalGames Command");
        parent::setAliases(["survivalgames"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!isset($args[0])){
            $sender->sendMessage("do /{$commandLabel} help to get all list off commands");
            return;
        }
        if(!$sender->hasPermission("sg.admin")){
            switch(strtolower($args[0])){
                case "join":
                    if(!isset($args[1])){
                        $sender->sendMessage("do /{$commandLabel} join (arenaName)");
                        return;
                    }
                    if(!isset($this->plugin->arenas[$args[1]])){
                        $sender->sendMessage("arena {$args[1]} not found");
                        return;
                    }
                    if($sender instanceof Player)
                        $this->plugin->arenas[$args[1]]->joinToArena($sender);
                    break;
                case "quit":
                    if($sender instanceof Player)
                        $this->plugin->getPlayerArena($sender)->leaveFromArena($sender);
                    break;
                default:
                    $sender->sendMessage("§cYou dont have permission to use this command");
                    break;
            }
            return;
        }
        switch(strtolower($args[0])){
            case "join":
                if(!isset($args[1])){
                    $sender->sendMessage("do /{$commandLabel} join (arenaName)");
                    return;
                }
                if(!isset($this->plugin->arenas[$args[1]])){
                    $sender->sendMessage("arena {$args[1]} not found");
                    return;
                }
                if($sender instanceof Player)
                    $this->plugin->arenas[$args[1]]->joinToArena($sender);
                break;
            case "quit":
                if($sender instanceof Player)
                    $this->plugin->getPlayerArena($sender)->leaveFromArena($sender);
                break;
            case "help":
                $sender->sendMessage("SurvivalGames command:\n/{$commandLabel} help : get list of commands\n/{$commandLabel} create : create new SurvivalGames arena\n/{$commandLabel} delete : delete SurvivalGames arena\n/{$commandLabel} set : setup SurvivalGames arena\n/{$commandLabel} about");
                break;
            case "about":
                $sender->sendMessage("By DRAGKILLS\ngithub : https://github.com/DRAGKILLS\nDiscord : DRAGKILLS#0830\nDiscord Server : https://discord.gg/ab9qEQmCya");
                break;
            case "create":
                if($sender instanceof Player){
                    if(!isset($args[1])){
                        $sender->sendMessage("you dont type arena name!");
                        return;
                    }
                    if(!$this->plugin->getServer()->isLevelGenerated($args[1])){
                        $sender->sendMessage("{$args[1]} is not level/world!");
                        return;
                    }
                    if(isset($this->plugin->arenas[$args[1]])){
                        $sender->sendMessage("arena {$args[1]} is already exist!");
                        return;
                    }
                    $level = $this->plugin->getServer()->getLevelByName($args[1]);
                    $this->plugin->arenas[$args[1]] = new SG($this->plugin, $level);
                } else {
                    $sender->sendMessage("Only IN_GAME");
                }
                break;
            case "delete":
            case "remove":
            case "rm":
                if($sender instanceof Player){
                    if(!isset($args[1])){
                        $sender->sendMessage("you dont type arena name!");
                        return;
                    }
                    if(!isset($this->plugin->arenas[$args[1]])) {
                        $sender->sendMessage("arena {$args[1]} is not arena!");
                        return;
                    }
                    unset($this->plugin->arenas[$args[1]]);
                    unlink($this->plugin->getDataFolder() . "maps/" . $args[1] . ".zip");
                    unlink($this->plugin->getDataFolder() . "arenas/" . $args[1] . ".yml");
                } else {
                    $sender->sendMessage("Only IN_GAME");
                }
                break;
            case "set":
                if($sender instanceof Player){
                    if(!isset($args[1])){
                        $sender->sendMessage("do /{$commandLabel} set (arena)}");
                        return;
                    }
                    $this->plugin->set($sender, $args[1]);
                } else {
                    $sender->sendMessage("Only IN_GAME");
                }
                break;
            default:
                $sender->sendMessage("do /{$commandLabel} help for get list off commands");
                break;
        }
    }
}
