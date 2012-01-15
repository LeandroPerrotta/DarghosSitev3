<?php
class View
{
	function generateList()
	{
		$db = Core::$DB;
		$db instanceof MySQL;
		
		$LIMIT_DAYS = 3;
		
		$query = $db->query("
			SELECT 
				`id`,
				`begin`,
				`end`,
				`team1_points`,
				`team2_points`
			FROM
				`battlegrounds`
			WHERE
				`begin` >= UNIX_TIMESTAMP() - ({$LIMIT_DAYS} * 60 * 60 * 24)
			ORDER BY
				`id`
				DESC
			");
		
		if($query->numRows() == 0)
			return false;
			
		$matches = new ArrayObject();	
			
		while($fetch = $query->fetch())
		{
			$bgMatch = new BattlegroundMatch($fetch->id);
			$bgMatch->begin = $fetch->begin;
			$bgMatch->end = $fetch->end;
			
			$bgMatch->createTeams($fetch->team1_points, $fetch->team2_points);
			
			$players_query = $db->query("
			SELECT
				`player_id`
				,`team_id`
				,`deserter`
				,`ip_address`
			FROM
				`battleground_teamplayers`
			WHERE
				`battleground_id` = {$fetch->id}
			");
			
			while($pf = $players_query->fetch())
			{
				$k_query = $db->query("
					SELECT
						COUNT(*) as rows
					FROM
						`custom_pvp_kills`
					WHERE
						`player_id` = {$pf->player_id}
						AND `is_frag` = 1
						AND `ref_id` = {$fetch->id}
				");
				
				$a_query = $db->query("
					SELECT
						COUNT(*) as rows
					FROM
						`custom_pvp_kills`
					WHERE
						`player_id` = {$pf->player_id}
						AND `ref_id` = {$fetch->id}
				");
				
				$d_query = $db->query("
					SELECT
						COUNT(*) as rows
					FROM
						`custom_pvp_deaths`
					WHERE
						`player_id` = {$pf->player_id}
						AND `ref_id` = {$fetch->id}
				");
				
				$bgMatch->addPlayer($pf->team_id, $pf->player_id, $pf->ip_address, 
					$k_query->fetch()->rows, $a_query->fetch()->rows, $d_query->fetch()->rows, $pf->deserter);
			}
			
			$matches->append($bgMatch);
		}
		
		return $matches;	
	}
	
	function draw()
	{
		global $module;
		$matches = $this->generateList();
		
		$db = Core::$DB;
		
		$table = new HTML_Table();
		$table->AddField("Partidas de Battlegrounds");
		$table->addRow();
		
		if(!$matches || $matches->count() == 0)
		{
			$table->AddField("Nenhuma battleground.");
			$table->AddRow();
		}
		else
		{	
			$it = $matches->getIterator();
		
			while($it->valid())
			{
				$matche = $it->current();
				$matche instanceof BattlegroundMatch;
				
				$string = "#{$matche->id} " . Core::formatDate($matche->begin) . " ~ " . Core::formatDate($matche->end) . " <span class='tooglePlus'/><br/>";
				
				$string .= "(Time A) {$matche->teams[BattlegroundMatch::TEAM_ONE]["points"]} X {$matche->teams[BattlegroundMatch::TEAM_TWO]["points"]} (Time B)"; 
				
				$string .= "<div style='float: left; width: 100%; padding: 0px; margin: 0px; position: relative;'>";
				
				$subTable = new HTML_Table();
				
				$subTable->AddField("Nome");
				$subTable->AddField("IP");
				$subTable->AddField("Frags");
				$subTable->AddField("Assists");
				$subTable->AddField("Deaths");
				$subTable->AddField("Deserter");
				$subTable->AddRow();
				
				foreach($matche->teams[BattlegroundMatch::TEAM_ONE]["players"] as $pInfo)
				{
					$p = new Character();
					$p->load($pInfo["player_id"]);
					
					$subTable->AddField($p->getName());
					$subTable->AddField($pInfo["ip_address"]);
					$subTable->AddField($pInfo["frags"]);
					$subTable->AddField($pInfo["assists"]);
					$subTable->AddField($pInfo["deaths"]);
					$subTable->AddField($pInfo["deserter"]);
					
					$subTable->AddRow();
				}
				
				foreach($matche->teams[BattlegroundMatch::TEAM_TWO]["players"] as $pInfo)
				{
					$p = new Character();
					$p->load($pInfo["player_id"]);
					
					$subTable->AddField($p->getName());
					$subTable->AddField($pInfo["ip_address"]);
					$subTable->AddField($pInfo["frags"]);
					$subTable->AddField($pInfo["assists"]);
					$subTable->AddField($pInfo["deaths"]);
					$subTable->AddField($pInfo["deserter"]);
					
					$subTable->AddRow();
				}			
				
				$string .= $subTable->Draw();
				
				$string .= "</div>";	
				$it->next();
			}		
		}
		
		$module .= $table->Draw();
	}
}

$view = new View();
$view->draw();