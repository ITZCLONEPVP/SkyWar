<?php
declare(strict_types=1);
namespace sw;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\tile\Sign;

use pocketmine\level\Level;
class MatchTime extends Task{
    public $minPlayer = 2;
    
    private $match;
    public $waitTime = 40;
    public $restartTime = 10;
    public $matchTime = 0;
    public $phase = 0;
    public $forceStart = false;
    
    public function __construct(Match $match) {
        $this->match = $match;
        $this->setUp();
		$this->minPlayer = SkyWar::getInstance()->getPluginData("minPlayer");
    }
    
    public function setUp(){
        
    }

    public function onRun(int $tick) {
        $this->sendScoreboard();
        $this->refreshSign();
        switch ($this->phase) {
            case 0:
            	if(count($this->match->players) < $this->minPlayer && !$this->forceStart){
            		$this->match->broadcast("Waiting for more player!", Match::MESS_TIP);
            		if($waitTime < self::WAIT_TIME) $this->waitTime = self::WAIT_TIME;
            		break;
            	}
            	if($this->waitTime > 10 && count($this->match->players) == $this->match->data["slots"]) $this->waitTime = 10;
                $this->match->broadcast("Starting in " . gmdate("i:s", $this->waitTime), Match::MESS_TIP);

                if($this->waitTime == 5) {
                    foreach ($this->plugin->players as $player) {
                        $this->match->broadcastTitle("WM SkyWars MW");
                    }
                }
                
                if($this->waitTime <= 4){
					$this->match->broadcastTitle("text");
					$this->match->broadcastTip("Cage open in: {$this->waitTime}");
				}

                if($this->waitTime <= 0) $this->plugin->startMatch();
                $this->waitTime--;
                break;
            case 1:
                $this->plugin->checkEnd();
				if($this->matchTime % SkyWars::getInstance()->getData("fill-time") == 0){
				    $this->match->broadcast("All chest have been refilled!", Match::MESS_TIP);
				    $this->match->level->addSound(new AnvilUseSound($player->asVector3()));
				}
                if($this->matchTime % 60 == 0 && count($this->plugin->dragonTargetManager->dragons) < $this->plugin->data["maxDragons"]) {
                    $this->plugin->dragonTargetManager->addDragon();
                    $this->plugin->broadcastMessage(Lang::getMatchPrefix() . Lang::getMessage("dragon-spawned"));
                }
                $this->matchTime++;
                break;
            case 2:
                if($this->rewaitTime == 0) {
                    $this->plugin->broadcastMessage(Lang::getMatchPrefix() . Lang::getMessage("restarting"));

                    $players = $this->plugin->players + $this->plugin->spectators;
                    foreach ($players as $player) {
                        $this->plugin->disconnectPlayer($player, true);
                    }
                }

                if($this->rewaitTime == -2) {
                    $this->plugin->mapReset->loadMap($this->plugin->level->getFolderName());
                }

                if($this->rewaitTime == -5) {
                    $this->plugin->reloadArena();
                }
                $this->rewaitTime--;
                break;
        }
    }

    public function refreshSign() {
        /** @var string $level */
        $level = $this->plugin->data["joinSignLevel"];
        /** @var Vector3 $pos */
        $pos = $this->plugin->data["joinSignPos"];

        if(!$this->plugin->plugin->getServer()->isLevelGenerated($level)) {
            return;
        }
        if(!$this->plugin->plugin->getServer()->isLevelLoaded($level)) {
            $this->plugin->plugin->getServer()->loadLevel($level);
        }

        $targetLevel = $this->plugin->plugin->getServer()->getLevelByName($level);
        if(!$targetLevel instanceof Level) {
            return;
        }

        $sign = $targetLevel->getTile($pos);
        if(!$sign instanceof Sign) {
            return;
        }

        $map = "§a---";
        if($this->plugin->level instanceof Level) {
            $map = $this->plugin->level->getName();
        }

        $phase = $this->phase === 0 ?
            ((count($this->plugin->players) < $this->plugin->data["slots"]) ? "§aJoin" : "§6Full") :
            (($this->phase === 1) ? "§5InMatch" : "§cRestarting...");

        $sign->setText(
            "§5§lDragons§r",
            "§9[§b " . (string)count($this->plugin->players) . " / " . (string)$this->plugin->data["slots"] . " §9]",
            $phase,
            "§8Map: §7$map");
    }

    public function sendScoreboard() {
        if($this->plugin->level === null) {
            return;
        }

        $scoreboardSettings = $this->plugin->plugin->config["scoreboards"];
        if(!$scoreboardSettings["enabled"]) {
            // var_dump($scoreboardSettings);
            return;
        }

        $map = $this->plugin->level === null ? "§a---" : "§a{$this->plugin->level->getName()}";

        /**
         * @param array $settings
         * @param string $map
         *
         * @return string
         */
        $replaceDefault = function (array $settings, string $map): string {
            $text = implode("\n", $settings);

            return str_replace(["{%players}", "{%maxPlayers}", "{%map}"], [(string)count($this->plugin->players), (string)$this->plugin->data["slots"], $map], $text);
        };

        /**
         * @param Dragons $plugin
         * @param Player $player
         *
         * @return string
         */
        $getKit = function (Dragons $plugin, Player $player): string {
            $kit = $plugin->kitManager->playerKits[$player->getName()] ?? "---";
            if($kit instanceof Kit) {
                $kit = $kit->getName();
            }

            return $kit;
        };

        switch ($this->phase) {
            case 0:
                if(count($this->plugin->players) < $this->playersToStart) {
                    foreach ($this->plugin->players as $player) {
                        ScoreboardBuilder::sendScoreBoard($player, str_replace(
                            ["{%kit}"],
                            [$getKit($this->plugin->plugin, $player)],
                            $replaceDefault($scoreboardSettings["formats"]["waiting"], $map)
                        ));
                    }
                }
                else {
                    foreach ($this->plugin->players as $player) {
                        ScoreboardBuilder::sendScoreBoard($player, str_replace(
                            ["{%kit}", "{%waitTime}"],
                            [$getKit($this->plugin->plugin, $player), gmdate("i:s", $this->waitTime)],
                            $replaceDefault($scoreboardSettings["formats"]["starting"], $map)
                        ));
                    }
                }
                break;
            case 1:
                $players = $this->plugin->players + $this->plugin->spectators; // Did you try this already? xd
                foreach ($players as $player) {
                    ScoreboardBuilder::sendScoreBoard($player, str_replace(
                        ["{%kit}", "{%matchTime}"],
                        [$getKit($this->plugin->plugin, $player), gmdate("i:s", $this->matchTime)],
                        $replaceDefault($scoreboardSettings["formats"]["playing"], $map)
                    ));
                }
                break;
            case 2:
                $players = $this->plugin->players + $this->plugin->spectators;
                foreach ($players as $player) {
                    ScoreboardBuilder::sendScoreBoard($player, str_replace(
                        ["{%kit}", "{%rewaitTime}"],
                        [$getKit($this->plugin->plugin, $player), gmdate("i:s", $this->rewaitTime)],
                        $replaceDefault($scoreboardSettings["formats"]["restarting"], $map)
                    ));
                }
                break;
        }
    }

    public function resetTimer() {
        $this->waitTime = self::START_TIME;
        $this->matchTime = 0;
        $this->rewaitTime = self::RESTART_TIME;

        $this->phase = 0;

        $this->forceStart = false;
    }
}