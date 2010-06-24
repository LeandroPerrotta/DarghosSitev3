<?
if($_GET["p"] && $_GET["p"] > 24)
	$_GET["p"] = 24;
	

if(isset($_POST['skill']))
{
	($_POST['show_onlyPeacers'] == 1) ? Core::redirect("?ref=community.highscores&skill={$_POST['skill']}&filter=1") : Core::redirect("?ref=community.highscores&skill={$_POST['skill']}");
}

if(isset($_GET['skill']))
{
	if(isset($_GET['filter']))
	{
		$filter = $_GET['filter'];	
	}
	
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
			<input '.((isset($filter) ? 'checked="checked"' : null)).' name="show_onlyPeacers" type="checkbox" value="1" /> Exibir apénas personagens em Island of Peace.
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
	if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS != 0)
		$query = $db->query("SELECT id FROM players WHERE ".((isset($filter)) ? "town_id = 6 AND" : null)." group_id < 3 AND lastlogin + (60 * 60 * 24 * ".HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS.") > ".time()." ORDER BY {$skill} DESC LIMIT {$start}, 20");
	else
		$query = $db->query("SELECT id FROM players WHERE ".((isset($filter)) ? "town_id = 6 AND" : null)." group_id < 3 ORDER BY {$skill} DESC LIMIT {$start}, 20");
}
else
{
	$skillid = $_skill[$skill];
	if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS != 0)
		$query = $db->query("SELECT player.id FROM players as player, player_skills as skill WHERE ".((isset($filter)) ? "player.town_id = 6 AND" : null)." player.id = skill.player_id AND skill.skillid = {$skillid} AND player.group_id < 3 AND player.lastlogin < '".(time() - (60 * 60 * 24 * HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS))."' ORDER BY skill.value DESC LIMIT {$start}, 20");
	else
		$query = $db->query("SELECT player.id FROM players as player, player_skills as skill WHERE ".((isset($filter)) ? "player.town_id = 6 AND" : null)." player.id = skill.player_id AND skill.skillid = {$skillid} AND player.group_id < 3 ORDER BY skill.value DESC LIMIT {$start}, 20");
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

if($_GET['filter'])
	$_filter = "&filter={$_GET["filter"]}";	
	
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
		<th width='5%'>&nbsp;</th> <th width='50%'>Nome</th> <th>Vocação</th> <th>Nível</th> ".(($skill == "experience") ? "<th>Pontos</th>" : null)."
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
	
	$module .= "
		<tr>
			<td>{$n}.</td> <td class='name'><a href='?ref=character.view&name={$character->getName()}'>{$character->getName()}</a> $online</td> <td>{$_vocationid[$character->getVocation()]}</td> <td>{$skill_value}</td> ".(($skill == "experience") ? "<td>".number_format($character->getExperience())."</td>" : null)."
		</tr>
	";
	
	$n++;
}

$module .= "
</table>
";
?>