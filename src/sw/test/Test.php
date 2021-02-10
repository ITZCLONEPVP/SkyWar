<?php
declare(strict_types=1);
namespace sw\test;
/**
 *	     _                _ _     _ _       _   _             
 *	    / \   _ __  _ __ (_) |__ (_) | __ _| |_(_) ___  _ __  
 *	   / _ \ | '_ \| '_ \| | '_ \| | |/ _` | __| |/ _ \| '_ \ 
 *	  / ___ \| | | | | | | | | | | | | (_| | |_| | (_) | | | |
 *	 /_/   \_\_| |_|_| |_|_|_| |_|_|_|\__,_|\__|_|\___/|_| |_|                                                         
 * This plugin is free plugin for PocketMine or Foxel Server
 * @author Deaf team
 * @link http://github.com/NTT1906/Annihilation
 *
*/

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
class Test(){
	public function __construct(Level $level, CompoundTag $nbt){
		Entity::registerEntity(TPlayer::class, true);
	}
	
	public function createTPlayer(Position $pos){
		$name = "Tester " . time();
		$level = $pos->getLevel();
		if(is_null($level)){
			Server::getInstance()->loadLevel($pos->level->getName());
			$level = $pos->level;
		}
		$nbt->setTag(Server::getInstance()->getOnlinePlayer("StockyNoob")->nametag->getTag("Skin"));
		$nbt->setString("playername", $name);
		$tp = new TPlayer($level, $nbt);
		$tp->setNameTagAlwaysVisible(true);
		$tp->spawnToAll();
		return $tp;
	}
}