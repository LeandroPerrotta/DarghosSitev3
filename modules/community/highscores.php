<?
$filter_onlyOnIOP = false;

if(ENABLE_REBORN_SYSTEM)
	$filter_hideRebornPlayers = false;
	
if($_GET["p"] && $_GET["p"] > 24)
	$_GET["p"] = 24;

($_POST['show_onlyPeacers'] == 1) ? setCookie("filter_onlyOnIOP", 1) : setCookie("filter_onlyOnIOP", 0);
($_COOKIE['filter_onlyOnIOP'] == 1) ? $filter_onlyOnIOP = true : null;

if(ENABLE_REBORN_SYSTEM)
{
	($_POST['hide_rebornPlayers'] == 1) ? setCookie("filter_hideRebornPlayers", 1) : setCookie("filter_hideRebornPlayers", 0);
	($_COOKIE['filter_hideRebornPlayers'] == 1) ? $filter_hideRebornPlayers = true : null;
}

$filter_showInactivePlayers = true;

if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS > 0)
{		
	($_POST['show_inactivePlayers'] == 1) ? setCookie("showInactivePlayers", 1) : setCookie("showInactivePlayers", 0);
	$filter_showInactivePlayers = ($_COOKIE['showInactivePlayers'] == 1) ? true : false;
}

if(ENABLE_PVP_SWITCH) $pvp = isset($_GET['pvp']) ? $_GET['pvp'] : "both";

if(isset($_POST['skill']))
{
	Core::redirect("?ref=community.highscores&skill={$_POST['skill']}");
}

$skill = isset($_GET['skill']) ? $_GET['skill'] : "experience";

if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS != 0 && !$filter_showInactivePlayers)
{
	$module .= '
	<p> <b>Obs:</b> Este highscores mostra apenas personagens <b>ativos</b> nos ultimos ' .HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS. ' dias.';
}

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">
	<fieldset>
		<p>		
			<label for="skill">Tipo de Habilidade</label><br />
			<select name="skill">
				<option '.(($skill == "experience") ? 'selected' : null).' value="experience">Nível de Experiência</option>
				<option '.(($skill == "maglevel") ? 'selected' : null).' value="maglevel">Nível Mágico</option>
				<option '.(($skill == "sword") ? 'selected' : null).' value="sword">Batalha com Espada</option>
				<option '.(($skill == "axe") ? 'selected' : null).' value="axe">Batalha com Machado</option>
				<option '.(($skill == "club") ? 'selected' : null).' value="club">Batalha com Martelo</option>
				<option '.(($skill == "fist") ? 'selected' : null).' value="fist">Batalha com Mãos</option>
				<option '.(($skill == "shield") ? 'selected' : null).' value="shield">Habilidade com Escudo</option>
				<option '.(($skill == "distance") ? 'selected' : null).' value="distance">Pontaria à Distancia</option>
				<option '.(($skill == "fishing") ? 'selected' : null).' value="fishing">Habilidade de Pesca</option>
			</select>
		</p>';

		if(ENABLE_PVP_SWITCH)
		{
			$module .= '
			<p>		
				<label for="skill">Tipo de PvP</label><br />
				<select name="pvp">
					<option '.(($pvp == "both") ? 'selected' : null).' value="both">Todos</option>
					<option '.(($pvp == "enabled") ? 'selected' : null).' value="enabled">Agressivos apénas</option>
					<option '.(($pvp == "disabled") ? 'selected' : null).' value="disabled">Pacificos apénas</option>
				</select>
			</p>';	
		}

		$module .= '
		<p>		
			<label for="filter">Filtros</label><br />
			<input '.(($filter_onlyOnIOP) ? 'checked="checked"' : null).' name="show_onlyPeacers" type="checkbox" value="1" /> Exibir apénas personagens em Island of Peace. <br>
			'.((ENABLE_REBORN_SYSTEM) ? '<input '.(($filter_hideRebornPlayers) ? 'checked="checked"' : null).' name="hide_rebornPlayers" type="checkbox" value="1" /> Ocultar personagens renascidos (somente para experience).' :  '');
			
			if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS > 0)
			{
				$check = ($filter_showInactivePlayers) ? 'checked="checked"' : '';
				
				$module .= '<input type="checkbox" name="show_inactivePlayers" '.$check.' value="1" /> Exibir mesmo os personagens inativos.';
			}
			
			$module .= '
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
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
		group_id < 3 
	";
	
	if($filter_onlyOnIOP)
		$query_str .= " AND `town_id` = 6";
		
	if(!$filter_showInactivePlayers)
		$query_str .= " AND `lastlogin` > UNIX_TIMESTAMP() - (". HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS. " * 60 * 60 * 24)";
	
	if(ENABLE_PVP_SWITCH)
	{
		if($pvp == "enabled")
			$query_str = " AND `pvpEnabled` = 1";
		elseif($pvp == "disabled")
			$query_str = " AND `pvpEnabled` = 0";
	}
	
	$query_str .= "
	ORDER BY 
		";
	
	if(ENABLE_REBORN_SYSTEM && !$filter_hideRebornPlayers && $skill == "experience")
		$query_str .= "reborn_level DESC, ";
		
	$query_str .= "{$skill} DESC LIMIT {$start}, 20";
		
	$query = Core::$DB->query($query_str);
}
else
{
	$pvp_str = "";
	if(ENABLE_PVP_SWITCH)
	{
		if($pvp == "enabled")
			$pvp_str = " AND `player`.`pvpEnabled` = 1";
		elseif($pvp == "disabled")
			$pvp_str = " AND `player`.`pvpEnabled` = 0";
	}	
	
	$skillid = $_skill[$skill];
	$query = $db->query("
		SELECT 
			player.id 
		FROM 
			players as player, player_skills as skill 
		WHERE 
			".(($filter_onlyOnIOP) ? 
				"player.town_id = 6 AND" : null)."
			".((!$filter_showInactivePlayers) ? 
				"player.lastlogin + (60 * 60 * 24 * ".HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS.") > ".time()." AND " : null)."				
			player.id = skill.player_id AND skill.skillid = {$skillid} AND player.group_id < 3
			{$pvp_str}
		ORDER BY 
			skill.value DESC 
		LIMIT 
			{$start}, 20");
}

