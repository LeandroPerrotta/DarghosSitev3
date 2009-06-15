<?
if(isset($_POST['skill']))
{
	$core->redirect("?ref=community.highscores&skill={$_POST['skill']}");
}

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
				<option '.(($skill == "experience") ? 'selected' : null).' value="experience">Nível de Expêriencia</option>
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
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

if($skill == "experience" or $skill == "maglevel")
{
	if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS != 0)
		$query = $db->query("SELECT id FROM players WHERE group_id < 3 AND lastlogin + (60 * 60 * 24 * ".HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS.") > ".time()." ORDER BY {$skill} DESC LIMIT 100");
	else
		$query = $db->query("SELECT id FROM players WHERE group_id < 3 ORDER BY {$skill} DESC LIMIT 100");
}
else
{
	$skillid = $_skill[$skill];
	if(HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS != 0)
		$query = $db->query("SELECT player.id FROM players as player, player_skills as skill WHERE player.id = skill.player_id AND skill.skillid = {$skillid} AND player.group_id < 3 AND player.lastlogin < '".(time() - (60 * 60 * 24 * HIGHSCORES_IGNORE_INACTIVE_CHARS_DAYS))."' ORDER BY skill.value DESC LIMIT 100");
	else
		$query = $db->query("SELECT player.id FROM players as player, player_skills as skill WHERE player.id = skill.player_id AND skill.skillid = {$skillid} AND player.group_id < 3 ORDER BY skill.value DESC LIMIT 100");
}

$character = $core->loadClass("Character");

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th width='5%'>&nbsp;</th> <th width='50%'>Nome</th> <th>Vocação</th> <th>Nível</th> ".(($skill == "experience") ? "<th>Pontos</th>" : null)."
	</tr>	
";

while($fetch = $query->fetch())
{
	$n++;	
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
}

$module .= "
</table>
";
?>