<?php 
namespace Controllers;

use Core\Configs;
use Core\Consts;
use Framework\Player;

class Adv
{
    function UpdateAccs(){
        $query = \Core\Main::$DB->query("SELECT item.name, item.price, p.account_id FROM darghos_bkp.wb_itemshop_log l LEFT JOIN darghos_bkp.wb_itemshop item ON item.id = l.shop_id LEFT JOIN darghos_bkp.players p ON p.id = l.player_id WHERE l.`date` + (60 * 60 * 24 * 45) >= UNIX_TIMESTAMP()");
    
        if($query->numRows() > 0){
            while($fetch = $query->fetch()){
                
                echo "Updating #{$fetch->account_id} with {$fetch->price}<br>";
                \Core\Main::$DB->query("UPDATE `accounts` SET `balance` = `balance` + {$fetch->price} WHERE `id` = {$fetch->account_id}");
            }
        }
    }
    
    function UpdatePlayers(){
        
        $DB = \Core\Main::$DB;
        
        $DB->query("
            UPDATE `players` SET 
                `posx` = 0
                , `posy` = 0
                , `posz` = 0
                , `town_id` = 1
                , `level` = 200
                , `experience` = 129389800
                , `manaspent` = 0
                , `pvpEnabled` = 1
            ");
        
        $DB->query("
            UPDATE 
                `players` 
            SET
                `maglevel` = 85
                , `health` = 1145
                , `healthmax` = 1145
                , `mana` = 5800
                , `manamax` = 5800
                , `cap` = 2390
            WHERE
                vocation IN(1, 2) AND `maglevel` < 85       
            ");

        $DB->query("
            UPDATE
                `players`
            SET
                `health` = 1145
                , `healthmax` = 1145
                , `mana` = 5800
                , `manamax` = 5800
                , `cap` = 2390
            WHERE
                vocation IN(1, 2) AND `maglevel` >= 85
            ");

        $DB->query("
            UPDATE
                `players`
            SET
                `maglevel` = 26
                , `health` = 2105
                , `healthmax` = 2105
                , `mana` = 2920
                , `manamax` = 2920
                , `cap` = 4310
            WHERE
                vocation IN(3) AND `maglevel` < 26          
            ");
        
        $DB->query("
            UPDATE
                `players`
            SET
                `health` = 2105
                , `healthmax` = 2105
                , `mana` = 2920
                , `manamax` = 2920
                , `cap` = 4310
            WHERE
                vocation IN(3) AND `maglevel` >= 26
            ");        

        $DB->query("
            UPDATE
                `players`
            SET
                `maglevel` = 10
                , `health` = 3065
                , `healthmax` = 3065
                , `mana` = 1000
                , `manamax` = 1000
                , `cap` = 5270
            WHERE
                vocation IN(4) AND `maglevel` < 10       
            ");
        

        $DB->query("
            UPDATE
                `players`
            SET
                `health` = 3065
                , `healthmax` = 3065
                , `mana` = 1000
                , `manamax` = 1000
                , `cap` = 5270
            WHERE
                vocation IN(4) AND `maglevel` >= 10
            ");

        $query = $DB->query("SELECT `id` FROM `players`");
        
        while($fetch = $query->fetch()){
            $player = new Player();
            
            $player->load($fetch->id);
            
            $player->loadSkills();
            
            if(\Core\Tools::isDruid($player->getVocation(false)) || \Core\Tools::isSorcerer($player->getVocation(false)))
            {
                if($player->getSkill(\t_Skills::Shielding) < 30)
                    $player->setSkill(\t_Skills::Shielding, 30);
            }
            elseif(\Core\Tools::isPaladin($player->getVocation(false)))
            {
                if($player->getSkill(\t_Skills::Shielding) < 85)
                    $player->setSkill(\t_Skills::Shielding, 85);
                
                if($player->getSkill(\t_Skills::Distance) < 105)
                    $player->setSkill(\t_Skills::Shielding, 105);
            }
            elseif(\Core\Tools::isKnight($player->getVocation(false)))
            {
                if($player->getSkill(\t_Skills::Shielding) < 95)
                    $player->setSkill(\t_Skills::Shielding, 95);
            
                if($player->getSkill(\t_Skills::Axe) < 95)
                    $player->setSkill(\t_Skills::Axe, 95);
                
                if($player->getSkill(\t_Skills::Sword) < 95)
                    $player->setSkill(\t_Skills::Sword, 95);

                if($player->getSkill(\t_Skills::Club) < 95)
                    $player->setSkill(\t_Skills::Club, 95);                
            } 

            $player->saveSkills();
        }
    }
    
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