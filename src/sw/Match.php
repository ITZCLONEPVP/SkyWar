<?php
declare(strict_types=1);
namespace sw;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use sw\test\Test;

class Match{
	/** @var SkyWar $plugin */
	private $plugin;
	/** @var MatchData $pdata */
	private $data;
	private $players;
	private $player_data;
	public $server;
	
	public __construct(SkyWar $plugin){
	    $this->server = Server::getInstance(); 
	}
	
	public function join(Player $player){
	    $this->addPlayer($player);
	    $this->setFeature($player, 0);
	    $player->setImmobile(true);
	}
	
	public function left(Player $player, bool $left = false){
	    $this->setFeature($player);
	    $player->setImmobile(false);
	    if(!$left){
	        $player->teleport($this->server->getLevelByName($this->data["lobby"])->getSpawnLocation());
	    }
	}
	
	public function addPlayer(Player $player){
	    $this->players[$player->getXuid()] = $player;
	    $this->player_data[$player->getXuid()] = [
	        "kills" => 0,
	        "status" => 0; // 0 is alive, 1 is death, 2 is left
	    ];
	}
	
	public function setFeature(Player $player, $mode){
	    $inv = $player->getInventory();
	    $a_inv = $player->getArmorInventory();
	    $c_inv = $player->getCursorInventory();
	    $inv->clearAll; 
	    $a_inv->clearAll();
	    $c_inv->clearAll();
	    $player->extinguish(); 
	    switch($mode){
	        case 0:
	            $player->setGamemode(Player::ADVENTURE);
	            $player->setHealth(20);
	            $player->setMaxHealth(20);
	            $player->setFood(20);
	            $player->setFlying(false);
	            $player->setAllowFlight(false);
	            $player->getInventory()->setItem(0, Item::get(Item::NETHERSTAR)->setCustomName("§r§eKit\n§7[Use]"));
	            $player->getInventory()->setItem(8, Item::get(Item::BED)->setCustomName("§r§eBack to Hub\n§7[Use]"));
	        break;
	        case 1:
	            $player->setGamemode(Player::SURVIVAL);
	            $player->setHealth(20);
	            $player->setMaxHealth(20);
	            $player->setFood(20);
	            $player->setFlying(false);
	            $player->setAllowFlight(false);
	            
	            $this->giveKit($player);
	        break;
	        case 2:
	            $player->setGamemode(Player::SPECTATOR);
	            $player->setFlying(true));
	            $player->setAllowFlight(true);
	            $player->getInventory()->setItem(0, Item::get(Item::COMPASS)->setCustomName("§r§eTeleportor\n§7[Use]"));
	            $player->getInventory()->setItem(8, Item::get(Item::BED)->setCustomName("§r§eBack to Hub\n§7[Use]"));
	        break;
	        default:
	            $player->setFlying(false);
	            $player->setAllowFlight(false); 
	            $player->setGamemode($this->server->getGamemode());
	    }
	}
	
	public function genCage(Position $pos){
	    for($this->players as $player){
	}
	
	public function removeCage(Position $pos){
	    
	}
	
	public function onInteract(PlayerInteractEvent_$event){
	    //Todo item options
	}
	
	public function onItemHeld(PlayerItemHeldEvent $event){
	    //Todo item options
	}
	
	public function onDamage(EntittyDamageEvent $event){
	    //Todo phase system
	}
	
	public function onItemDrop(PlayerDropItemEvent $event){
	    $player = $event->getPlayer();
	    //Todo phase system
		//$event->setCancelled(true);
	}
}