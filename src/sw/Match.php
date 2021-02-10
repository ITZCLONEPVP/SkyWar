<?php
declare(strict_types=1);
namespace sw;

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
	private $spectators;
	private $kill_points;
	public $server;
	private $mtime;
	
	public __construct(SkyWar $plugin, array $data){
	    $this->plugin = $plugin;
	    $this->data = $data;
	    $this->server = $plugin->getServer();
	    
	    if(!$this->server->isLevelGenerated($this->plugin->pdata["lobby"])){
	        $plugin->getLogger()->error("Invalid lobby level");
	        return;
	    }
	    if(!$this->server->isLevelLoaded($this->plugin->pdata["lobby"])){
	        $plugin->getServer()->loadLevel($this->plugin->pdata["lobby"]);
	    }
        
        $this->mtime = new MatchTime($this);
        
        if(!file_exists($this->plugin->getDataFolder() . "saves/{$this->data["level"]}.zip"))
            $this->plugin->saveMap($this->plugin->getServer()->getLevelByName($this->data["level"]));
        $this->plugin->loadMap($this->data["level"]);
        
        $this->level = $this->server->getLevelByName($this->data["level"]);
        
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $plugin->getScheduler()->scheduleRepeatingTask($this->mtime, 20);
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
	    $xuid = $player->getXuid();
	    $this->players[$xuid] = $player;
	    $this->kill_points[$xuid] = 0;
	}
	
	public function removePlayer(Player $player, bool $left = false){
	    $xuid = $player->getXuid();
	    if($left){
	        $this->setFeature($player);
	        if(isset($this->players[$xuid])) unset($this->players[$xuid]);
	        else unset($this->spectators[$xuid]);
	    }else{
	        $this->spectators[$xuid] = $player;
	        unset($this->players[$xuid]);
	        $this->setFeature($player, 2);
	    }
	    unset($this->kill_points[$xuid]);
	}
	
	public function broadcastTip(string $mess){
	    foreach($player->addPlayer)
	}
	
	public function setFeature(Player $player, int $mode = -1){
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
	
	public function startMatch(){
	    $this->
	}
	
	public function endMatch(){
	    
	}
	
	public function checkEnd(){
	    if(count($this->$players) > 2) return;
	    $this->endMatch();
	}
	
	public function fillChests(){
	    foreach($this->chestpos as $pos){
	        $inv = $this->level->getTile(Position::fromObject(Vecter3::fromstring($pos), $this->level))->getInventory();
	        
	    }
	}
}