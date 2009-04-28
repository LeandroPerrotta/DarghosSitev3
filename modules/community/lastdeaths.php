<?php
$query = $db_tenerian->query("SELECT * FROM player_deaths WHERE time > ".(time() - (60 * 60 * 2))." ORDER BY time DESC");

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th>Últimas Mortes</th>
	</tr>";		

if($query->numRows() != 0)
{
	while($fetch = $query->fetch())
	{
		$killer = ($fetch->killed_by != "-1") ? $fetch->killed_by : "field";
		
		$character = $core->loadClass("character");
		$character->load($fetch->player_id, "name");
		
		if($fetch->is_player == 1)
		{
			$module .= "
			<tr>
				<td><a href='?ref=character.view&name={$character->get("name")}'>{$character->get("name")}</a> morreu no nivel {$fetch->level} para <a href='?ref=character.view&name={$killer}'>{$killer}</a> em {$core->formatDate($fetch->time)}.</td>
			</tr>";		
		}
		else
		{
			$module .= "
			<tr>
				<td><a href='?ref=character.view&name={$character->get("name")}'>{$character->get("name")}</a> morreu no nivel {$fetch->level} para um {$killer} em {$core->formatDate($fetch->time)}.</td>
			</tr>";					
		}
	}
}
else
{
	$module .= "
	<tr>
		<td colspan='2'>Não foi registrada nenhuma morte no servidor nas últimas 2 horas.</td>
	</tr>";		
}

$module .= "
</table>";		

?>
