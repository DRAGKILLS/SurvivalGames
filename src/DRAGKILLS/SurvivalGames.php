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
 * Time: 12:50 PM
 */


declare(strict_types=1);

namespace DRAGKILLS;


use DRAGKILLS\arena\SG;
use DRAGKILLS\commands\SGCommands;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
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

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("SG_v1", new SGCommands($this));
        $this->getLogger()->alert("Enabled By DRAGKILLS");
    }

    public function set(Player $player, $arena): void
    {
        $this->editors[$player->getName()] = $this->arenas[$arena];
        $player->sendMessage("You are now in setup mode\ntype /.help to get list of help");
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
            case "/.help":
                $player->sendMessage("SurvivalGames SetUP:\n/.mode <team : solo>\n/.maxplayers <num, [max players]>/.done\n/.spawn <num, 1,2,3...>\n>");
                break;
            case "/.mode":
                echo "not now";
                break;
            case "/.maxplayers":
                if(is_numeric($message[1])){
                    $this->editors[$player->getName()]->setMaxPlayers($message[1]);
                } else {
                    $player->sendMessage("type numeric");
                }
                break;
        }
    }
}