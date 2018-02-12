<?php
namespace rank;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener{
	public $Main;
	
	public function onEnable(){
		$this->saveDefaultConfig();
		@mkdir($this->getDataFolder());
		$this->getResource("config.yml");
		@mkdir($this->getDataFolder()."Ranks");
    
	$this->getLogger()->info(TF::GREEN."Rank loaded by SkySeven!");
	$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable(){

    }
	
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		
		if($player->isOp()){
			$this->setRank($name, $this->getConfig()->get("op-rank"));
		}else{
			$this->setRank($name, $this->getConfig()->get("default-rank"));
		}

	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		
		if($cmd == "setrank"){
			if($sender->isOp()){
				if(!empty($args[1])){
					
					if(file_exists($this->getDataFolder()."Ranks/".$args[1].".yml")){
						
						$this->setRank($args[0], $args[1]);
						$sender->sendMessage("§a".$args[0]." is now ".$args[1].".");
						
					}else{
						$sender->sendMessage("§cThis rank doesn't exist !");
					}
				}else{
					$sender->sendMessage("§a/setrank §f[§7pseudo§f] [§7rank§f]");
				}
			}
		}
		if($cmd == "addrank"){
			if(!empty($args[1])){
				$config = new Config($this->getDataFolder()."Ranks/".$args[0].".yml", Config::YAML, array(
					"prefix" => $args[1]
				));
				
				$sender->sendMessage("§aThis rank was successfully created");
				
			}else{
				$sender->sendMessage("§a/addrank §f[§7Name§f] [§7Prefix§f]");
			}
		}
		return true;
	}
	
	public function onChat(PlayerChatEvent $event){
		
		$player = $event->getPlayer();
		$name = $player->getName();
		$message = $event->getMessage();
		$rank = $this->getRank($name);
		
		$event->setFormat($this->getPrefix($rank)." §r§7".$name. " §r§8:§7 " . $message);
	}
	
	public function getRank($name){
		
		$config = new Config($this->getDataFolder()."players.yml",Config::YAML);
		return $config->get($name);
		
	}
	
	public function setRank($name, $rank){
		$config = new Config($this->getDataFolder()."players.yml",Config::YAML);
		
		$config->set($name, $rank);
		$config->save();
	}
	
	public function getPrefix($rank){
		$config = new Config($this->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
		return $config->get("prefix");
	}
}