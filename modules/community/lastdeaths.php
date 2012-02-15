<?php
\Core\Main::requireWorldSelection();

if(isset($_GET["world"]))
{
	$world_id = (int)$_GET["world"];

	if(!t_Worlds::Get($world_id))
		$world_id = t_Worlds::Ordon;	
	
	$query = \Core\Main::$DB->query("SELECT `player_deaths`.`id`, `player_deaths`.`player_id` FROM `player_deaths` LEFT JOIN `players` ON `players`.`id` = `player_deaths`.`player_id` WHERE `players`.`deleted` = 0 AND `players`.`world_id` = {$world_id} AND `player_deaths`.`date` > '".(time() - 60 * 60 * 2)."' ORDER BY `player_deaths`.`date` DESC");
	
	$module .= "
	<table cellspacing='0' cellpadding='0' id='table'>
		<tr>
			<th colspan='2'>Últimas Mortes</th>
		</tr>";		
	
	if($query->numRows() != 0)
	{
		while($fetch = $query->fetch())
		{
			$deaths = new \Framework\Deaths();
			
			$death_values = $deaths->load($fetch->id);
			
			$deathPlayer = new \Framework\Player();
			$deathPlayer->load($fetch->player_id);		
					
			$date = \Core\Main::formatDate($death_values['date']);
			
			$death = "<a href='?ref=character.view&name={$deathPlayer->getName()}'>{$deathPlayer->getName()}</a> foi morto no nivel {$death_values['level']} por ";
			
			if(count($death_values['killers']) != 0)
			{
				$k = 0;
				foreach($death_values['killers'] as $killer)
				{
					$k++;
	
					if($k > 1)
					{
						if(count($death_values['killers']) > 2)
						{
							if($k < count($death_values['killers']))
							{
								$death .= ", ";
							}
							elseif($k == count($death_values['killers']))
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
						$_killer = new \Framework\Player();	
						$_killer->load($killer['killer']);	
	
						$death .= "<a href='?ref=character.view&name={$_killer->getName()}'>{$_killer->getName()}</a>";
					}
				}
			}	
	
			$death .= ".";
			
			$module .= "
				<tr>
					<td witdh='30%'>{$date}</td> <td>{$death}</td>
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
}

?>
