<?php
$query = $db->query("SELECT * FROM player_deaths WHERE time > ".(time() - (60 * 60 * 2))." ORDER BY time DESC");

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th colspan='2'>Últimas Mortes</th>
	</tr>";		

if($query->numRows() != 0)
{
	while($fetch = $query->fetch())
	{
		$deathPlayer = $core->loadClass("character");
		$deathPlayer->load($fetch->player_id, "name");		
		
		$monsters = $core->loadClass("monsters");
				
		$time = $core->formatDate($fetch->time);
		
		$death = "{$deathPlayer->getName()} foi morto no nivel {$values['level']} por ";
		
		if($fetch->killed_by == "field item")
		{
			$death .= "um campo de dano";
		}	
		elseif($monsters->load($fetch->killed_by))
		{
			$death .= "um ".$fetch->altkilled_by;
		}	
		elseif(is_int($fetch->killed_by))
		{
			$Killer = $core->loadClass("character");	
			$Killer->load($fetch->killed_by);
			
			$death .= "<a href='?ref=character.view&name={$Killer->getName()}'>{$Killer->getName()}</a>";
		}
		else
		{
			$death .= "<a href='?ref=character.view&name={$fetch->killed_by}'>{$fetch->killed_by}</a>";
		}	
			
		if($fetch->altkilled_by)	
		{
			if($fetch->altkilled_by == "field item")
			{
				$death .= "e um campo de dano";		
			}				
			elseif($monsters->load($fetch->altkilled_by))
			{
				$death .= " e um ".$fetch->altkilled_by;
			}
			elseif(is_int($fetch->altkilled_by))	
			{
				$altKiller = $core->loadClass("character");	
				$altKiller->load($fetch->altkilled_by);
				
				$death .= " e por <a href='?ref=character.view&name={$altKiller->getName()}'>{$altKiller->getName()}</a>";			
			}
			else
			{
				$death .= " e por <a href='?ref=character.view&name={$fetch->altkilled_by}'>{$fetch->altkilled_by}</a>";		
			}	
		}	

		$death .= ".";
		
		$module .= "
			<tr>
				<td witdh='20%'>{$time}</td> <td>{$death}</td>
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
