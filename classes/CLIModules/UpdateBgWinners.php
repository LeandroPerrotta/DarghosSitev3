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
		$winners = $query->fetchArray();
		
		$query = $this->db->query("SELECT `player_id`, COUNT(*) as `losses` FROM `player_history` WHERE `type` = 1 AND `history` = '2' AND `date` >= UNIX_TIMESTAMP() - (60 * 60 * 24 * 7) GROUP BY `player_id` ORDER BY `losses` DESC");
		$losses = $query->fetchArray();
		
		$array = array_merge_recursive($winners, $losses);
		$json = json_encode($array);
		
		\Core\Main::writeTempFile("bgbest.json", $json);
	}
}
?>