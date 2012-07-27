<?php 
namespace CLIModules;

use \Core\Main;
use \Framework\Guilds;
use \Framework\Player;
use \Framework\Deaths;
use \Core\Configs;

class UpdateGuildPvpStats
{
	private $db;
	
	function __construct()
	{
		$this->db = &Main::$DB;
	}
	
	function Run()
	{
		$values = json_decode(Main::readTempFile("values.json"));
		
		$lastUpdateDate = $values["lastUpdateGuildPvpStats"];
		
		$query = $this->db->query("SELECT `d`.`id`, `d`.`player_id`. `p`.`guild_rank` FROM `player_deaths` `d` LEFT JOIN `players` `p` ON `p`.`id` = `d`.`player_id` WHERE `d`.`date` > {$lastUpdateDate} AND `p`.`guild_rank` != 0");
		
		if($query->numRows() > 0){
			while ( ($fetch = $query->fetch ()) ) {
				$guild_id = Guilds::GetGuildIdByRankId($fetch->guild_rank);
				
				$this->db->ExecQuery("INSERT INTO `guild_pvp_deaths` (`guild_id`, `death_id`) VALUES ({$guild_id}, {$fetch->id})");
				$guild_pvp_death_id = $this->db->lastInsertId();
				
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
								if(in_array($killer->getGuildId, $guildKillers))
									continue;
								
								$this->db->ExecQuery("INSERT INTO `guild_pvp_kills` (`guild_id`, `guild_pvp_death_id`) VALUES ({$killer->getGuildId}, {$guild_pvp_death_id})");
								$guildKillers[] = $guild_pvp_death_id;
							}
						}
					}
				}			
			}
		}
		
		$values["lastUpdateGuildPvpStats"] = time();
		$json = json_encode ($values);
		
		Main::writeTempFile ("values.json", $json);		
	}
}
?>