<?php 
namespace CLIModules;

use \Core\Main;
use \Framework\Guilds;
use \Framework\Player;
use \Framework\Deaths;
use \Core\Configs;

class UpdatePlayersOn
{
	private $db;
	
	function __construct()
	{
		$this->db = &Main::$DB;
	}
	
	function Run()
	{
	    if(!Configs::Get(Configs::eConf()->ENABLE_MULTIWORLD)){ 
    		$server_ip = Configs::Get(Configs::eConf()->STATUS_HOST);
    		$server_port = "7171";
    		
    		$server = new \OTS_ServerInfo($server_ip, $server_port);
    		$status = $server->info(\OTS_ServerStatus::REQUEST_MISC_SERVER_INFO | \OTS_ServerStatus::REQUEST_PLAYERS_INFO);	
    
    		if(!$status){
    			$this->db->ExecQuery("INSERT INTO `serverstatus` VALUES ('0', '0', '0', '0', UNIX_TIMESTAMP(), 0)");
    			return false;
    		}			
    			
    		$playerson = $status->getOnlinePlayers();
    		$uptime = $status->getUptime();
    		
    		$this->db->ExecQuery("INSERT INTO `serverstatus` VALUES ('{$playerson}', '1', '{$uptime}', '0', UNIX_TIMESTAMP(), 0)");
	    }
	    else {

	        $server_ip = Configs::Get(Configs::eConf()->STATUS_HOST);
	        
			while(\t_Worlds::ItValid())
			{
			    $server_port = \t_Worlds::$m_worldPort[\t_Worlds::It()];

			    $server = new \OTS_ServerInfo($server_ip, $server_port);
			    $status = $server->info(\OTS_ServerStatus::REQUEST_MISC_SERVER_INFO | \OTS_ServerStatus::REQUEST_PLAYERS_INFO);
			    
			    if(!$status){
			        $this->db->ExecQuery("INSERT INTO `serverstatus` VALUES ('0', '0', '0', '0', UNIX_TIMESTAMP(), ".\t_Worlds::It().")");
			        continue;
			    }
			     
			    $playerson = $status->getOnlinePlayers();
			    $uptime = $status->getUptime();
			    
			    $this->db->ExecQuery("INSERT INTO `serverstatus` VALUES ('{$playerson}', '1', '{$uptime}', '0', UNIX_TIMESTAMP(), ".\t_Worlds::It().")");			    
			    
				\t_Worlds::ItNext();
			}
	    }
	    
		return true;
	}
}
?>