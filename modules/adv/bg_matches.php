<?php
class View
{		
	const PAGE_LIMIT = 25;
	
	function generateList()
	{
		$db = Core::$DB;
		$db instanceof MySQL;
		
		$start = 0;
		if($_GET["page"] && $_GET["page"] >= 1)
			$start = $_GET["page"] - 1;		
		
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
				`end` != 0
			ORDER BY
				`id`
				DESC
			LIMIT ".($start * self::PAGE_LIMIT).", ".self::PAGE_LIMIT."
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
			
			$stats = array();
			
			$players_query = $db->query("
			SELECT
				`player_id`
				,`team_id`
				,`deserter`
				,`ip_address`
				,`params`
			FROM
				`battleground_teamplayers`
			WHERE
				`battleground_id` = {$fetch->id}		
			");
			
			$kstats_query = $db->query("
			SELECT
				`player_id`,
				`is_frag`
			FROM
				`custom_pvp_kills`
			WHERE
				`ref_id` = {$fetch->id}");
			
			while($kfetch = $kstats_query->fetch())
			{
				if($kfetch->is_frag == 1)
				{
					$stats[$kfetch->player_id]["kills"]++;
				}
				
				$stats[$kfetch->player_id]["assists"]++;
			}		
			
			$kstats_query = $db->query("
			SELECT
				`player_id`
			FROM
				`custom_pvp_deaths`
			WHERE
				`ref_id` = {$fetch->id}");
			
			while($kfetch = $kstats_query->fetch())
			{				
				$stats[$kfetch->player_id]["deaths"]++;
			}					
			
			while($pf = $players_query->fetch())
			{								
				$params = json_decode($pf->params, true);
				
				$bgMatch->addPlayer($pf->team_id, $pf->player_id, $pf->ip_address, 
					$stats[$pf->player_id]["kills"] | 0, $stats[$pf->player_id]["assists"] | 0, $stats[$pf->player_id]["deaths"] | 0, $pf->deserter,
					$params["damage"], $params["heal"], $params["expGain"], $params["honorGain"], $params["ratingChange"], $params["highStamina"]);
			}
			
			$matches->append($bgMatch);
		}
		
		return $matches;	
	}
	
	static function compare($a, $b)
	{
		$order = (!$_GET["order"] ? "frags" : $_GET["order"]);
		$a = $a[$order];
		$b = $b[$order];

		return ($a > $b ? -1 : ($a == $b ? 0 : 1));
	}	
	
	function draw()
	{
		global $module;
		$matches = $this->generateList();
		
		$db = Core::$DB;
		
		$table = new HTML_Table();
		$table->IsSubTable();
		$table->AddField("Partidas de Battlegrounds");
		$table->addRow();
		
		if(!$matches || $matches->count() == 0)
		{
			$table->AddField("Nenhuma battleground.");
			$table->AddRow();
		}
		else
		{				
			$args = "";
			if($_GET["order"])
				$args .= "&order={$_GET["order"]}";					
			
			$pageNow = 1;
			if($_GET["page"])
				$pageNow = $_GET["page"];
				
			$pageNext = $pageNow + 1;
			$pagePrev = $pageNow - 1;			
			
			$query = $db->query("SELECT COUNT(*) pages FROM `battlegrounds` WHERE `end` != 0");
			$pageLast = ceil($query->fetch()->pages / self::PAGE_LIMIT);
			$pageFirst = 1;
			
			$bar = "";
			
			$module .= "<div>";
			
			
			if($pageNow > 1)
			{
				$module .= "<span style='margin-top: 10px; float: left;'>";
				$module .= "<a href='?ref=adv.bg_matches{$args}&page={$pageFirst}'>Primeira</a> | ";
				$module .= "<a href='?ref=adv.bg_matches{$args}&page={$pagePrev}'>Voltar</a>";
				$module .= "</span>";
			}
				
			if($pageNow < $pageLast)
			{
				$module .= "<span style='margin-top: 10px; float: right;'>";
				$module .= "<a href='?ref=adv.bg_matches{$args}&page={$pageNext}'>Proxima</a> | ";
				$module .= "<a href='?ref=adv.bg_matches{$args}&page={$pageLast}'>Ultima</a>";
				$module .= "</span>";
			}
			
			$module .= "</div>";			
			
			$it = $matches->getIterator();
		
			while($it->valid())
			{
				$matche = $it->current();
				$matche instanceof BattlegroundMatch;
				
				$string = "#{$matche->id} " . Core::formatDate($matche->begin) . " ~ " . Core::formatDate($matche->end) . " <span class='tooglePlus'></span><br/>";
				
				$string .= "(Time A) {$matche->teams[BattlegroundMatch::TEAM_ONE]["points"]} X {$matche->teams[BattlegroundMatch::TEAM_TWO]["points"]} (Time B)"; 
				
				$string .= "<div style='float: left; width: 100%; padding: 0px; margin: 0px; position: relative;'>";
				
				$subTable = new HTML_Table();
				
				$subTable->AddField("Stats");
				$subTable->AddRow();
				
				$args = "";
				if($_GET["page"])
					$args = "&page={$_GET["page"]}";				
				
				$subTable->AddField("Nome");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=ip_address'>IP</a>");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=frags'>Frags</a>");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=assists'>Assists</a>");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=deaths'>Deaths</a>");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=damage'>Damage</a>");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=heal'>Heal</a>");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=expGain'>Exp</a>");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=honorGain'>Honor</a>");
				$subTable->AddField("<a href='?ref=adv.bg_matches{$args}&order=changeRating'>Rating</a>");
				$subTable->AddRow();
				
				if(count($matche->teams["players"]) > 0)
				{
					$playerList = $matche->teams["players"];
					usort($playerList, array("View", "compare"));
					
					foreach($playerList as $pInfo)
					{
						$p = new Character();
						$p->load($pInfo["player_id"]);
						
						$team_str = array(
							BattlegroundMatch::TEAM_ONE => "A"
							,BattlegroundMatch::TEAM_TWO => "B"
						);
						
						$voc = new t_Vocation($p->getVocation());
						
						$subTable->AddField(($pInfo["deserter"] ? "(<-) " : "") . $p->getName() . "<br>Time {$team_str[$pInfo["team_id"]]}<br>lv {$p->getLevel()}<br>{$voc->GetByAbrev()} <br>rating {$p->getBattlegroundRating()}");
						$subTable->AddField($pInfo["ip_address"]);
						$subTable->AddField($pInfo["frags"]);
						$subTable->AddField($pInfo["assists"]);
						$subTable->AddField($pInfo["deaths"]);
						$subTable->AddField($pInfo["damage"]);
						$subTable->AddField($pInfo["heal"]);
						$subTable->AddField($pInfo["gainExp"] . ($pInfo["highStamina"] == 1 ? " (*)" : ""));
						$subTable->AddField($pInfo["gainHonor"]);
						$subTable->AddField($pInfo["changeRating"]);
						
						$subTable->AddRow();
					}
				}
				
				$string .= $subTable->Draw();
				
				$string .= "</div>";	
				
				$table->AddField($string);
				$table->AddRow();
				
				$it->next();
			}		
		}
		
		$module .= $table->Draw();
	}
}

$view = new View();
$view->draw();