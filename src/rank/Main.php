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
		@mkdir($this->getDataFolder()."ranks");
		$this->getLogger()->info(TF::GREEN."Rank loaded by SkySeven!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable(){

    }
	
	public function onJoin(PlayerJoinEvent $event){

		$player = $event->getPlayer();
		$name = $player->getName();
		
		if(!$this->hasRank($name)){

			if($player->isOp()){
				$this->setRank($name, $this->getConfig()->get("op-rank"));
			}else{
				$this->setRank($name, $this->getConfig()->get("default-rank"));
			}
		}

	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		
		if($sender->isOp()){
			if($cmd == "setrank"){
					
				if(!empty($args[1])){
					
					if(file_exists($this->getDataFolder()."ranks/".strtolower($args[1]).".yml")){
							
						$this->setRank($args[0], strtolower($args[1]));
						$sender->sendMessage("§l§a» §r§a".$args[0]." is now §e".strtolower($args[1]).".");
						
					}else{
						$sender->sendMessage("§l§c» §r§cThis rank doesn't exist !");
					}
				}else{
					$sender->sendMessage("§l§a» §r§a/setrank [name] [rank]");
				}
			}
			if($cmd == "addrank"){

				if(!empty($args[1])){

					$config = new Config($this->getDataFolder()."ranks/".strtolower($args[0]).".yml", Config::YAML, array(
						"prefix" => $args[1]
					));
					
					$sender->sendMessage("§l§a» §r§aThis rank was successfully created");
					
				}else{
					$sender->sendMessage("§l§a» §r§a/addrank [name] [prefix]");
				}
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
		return $config->get(strtolower($name));
		
	}
	
	public function setRank($name, $rank){

		$config = new Config($this->getDataFolder()."players.yml",Config::YAML);
		
		$config->set(strtolower($name), $rank);
		$config->save();
	}

	public function hasRank($name){
		
		$config = new Config($this->getDataFolder()."players.yml",Config::YAML);
		return $config->get(strtolower($name)) != false;

	}
	
	public function getPrefix($rank){

		$config = new Config($this->getDataFolder()."ranks/".$rank.".yml",Config::YAML);
		return $config->get("prefix");
	}
}
