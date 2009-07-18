<?php
$query = $db->query("SELECT id, player_id FROM player_deaths WHERE date > '".(time() - 60 * 60 * 2)."' ORDER BY date DESC");

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th colspan='2'>�ltimas Mortes</th>
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
		
		if(count($death_values['killers']) != 0)
		{
			$k = 0;
			foreach($death_values['killers'] as $killer)
			{
				$k++;

				if($k > 1)
				{
					if(count($death_values['killers']) != 2)
					{
						if($k < count($death_values['killers']) - 1)
						{
							$death .= ", ";
						}
						elseif($k == count($death_values['killers']) - 1)
						{
							if($killer["isEnv"] == 1)
							{			
								$death .= " e por um(a) ";
							}	
							else	
								$death .= " e por ";
						}
					}
					else
					{
						if($killer["isEnv"] == 1)
						{			
							$death .= " e por um(a) ";
						}	
						else	
							$death .= " e por ";
					}	
				}		
				
				if($killer["isEnv"] == 1)
				{
					if($k == 1)
						$death .= "um(a) ";
					
					$explodeKiller = explode(" ", $killer['killer'], 2);
					
					$death .= "".$explodeKiller[1];
				}
				else
				{
					$_killer = $core->loadClass("character");	
					$_killer->load($killer['killer']);	

					$death .= "<a href='?ref=character.view&name={$_killer->getName()}'>{$_killer->getName()}</a>";
				}
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
		<td colspan='2'>N�o foi registrada nenhuma morte no servidor nas �ltimas 2 horas.</td>
	</tr>";		
}

$module .= "
</table>";		

?>
