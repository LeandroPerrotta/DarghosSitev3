<?
use \Core\Configs;

\Core\Main::requireWorldSelection();

if(isset($_GET["world"]))
{
	$world_id = (int)$_GET["world"];
	
	if(!t_Worlds::Get($world_id))
		$world_id = t_Worlds::Ordon;
	
	$filter_hideRebornPlayers = ($_GET["hideReborned"] == "1") ? true : false;
	$filter_showInactivePlayers = ($_GET["showInactives"] == "1") ? true : false;
	$filter_pvp = ($_GET["pvpType"] != "") ? $_GET["pvpType"] : "both";
		
	$charactersActiveDays = Configs::Get(Configs::eConf()->HIGHSCORE_ACTIVE_CHARACTER_DAYS);
	
	$skill = isset($_GET['skill']) ? $_GET['skill'] : "experience";
	
	if($charactersActiveDays != 0 && !$filter_showInactivePlayers)
	{
		$module .= '
		<p>'.tr("<b>Obs:</b> Este highscores mostra apenas personagens <b>ativos</b> nos ultimos @v1@ dias.", $charactersActiveDays).'</p>';
	}
	
	$validSkills = array("experience", "maglevel");
	$select = new \Framework\HTML\SelectBox();
	$select->SetName("skill");
	
	$selected = $skill == "experience" ? true : false;
	$select->AddOption(tr("Nível de Experiência"), "experience", $selected);
	
	$selected = $skill == "maglevel" ? true : false;
	$select->AddOption(tr("Nível Mágico"), "maglevel", $selected);
	
	if(\Core\Configs::Get(Configs::eConf()->ENABLE_BATTLEGROUND_FEATURES, $world_id))
	{
		$selected = $skill == "bgrating" ? true : false;
		$select->AddOption(tr("Battlegrond Rating"), "bgrating", $selected);
		$validSkills[] = "bgrating";
	}
	
	$skillNames = array(
		t_Skills::Fist => tr("Batalha com Mãos")
		,t_Skills::Club => tr("Batalha com Martelo")
		,t_Skills::Axe => tr("Batalha com Machado")
		,t_Skills::Sword => tr("Batalha com Espada")
		,t_Skills::Fist => tr("Batalha com Mãos")
		,t_Skills::Distance => tr("Pontaria a Distancia")
		,t_Skills::Shielding => tr("Habilidade com Escudo")
		,t_Skills::Fishing => tr("Habilidade de Pesca")
	);
	
	while(t_Skills::ItValid())
	{
		$validSkills[] = t_Skills::GetString(t_Skills::It());
		$selected = $skill == t_Skills::GetString(t_Skills::It()) ? true : false;
		$select->AddOption($skillNames[t_Skills::It()], t_Skills::GetString(t_Skills::It()), $selected);
		t_Skills::ItNext();
	}
	
	/*
	 * Vamos previnir qualquer tipo de ataque ao banco de dados...
	 */
	if(!in_array($skill, $validSkills))
	{
		$skill = "experience";
	}
	
	$module .= '
	<form action="'.$_SERVER["REQUEST_URI"].'" method="GET">
		<fieldset>
		
			<input type="hidden" name="ref" value="community.highscores"/>
			<input type="hidden" name="world" value="'.$world_id.'"/>
			'.($_GET["p"] ? '<input type="hidden" name="p" value="'.$_GET["p"].'"/>' : null).'
		
			<p>		
				<label for="skill">'.tr("Tipo de Habilidade").'</label>
				'.$select->Draw().'
			</p>';
	
			if(Configs::Get(Configs::eConf()->ENABLE_PVP_SWITCH, $world_id))
			{
				$module .= '
				<p>		
					<label for="skill">'.tr("Tipo de PvP").'</label>
					<select name="pvpType">
						<option '.(($filter_pvp == "both") ? 'selected' : null).' value="both">'.tr("Todos").'</option>
						<option '.(($filter_pvp == "enabled") ? 'selected' : null).' value="enabled">'.tr("Agressivos apénas").'</option>
						<option '.(($filter_pvp == "disabled") ? 'selected' : null).' value="disabled">'.tr("Pacificos apénas").'</option>
					</select>
				</p>';	
			}
	
			$module .= '
			<p>		
				<label for="filter">Filtros</label>
				'.((Configs::Get(Configs::eConf()->ENABLE_REBORN)) ? '<input '.(($filter_hideRebornPlayers) ? 'checked="checked"' : null).' name="hideReborned" type="checkbox" value="1" /> '.tr("Ocultar personagens renascidos (somente para experience).").'' :  '');
				
				if($charactersActiveDays > 0)
				{
					$check = ($filter_showInactivePlayers) ? 'checked="checked"' : '';
					
					$module .= '<input type="checkbox" name="showInactives" '.$check.' value="1" /> '.tr("Exibir mesmo os personagens inativos.").'';
				}
				
				$module .= '
			</p>		
			
			<p class="line"></p>
			
			<p>
				<input class="button" type="submit" value="'.tr("Enviar").' />
			</p>
		</fieldset>
	</form>';
	
	$page = 0;
	
	if($_GET["p"])
		$page = $_GET["p"];
		
	$start = $page * 20;
	
	if($skill == "experience" or $skill == "maglevel")
	{	
		$query_str = "
		SELECT 
			`id` 
		FROM 
			`players` 
		WHERE 	
			world_id = {$world_id}
			AND deleted = 0";
			
		if(!$filter_showInactivePlayers)
			$query_str .= " AND `lastlogin` > UNIX_TIMESTAMP() - ({$charactersActiveDays} * 60 * 60 * 24)";
		
		if(Configs::Get(Configs::eConf()->ENABLE_PVP_SWITCH, $world_id))
		{
			if($filter_pvp == "enabled")
				$query_str .= " AND `pvpEnabled` = 1";
			elseif($filter_pvp == "disabled")
				$query_str .= " AND `pvpEnabled` = 0";
		}
		
		$query_str .= "
		ORDER BY 
			";
		
		if(Configs::Get(Configs::eConf()->ENABLE_REBORN) && !$filter_hideRebornPlayers && $skill == "experience")
			$query_str .= "`reborn_level` DESC, ";
			
		$query_str .= "`{$skill}` DESC ";
		
		if($skill == "maglevel")
			$query_str .= ", `manaspent` DESC ";
		
		$query_str .= "LIMIT {$start}, 20";
			
		$query = \Core\Main::$DB->query($query_str);
	}
	elseif($skill == "bgrating")
	{
		$query_str = "
		SELECT
			`id`
		FROM
			`players`
		WHERE
			`world_id` = {$world_id}
			AND `deleted` = 0";
		
		if(!$filter_showInactivePlayers)
			$query_str .= " AND `lastlogin` > UNIX_TIMESTAMP() - ({$charactersActiveDays} * 60 * 60 * 24)";
		
		if(Configs::Get(Configs::eConf()->ENABLE_PVP_SWITCH, $world_id))
		{
			if($filter_pvp == "enabled")
				$query_str .= " AND `pvpEnabled` = 1";
			elseif($filter_pvp == "disabled")
			$query_str .= " AND `pvpEnabled` = 0";
		}
		
		$query_str .= "
		ORDER BY
		";
		
		if(Configs::Get(Configs::eConf()->ENABLE_REBORN) && !$filter_hideRebornPlayers && $skill == "experience")
			$query_str .= "`reborn_level` DESC, ";
		
		$query_str .= "`battleground_rating` DESC LIMIT {$start}, 20";
		
		$query = \Core\Main::$DB->query($query_str);	
	}
	else
	{
		$pvp_str = "";
		if(Configs::Get(Configs::eConf()->ENABLE_PVP_SWITCH))
		{
			if($filter_pvp == "enabled")
				$pvp_str = " AND `player`.`pvpEnabled` = 1";
			elseif($filter_pvp == "disabled")
				$pvp_str = " AND `player`.`pvpEnabled` = 0";
		}	
		
		$skillid = t_Skills::GetByString($skill);
		
		$query = \Core\Main::$DB->query("
			SELECT 
				`player`.`id`
			FROM 
				`players` as `player`
			LEFT JOIN
				`player_skills` as `skill`
			ON
				`skill`.`player_id` = `player`.`id`
			WHERE 
				`player`.`deleted` = 0 AND
				`player`.`world_id` = {$world_id} AND
				".((!$filter_showInactivePlayers) ? 
					"`player`.`lastlogin` + (60 * 60 * 24 * {$charactersActiveDays}) > ".time()." AND " : null)."				
				`player`.`id` = `skill`.`player_id` AND `skill`.`skillid` = {$skillid}
				{$pvp_str}
			ORDER BY 
				`skill`.`value` DESC,
				`skill`.`count` DESC
			LIMIT 
				{$start}, 20");
	}
	
	$now = 0;
	$page = 0;
	
	if(!$_GET["p"])
		$page = 1;
	else
	{
		$now = $_GET["p"];
		$page = $_GET["p"] + 1;
	}
		
	$ultima = 24;
	
	$pattern = array();
	$pattern[0] = '/&p=([0-9]{1,})/';
	
	$replace = array();
	$replace[0] = "";
	
	$regex_query = preg_replace($pattern, $replace, $_SERVER["QUERY_STRING"]);
		
	$module .= "<div>";
	
	if($now > 0)
		$module .= "<span style='margin-top: 10px; float: left;'><a href='?{$regex_query}'>Primeira</a> | <a href='?{$regex_query}&p=".($now - 1)."'>Anterior</a></span>";
	
	$module .= "<span style='margin-top: 10px; float: right;'>";	
	
	$havenext = false;
	
	if($now != $ultima)
	{
		$module .= "<a href='?{$regex_query}&p=".($now + 1)."'>".tr("Proximo")."</a>";
		$havenext = true;
	}		
	
	if($now < $ultima)
	{
		if($havenext)
		{
			$module .= " | ";
		}			
		
		$module .= "<a href='?{$regex_query}&p={$ultima}'>".tr("Última")."</a>";
	}
	
	$module .= "</span>";
	$module .= "</div>";
	
	$module .= "
	<table cellspacing='0' cellpadding='0' id='table'>
		<tr>
			<th width='5%'>&nbsp;</th> <th width='50%'>".tr("Nome")."</th> <th width='25%'>".tr("Vocação")."</th> <th>".tr("Nível")."</th> ".(($skill == "experience") ? "<th>".tr("Pontos")."</th>" : null)."
		</tr>	
	";
	
	$page = 0;
	
	if($_GET["p"])
		$page = $_GET["p"];
		
	$start = $page * 20;	
	
	$n = $start + 1;	
	
	while($fetch = $query->fetch())
	{		
		$player = new \Framework\Player();
		$player->load($fetch->id);
		
		if($player->getAccess() > t_Access::SeniorTutor)
			continue;
		
		if($skill == "experience")
		{
			$skill_value = $player->getLevel();
		}
		elseif($skill == "maglevel")
		{
			$skill_value = $player->getMagLevel();
		}
		elseif($skill == "bgrating")
		{
			$skill_value = $player->getBattlegroundRating();
		}
		else
		{
			$player->loadSkills();
			$skill_value = $player->getSkill($skillid);
		}
		
		$online = ($player->getOnline() == 1) ? "[<span class='online'>".tr("Online")."</span>]" : "";
		$guild = ($player->LoadGuild()) ? "<span style='font-size: 9px;'><br>".tr("Membro da guild")." <a href='?ref=guilds.details&name={$player->GetGuildName()}'>{$player->GetGuildName()}</a><span>" : "<span style='font-size: 9px;'><br>".tr("Não pertence a nenhuma guild.")."";
		
		$_vocation = new t_Vocation($player->getVocation());
		
		$skull_img = Framework\Player::getSkullImg($player);
		
		$module .= "
			<tr>
				<td>{$n}.</td> 
				<td class='name'><a style='font-size: 14px;' href='?ref=character.view&name={$player->getName()}'>{$player->getName()}</a> {$skull_img} {$online} {$guild} </td> 
				<td>{$_vocation->GetByName()}</td> 
				<td>{$skill_value}</td> 
				".(($skill == "experience") ? "<td>".number_format($player->getExperience())."</td>" : null)."
			</tr>
		";
		
		$n++;
	}
	
	$module .= "
	</table>
	";
}
?>