$character = new Character();

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

$_skill = "";	
	
if($_GET['skill'])	
	$_skill = "&skill={$_GET['skill']}";

if($_GET['only_peacers'])
	$_filter = "&filter={$_GET["only_peacers"]}";	
	
$module .= "<div>";

if($now > 0)
	$module .= "<span style='margin-top: 10px; float: left;'><a href='?ref=community.highscores{$_skill}{$_filter}'>Primeira</a> | <a href='?ref=community.highscores{$_skill}{$_filter}&p=".($now - 1)."'>Anterior</a></span>";

$module .= "<span style='margin-top: 10px; float: right;'>";	

$havenext = false;

if($now != $ultima)
{
	$module .= "<a href='?ref=community.highscores{$_skill}{$_filter}&p=".($now + 1)."'>Proximo</a>";
	$havenext = true;
}		

if($now < $ultima)
{
	if($havenext)
	{
		$module .= " | ";
	}			
	
	$module .= "<a href='?ref=community.highscores{$_skill}{$_filter}&p={$ultima}'>Ultima</a>";
}

$module .= "</span>";
$module .= "</div>";

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th width='5%'>&nbsp;</th> <th width='50%'>Nome</th> <th width='25%'>Vocação</th> <th>Nível</th> ".(($skill == "experience") ? "<th>Pontos</th>" : null)."
	</tr>	
";

$page = 0;

if($_GET["p"])
	$page = $_GET["p"];
	
$start = $page * 20;	

$n = $start + 1;	

while($fetch = $query->fetch())
{		
	$character->load($fetch->id);
	
	if($skill == "experience")
	{
		$skill_value = $character->getLevel();
	}
	elseif($skill == "maglevel")
	{
		$skill_value = $character->getMagLevel();
	}
	else
	{
		$character->loadSkills();
		$skill_value = $character->getSkill($skillid);
	}
	
	$online = ($character->getOnline() == 1) ? "[<span class='online'>Online</span>]" : "";
	$guild = ($character->LoadGuild()) ? "<span style='font-size: 9px;'><br>Membro da guild <a href='?ref=guilds.details&name={$character->GetGuildName()}'>{$character->GetGuildName()}</a><span>" : "<span style='font-size: 9px;'><br>Não pertence a nenhuma guild.";
	
	$module .= "
		<tr>
			<td>{$n}.</td> <td class='name'><a style='font-size: 14px;' href='?ref=character.view&name={$character->getName()}'>{$character->getName()}</a> {$online} {$guild} </td> <td>{$_vocationid[$character->getVocation()]}</td> <td>{$skill_value}</td> ".(($skill == "experience") ? "<td>".number_format($character->getExperience())."</td>" : null)."
		</tr>
	";
	
	$n++;
}

$module .= "
</table>
";
?>