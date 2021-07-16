<?php
class View
{
	function generateList()
	{
		global $module;
		
		$db = \Core\Main::$DB;
		$db instanceof \Core\MySQL;
		
		$DEPOT_DEST = 1;
		
		$_DEPOTS = array();
		$_LISTS = array();	
		
		$players = $db->query("SELECT `id` FROM `players` WHERE `world_id` = ".t_Worlds::Aaragon." AND `lastlogin` > 0");
		while($player = $players->fetch())
		{
			$module .= "{$player->id}<br>";
			$locker = $db->query("SELECT `sid` FROM `player_depotitems` WHERE `player_id` = {$player->id} AND `pid` = 1");
			
			$lockerSid = NULL;
			
			if($locker->numRows() == 0)
			{
				$lastSid = $db->query("SELECT `sid` FROM `player_depotitems` WHERE `player_id` = {$player->id} ORDER BY `sid` DESC LIMIT 1");
			
				$lockerSid = $lastSid->fetch()->sid + 1;
				
				$db->query("INSERT INTO `player_depotitems` (`player_id`,`sid`, `pid`, `itemtype`, `count`) VALUES ({$player->id}, {$lockerSid}, 1, 2589, 1)");
				$db->query("INSERT INTO `player_depotitems` (`player_id`,`sid`, `pid`, `itemtype`, `count`) VALUES ({$player->id}, ".($lockerSid + 1).", {$lockerSid}, 2594, 1)");
			}
			else
			{				
				$locker_fetch = $locker->fetch();			
				$lockerSid = $locker_fetch->sid;
			}
			
			$depots = $db->query("SELECT `sid`, `pid` FROM `player_depotitems` WHERE `player_id` = {$player->id} AND `pid` != 1 AND `pid` <= 100");
			if($depots->numRows() == 0)
				continue;
			
			while($depot = $depots->fetch())
			{				
				$childs = $db->query("SELECT `itemtype`, `sid`, `pid` FROM `player_depotitems` WHERE `player_id` = {$player->id} AND `pid` = {$depot->sid}");
				
				while($child = $childs->fetch())
				{
					if($child->itemtype == 2594)
					{
						$depotChest = $db->query("SELECT `sid` FROM `player_depotitems` WHERE `player_id` = {$player->id} AND `pid` = {$lockerSid} AND `itemtype` = 2594");
						
						if($depotChest->numRows() == 0)
							continue;
						
						$chest_fetch = $depotChest->fetch();
						
						$db->query("UPDATE `player_depotitems` SET `pid` = {$chest_fetch->sid} WHERE `player_id` = {$player->id} AND `pid` = {$child->sid}");
					}
					else
					{
						$db->query("UPDATE `player_depotitems` SET `pid` = {$lockerSid} WHERE `player_id` = {$player->id} AND `sid` = {$child->sid}");
					}
				}
			}
		}
		
		$removeItems = array(
			2431 //stonecutter's axe
			,2400 //magic sword
			,2494 //demon armor
			,2112 //teddy bear
			,2493 //demon helmet
		);
		
		$db->query("DELETE FROM `player_depotitems` WHERE `itemtype` IN (".implode(",", $removeItems).")");
		$db->query("DELETE FROM `player_items` WHERE `itemtype` IN (".implode(",", $removeItems).")");
		
		$houses = $db->query("SELECT `price`, `owner` FROM `houses` WHERE `world_id` = ".t_Worlds::Aaragon." AND `owner` != 0");
		if($houses->numRows() > 0)
		{
			while($house = $houses->fetch())
			{
				$db->ExecQuery("UPDATE `players` SET `balance` = `balance` + {$house->price} WHERE `id` = {$house->owner}");
			}
		}
		
		$db->query("DELETE FROM `houses` WHERE `world_id` = ".t_Worlds::Aaragon."");
		$db->query("DELETE FROM `house_lists` WHERE `world_id` = ".t_Worlds::Aaragon."");
		$db->query("DELETE FROM `house_data` WHERE `world_id` = ".t_Worlds::Aaragon."");
		
		$db->ExecQuery("UPDATE players SET `town_id` = ".t_Towns::Quendor.", posx = 0, posy = 0, posz = 0 WHERE world_id = ".t_Worlds::Aaragon."");
	}
	
	function draw()
	{
		$this->generateList();
	}
}

$view = new View();
$view->draw();