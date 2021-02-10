<?php
declare(strict_types=1);
namespace sw;

use pocketmine\Player;
use pocketmine\plugin\PluginBase; 
use pocketmine\entity\Entity;
use sw\test\Test;

class SkyWar extends PluginBase{
	/** @var SkyWar $plugin */
	private $plugin;
	/** @var PluginData $pdata */
	private $pdata;
	
	private $arenas = [];
	
	private $matchBackup = null;
	
	public function onEnable(){
		$this->loadPlugin();
	}
	
	public function loadPlugin(){
	    $this->mapBackup = new MapBackup($this);
		$pdatas = $this->pdata;
		$this->loadArenas();
	}
	
	public function savePlugin(){
	}
	
	public function setPluginData(){
	}
	
	public function onCommand(){
	}

	public function getRandomArena() : ?Arena{
		$arenas = $this->plugin->getArenas();
		$arenasByPlayers = [];
		foreach($arenas as $index => $arena){
			if($arena->scheduler->phase == 0 || $arena->scheduler->startTime >= 6){
				if($arena->data["enabled"]) $arenasByPlayers[] = $arena;
			}
		}
		return $arenas[array_rand($arenasByPlayer, 1)];
	}
	
	public function onDisable(){
		$this->savePlugin();
	}
}