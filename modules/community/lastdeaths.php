<?php
$query = $db->query("SELECT id, player_id FROM player_deaths WHERE date > ".(time() - (60 * 60 * 4))." ORDER BY date DESC");

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th colspan='2'>Últimas Mortes</th>
	</tr>";		

if($query->numRows() != 0)
{
	while($fetch = $query->fetch())
	{
		$deaths = $core->loadClass("Deaths");
		
		$death_values = $deaths->load($fetch->id);
		
		$deathPlayer = $core->loadClass("character");
		$deathPlayer->load($fetch->player_id, "name");		
				
		$date = $core->formatDate($death_values['date']);
		
		$death = "<a href='?ref=character.view&name={$deathPlayer->getName()}'>{$deathPlayer->getName()}</a> foi morto no nivel {$death_values['level']} por ";
		
		if($death_values['killer_is_env'] == 1)
		{
			$death .= "um ".$death_values['killed_by'];
		}	
		else
		{
			$Killer = $core->loadClass("character");	
			$Killer->load($death_values['killed_by']);
			
			$death .= "<a href='?ref=character.view&name={$Killer->getName()}'>{$Killer->getName()}</a>";
		}
			
		if($death_values['alt_killed_by'])	
		{			
			if($death_values['alt_killer_is_env'] == 1)
			{
				$death .= "um ".$death_values['altkilled_by'];
			}
			else
			{
				$altKiller = $core->loadClass("character");	
				$altKiller->load($death_values['altkilled_by']);
				
				$death .= " e por <a href='?ref=character.view&name={$altKiller->getName()}'>{$altKiller->getName()}</a>";			
			}	
		}	

		$death .= ".";
		
		$module .= "
			<tr>
				<td witdh='20%'>{$date}</td> <td>{$death}</td>
			</tr>					
		";			
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
