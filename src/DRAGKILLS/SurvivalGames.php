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


use pocketmine\plugin\PluginBase;

/**
 * Class SurvivalGames
 * @package DRAGKILLS
 */
class SurvivalGames extends PluginBase
{

    public $arenas = [];

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new SGListener($this));
        $this->getServer()->getCommandMap()->register("SG_v1", new SGCommands($this));
        $this->getLogger()->alert("Enabled By DRAGKILLS");
    }
}