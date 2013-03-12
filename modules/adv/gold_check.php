<?php
class View
{
	function generateList()
	{
		$db = \Core\Main::$DB;
		$db instanceof \Core\MySQL;
		
		$GOLD_COINS = array(
			array("id" => "2148", "formula" => 1), 
			array("id" => "2152", "formula" => 100),
			array("id" => "2160", "formula" => 10000)
		);
		
		$TABLES = array("player_items", "player_depotitems");
		
		$playersGold = array();
		
		foreach($TABLES as $t)
		{
			foreach($GOLD_COINS as $k => $v)
			{
				$query = $db->query("SELECT `player_id`, SUM(`count`) as total FROM `{$t}` WHERE `itemtype` = {$v["id"]} GROUP BY `player_id`");
				
				if($query->numRows() > 0)
				{				
					while($fetch = $query->fetch())
					{
						$playersGold[$fetch->player_id] += ($fetch->total * $v["formula"]);
					}
				}
			}
		}
		
		$query = $db->query("SELECT `id`, `balance` FROM `players` WHERE `balance` > 0");
		if($query->numRows() > 0)
		{
			while($fetch = $query->fetch())
			{
				$playersGold[$fetch->id] += $fetch->balance;
			}
		}
		
		arsort($playersGold);
		return $playersGold;		
	}
	
	function draw()
	{
		global $module;
		$playersGold = $this->generateList();
		
		$db = \Core\Main::$DB;
		
		$table = new \Framework\HTML\Table();
		$table->AddField("Dinheiro dos jogadores");
		$table->addRow();
		
		$table->AddField("Player");
		$table->AddField("Level");
		$table->AddField("Qtd");
		$table->AddRow();
		
		foreach($playersGold as $id => $v)
		{
			$query = $db->query("SELECT `name`, `level` FROM `players` WHERE id = {$id}");
			$fetch = $query->fetch();
			
			$table->AddField($fetch->name);
			$table->AddField($fetch->level);
			$table->AddField($v);	
			$table->AddRow();		
		}		
		
		$module .= $table->Draw();
	}
}

$view = new View();
$view->draw();