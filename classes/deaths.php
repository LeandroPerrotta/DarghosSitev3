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
			
			$killerIsEnv = 0;
			$altKillerIsEnv = 0;
			
			$_killer = "";
			$_altkiller = "";
				
			$killers_query = $this->db->query("SELECT id, death_id, lasthit FROM killers WHERE death_id = '{$death_fetch->id}'");
			while($killers_fetch = $killers_query->fetch())
			{
				$player_killers_query = $this->db->query("SELECT kill_id, player_id FROM player_killers WHERE kill_id = '{$killers_fetch->id}' ORDER BY kill_id LIMIT 1");
				if($player_killers_query->numRows() != 0)
				{
					if($killers_fetch->lasthit == 1)
						$_killer = $player_killers_query->fetch()->player_id;
					else
						$_altkiller = $player_killers_query->fetch()->player_id;
				}
				
				$env_killers_query = $this->db->query("SELECT kill_id, name FROM environment_killers WHERE kill_id = '{$killers_fetch->id}' ORDER BY kill_id LIMIT 1");
				if($env_killers_query->numRows() != 0)
				{
					if($killers_fetch->lasthit == 1)
					{
						$_killer = $env_killers_query->fetch()->name;
						$killerIsEnv = 1;
					}	
					else
					{
						$_altkiller = $env_killers_query->fetch()->name;
						$altKillerIsEnv = 1;
					}	
				}
				
			}
			
			$deathlist = array
			(
				"date" => $death_fetch->date,
				"level" => $death_fetch->level,
				"killed_by" => $_killer,
				"killer_is_env" => $killerIsEnv,
				"altkilled_by" => $_altkiller,
				"alt_killer_is_env" => $altKillerIsEnv
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

}
?>