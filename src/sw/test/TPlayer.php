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
use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
class TPlayer extends Human {
	private $name;
	private $knockback = 0.45;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
	}

	public function initEntity(): void{
		parent::initEntity();
		$this->name = $this->namedtag->getString("playername");
	}
	
	public function knockBack(Entity $attacker, float $damage, float $x, float $z, float $base = 0.45) : void{
		parent::knockBack($attacker, $damage, $x, $z, $this->knockback);
	}
	
	public function saveNBT(): void{
		$this->namedtag->setString("playername", $this->name);
		parent::saveNBT();
	}
}