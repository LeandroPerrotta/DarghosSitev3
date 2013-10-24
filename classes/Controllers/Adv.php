<?php 
namespace Controllers;

use Core\Configs;
use Core\Consts;

class Adv
{
    function Statistics(){	
		
    	new \Views\Adv\Statistics(array());
    	return true;
    }
    
    function GetStatisticsData(){
    	
    	\Core\Main::$isAjax = true;
    	
		$days = $_POST["days"] ? $_POST["days"] : 30;
		
		$today = getdate();
		
		$qtd_list = array();
		$date_list = array();
		
		$target_end = mktime(3, 0, 0, $today["mon"], $today["mday"], $today["year"]);
		$day_seconds = 60 * 60 * 24;
		$target_start = $target_end - $day_seconds;
		
		$db = \Core\Main::$DB;
		
		$qtd = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE `login` >= {$target_start} AND `login` < {$target_end}")->numRows();
		array_push($qtd_list, $qtd);
		
		for($i = 1; $i < ($days); $i++){
			
			$target_end -= $day_seconds;
			$target_start = $target_end - $day_seconds;
			
			$date_str = date("d-m", $target_start);
			array_push($date_list, $date_str);
			
			$qtd = $db->query("SELECT DISTINCT `ip_address` FROM `player_activities` WHERE `login` >= {$target_start} AND `login` < {$target_end}")->numRows();
			array_push($qtd_list, $qtd);
		}
		
		$array = array();
		
		$array["data"] = array_reverse($qtd_list);
		$array["labels"] = array_reverse($date_list);
		
		return $array;
    }
}
?>