<?
class Deaths
{
	private $db/*, $data = array() */;

	function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	function load($death_id)
	{
		$query = $this->db->query("SELECT id, player_id, date, level FROM player_deaths WHERE id = '{$death_id}' AND date + ".(60 * 60 * 24 * SHOW_DEATHS_DAYS_AGO)." > ".time()." ORDER BY date DESC");			
	
		if($query->numRows() != 0)
		{
			$death_fetch = $query->fetch();
			
			$_killers = array();
			$_monsters = array();
				
			$killers_query = $this->db->query("SELECT id, death_id, final_hit FROM killers WHERE death_id = '{$death_fetch->id}'");
			while($killers_fetch = $killers_query->fetch())
			{
				$_isLastDamager = 0;
				$_isEnv = 1;
				
				$player_killers_query = $this->db->query("SELECT kill_id, player_id FROM player_killers WHERE kill_id = '{$killers_fetch->id}' ORDER BY kill_id LIMIT 1");
				if($player_killers_query->numRows() != 0)
				{
					$_isEnv = 0;
					$_damager = $player_killers_query->fetch()->player_id;
					
					if($killers_fetch->final_hit == 1)
						$_isLastDamager = 1;		
				}
				
				$env_killers_query = $this->db->query("SELECT kill_id, name FROM environment_killers WHERE kill_id = '{$killers_fetch->id}' ORDER BY kill_id LIMIT 1");
				if($_isEnv && $env_killers_query->numRows() != 0)
				{
					$_damager = $env_killers_query->fetch()->name;
					
					if($killers_fetch->final_hit == 1)
						$_isLastDamager = 1;

					if(in_array($_damager, $_monsters))	
						continue;
					else
						$_monsters[] = $_damager;					
				}
				
				$_killers[] = array(
					"killer" => $_damager,
					"isEnv" => $_isEnv,
					"isLastDamager" => $_isLastDamager,
				);
				
			}
			
			$deathlist = array
			(
				"date" => $death_fetch->date,
				"level" => $death_fetch->level,
				"killers" => $_killers
			);	
			
			return $deathlist;			
		}
		else
			return false;
	}
	
	function getDeathListOfPlayer($player_id)
	{
		$query = $this->db->query("SELECT id FROM player_deaths WHERE player_id = '{$player_id}' AND date + ".(60 * 60 * 24 * SHOW_DEATHS_DAYS_AGO)." > ".time()." ORDER BY date DESC");
		
		if($query->numRows() != 0)
		{
			$deaths = array();
			
			while($fetch = $query->fetch())
			{
				$deaths[] = $fetch->id;
			}
			
			return $deaths;
		}
		else
			return false;
	}

	function getKillsOfPlayer($player_id)
	{
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "
			SELECT 
				player_deaths.player_id, 
				player_deaths.level, 
				player_deaths.date, 
				player_killers.unjustified 
			FROM 
				player_deaths, 
				killers, 
				player_killers 
			WHERE 
				player_killers.player_id = '{$player_id}' 
				AND killers.id = player_killers.kill_id 
				AND player_deaths.id = killers.death_id 
				AND player_deaths.date > ".(time() - (60 * 60 * 24 * 60))." 
			ORDER BY 
				player_deaths.date DESC";
		elseif(SERVER_DISTRO == DISTRO_TFS)	
			$query_str = "
			SELECT 
				player_deaths.player_id, 
				player_deaths.level, 
				player_deaths.date, 
				killers.unjustified 
			FROM 
				player_deaths, 
				killers, 
				player_killers 
			WHERE 
				player_killers.player_id = '{$player_id}' 
				AND killers.id = player_killers.kill_id 
				AND player_deaths.id = killers.death_id 
				AND player_deaths.date > ".(time() - (60 * 60 * 24 * 60))." 
			ORDER BY 
				player_deaths.date DESC";		
			
		$query = $this->db->query($query_str);
		
		if($query->numRows() != 0)
		{
			$kills = array();
			
			while($fetch = $query->fetch())
			{
				$kills[] = array("killed" => $fetch->player_id, "date" => $fetch->date, "level" => $fetch->level, "injust" => $fetch->unjustified);
			}
			
			return $kills;
		}
		else
			return false;		
	}
}
?>