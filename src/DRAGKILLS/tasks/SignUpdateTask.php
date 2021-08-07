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
 * Date: 8/6/2021
 * Time: 6:17 PM
 */


declare(strict_types=1);

namespace DRAGKILLS\tasks;


use DRAGKILLS\arena\SG;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\tile\Sign;
use pocketmine\tile\Tile;

/**
 * Class SignUpdateTask
 * @package DRAGKILLS\tasks
 */
class SignUpdateTask extends Task
{

    /**
     * @var SG
     */
    public $plugin;

    /**
     * @param SG $plugin
     */
    public function __construct(SG $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        if(!is_array($this->plugin->data['joinsign']) || empty($this->plugin->data['joinsign']) || !isset($this->plugin->data['joinsign'])) return;
        $signPos = Position::fromObject(new Vector3($this->plugin->data['joinsign']['x'], $this->plugin->data['joinsign']['y'], $this->plugin->data['joinsign']['z']), $this->plugin->plugin->getServer()->getLevelByName($this->plugin->data['joinsign']['Arena']));
        if(!$signPos->getLevel() instanceof Level) return;
        if($signPos->getLevel()->getTile($signPos) === null) return;
        $sign = $signPos->getLevel()->getTile($signPos);
        $signText = [
            'SurvivalGames',
            '',
            'Click To Join'
        ];
        $signText[1] = '[ ' . count($this->plugin->players) . ' / ' . $this->plugin->data['maxplayers'] . ' ]';
        switch ($this->plugin->phase){
            case SG::GAME_STARTED;
                $signText[2] = 'Game Started';
                break;
            case SG::GAME_RESTARTING:
                $signText[2] = 'Game Restarting';
                break;
        }
        if($sign instanceof Sign) {
            $sign->setText($signText[0], $signText[1], $signText[3]);
        }
    }
}