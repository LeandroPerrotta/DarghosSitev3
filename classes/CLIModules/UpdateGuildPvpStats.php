<?php 
namespace CLIModules;

use \Core\Main;
use \Framework\Guilds;
use \Framework\Player;
use \Framework\Deaths;
use \Core\Configs;

class UpdateGuildPvpStats
{
	private $db, $guilds;
	
	function __construct()
	{
		$this->db = &Main::$DB;
		$this->guilds = array();
	}
	
	function addGuildDeath($guild_id){
	    if(!$this->guilds[$guild_id])
	        $this->guilds[$guild_id] = array("kills" => 0, "deaths" => 0);
	    
	    $this->guilds[$guild_id]["deaths"]++;
	}
	
	function addGuildKill($guild_id){
	    if(!$this->guilds[$guild_id])
	        $this->guilds[$guild_id] = array("kills" => 0, "deaths" => 0);
	     
	    $this->guilds[$guild_id]["kills"]++;
	}	
	
	function Run()
	{
		$values = array();
		
		$temp = Main::readTempFile("values.json") ;
		if($temp){
			$values = json_decode($temp);
		}
		
		if($values->lastUpdateGuildPvpStats)
			$lastUpdateDate = $values->lastUpdateGuildPvpStats;
		else
			$lastUpdateDate = 0;
		
		echo "Running UpdateGuildPvpStats...\n";
		echo "LastUpdateDate: {$lastUpdateDate}\n";
		
		$query = $this->db->query("SELECT `d`.`id`, `d`.`player_id`, `p`.`rank_id` FROM `player_deaths` `d` LEFT JOIN `players` `p` ON `p`.`id` = `d`.`player_id` WHERE `d`.`date` > {$lastUpdateDate}"); 
		
		echo "Checking for {$query->numRows()} player deaths entries...\n";
		if($query->numRows() > 0){
			while ($fetch = $query->fetch()) {
				$guild_id = Guilds::GetGuildIdByRankId($fetch->rank_id);		
				
				$guild_pvp_death_id = 0;
				
				$death = new Deaths();
				$deathList = $death->load($fetch->id);
				
				if($deathList){
					if(is_array($deathList["killers"])){
						$guildKillers = array();
						
						foreach($deathList["killers"] as $killerEntry){
							if($killerEntry["isEnv"])
								continue;
							
							$killer = new Player();
							$killer->load($killerEntry["killer"]);
											
							if($killer->LoadGuild()){
								if(in_array($killer->getGuildId(), $guildKillers))
									continue;
								else
								{
									if($guild_pvp_death_id == 0 && $guild_id != 0){
										$this->db->ExecQuery("INSERT INTO `guild_pvp_deaths` (`guild_id`, `death_id`) VALUES ({$guild_id}, {$fetch->id})");
										$guild_pvp_death_id = $this->db->lastInsertId();
										$this->addGuildDeath($guild_id);
									}									
								}

								$this->addGuildKill($killer->getGuildId());
								
								if($guild_id != 0){
								    $this->db->ExecQuery("INSERT INTO `guild_pvp_kills` (`guild_id`, `guild_pvp_death_id`) VALUES ({$killer->getGuildId()}, {$guild_pvp_death_id})");
								    $guildKillers[] = $killer->getGuildId();
								}
							}
						}
					}
				}			
			}
		}
		
		foreach($this->guilds as $guild_id => $stats){	    
		    $this->db->query("UPDATE `guilds` SET `kills` = `kills` + {$stats["kills"]}, `deaths` = `deaths` + {$stats["deaths"]} WHERE `id` = {$guild_id}");
		}
		
		$values->lastUpdateGuildPvpStats = time();
		$json = json_encode ($values);
		
		Main::writeTempFile ("values.json", $json);		
	}
}
?>