<?
$filter_onlyOnIOP = false;

if(ENABLE_REBORN_SYSTEM)
	$filter_hideRebornPlayers = false;
	
$filter_inactivePlayers = false;

if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS != 0)
	$filter_inactivePlayers = true;

if($_GET["p"] && $_GET["p"] > 24)
	$_GET["p"] = 24;
	

if(isset($_POST['skill']))
{
	Core::redirect("?ref=community.highscores&skill={$_POST['skill']}");
}

($_POST['show_onlyPeacers'] == 1) ? setCookie("filter_onlyOnIOP", 1) : setCookie("filter_onlyOnIOP", 0);

if(ENABLE_REBORN_SYSTEM)
	($_POST['hide_rebornPlayers'] == 1) ? setCookie("filter_hideRebornPlayers", 1) : setCookie("filter_hideRebornPlayers", 0);

($_COOKIE['filter_onlyOnIOP'] == 1) ? $filter_onlyOnIOP = true : null;

if(ENABLE_REBORN_SYSTEM)
	($_COOKIE['filter_hideRebornPlayers'] == 1) ? $filter_hideRebornPlayers = true : null;

if(isset($_GET['skill']))
{	
	$skill = $_GET['skill'];	
}
else
{
	$skill = "experience";
}

if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS != 0)
{
	$module .= '
	<p> Este highscores mostra apenas personagens <b>ativos</b> no jogo (Apenas os que não estão inativos a menos de 7 dias atrás).
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
}

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
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
		</p>	

		<p>		
			<label for="filter">Filtros</label><br />
			<input '.(($filter_onlyOnIOP) ? 'checked="checked"' : null).' name="show_onlyPeacers" type="checkbox" value="1" /> Exibir apénas personagens em Island of Peace. <br>
			'.((ENABLE_REBORN_SYSTEM) ? '<input '.(($filter_hideRebornPlayers) ? 'checked="checked"' : null).' name="hide_rebornPlayers" type="checkbox" value="1" /> Ocultar personagens renascidos (somente para experience).' :  '').'
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
	$query = $db->query("
		SELECT 
			id 
		FROM 
			players 
		WHERE 
			".(($filter_onlyOnIOP) ? 
				"town_id = 6 AND" : null)."
			".(($filter_inactivePlayers) ? 
				" lastlogin + (60 * 60 * 24 * ".HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS.") > ".time()." AND " : null)."
			group_id < 3 ORDER BY ".((ENABLE_REBORN_SYSTEM && !$filter_hideRebornPlayers && $skill == "experience") ? "reborn_level DESC," : null)." {$skill} DESC LIMIT {$start}, 20");
}
else
{
	$skillid = $_skill[$skill];
	$query = $db->query("
		SELECT 
			player.id 
		FROM 
			players as player, player_skills as skill 
		WHERE 
			".(($filter_onlyOnIOP) ? 
				"player.town_id = 6 AND" : null)."
			".(($filter_inactivePlayers) ? 
				"player.lastlogin + (60 * 60 * 24 * ".HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS.") > ".time()." AND " : null)."				
			player.id = skill.player_id AND skill.skillid = {$skillid} AND player.group_id < 3
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