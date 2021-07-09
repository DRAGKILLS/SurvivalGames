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


use DRAGKILLS\SurvivalGames;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

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
            $sender->sendMessage("do /{$commandLabel} help for get list off commands");
            return false;
        }
        switch(strtolower($args[0])){
            case "help":
                break;
            default:
                $sender->sendMessage("do /{$commandLabel} help for get list off commands");
                break;
        }
    }
}