<?php 
namespace CLIModules;

use \Framework\Guilds;
use \Framework\Guilds\Rank;
use \Framework\Guilds\War;

class UpdateBgWinners
{
	private $db;
	
	function __construct()
	{
		$this->db = &\Core\Main::$DB;
	}
	
	function Run()
	{
		$query = $this->db->query("SELECT `player_id`, COUNT(*) as `wins` FROM `player_history` WHERE `type` = 1 AND `history` = '1' AND `date` >= UNIX_TIMESTAMP() - (60 * 60 * 24 * 7) GROUP BY `player_id` ORDER BY `wins` DESC");
		
		$data = array();
		
		if($query->numRows() == 0)
			return;
		
		while(($fetch = $query->fetch()))
		{
			array_push($data, array("player_id" => $fetch->player_id, "wins" => $fetch->wins));
		}
		
		$losses = array();
		
		$query = $this->db->query("SELECT `player_id`, COUNT(*) as `losses` FROM `player_history` WHERE `type` = 1 AND `history` = '2' AND `date` >= UNIX_TIMESTAMP() - (60 * 60 * 24 * 7) GROUP BY `player_id` ORDER BY `losses` DESC");
		
		if($query->numRows() > 0)
		{
			while(($fetch = $query->fetch()))
			{	
				foreach($data as $k => $d)
				{
					if($d["player_id"] == $fetch->player_id)
					{
						$data[$k]["losses"] = $fetch->losses;
						break;
					}
				}
			}
		}
		
		$json = json_encode($data);
		
		\Core\Main::writeTempFile("bgbest.json", $json);
	}
}
?>