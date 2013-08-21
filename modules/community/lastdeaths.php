<?php
\Core\Main::requireWorldSelection();

function getTimeString($minutes){
	if($minutes <= 1)
		return tr("A menos de um minuto.");
		
	if($minutes >= 60){
		$hours = 0;
		$mins = $minutes;
	
		do{
			$mins -= 60;
			$hours++;
		}while($mins >= 60);
		
		$time = $mins > 0 ? tr("A @v1@ hora(s) e @v2@ minuto(s)", $hours, $mins) : tr("A @v1@ hora(s)", $hours);
	}
	else{
		$time = tr("A @v1@ minuto(s)", $minutes);
	}		
	
	return $time;
}

if(isset($_GET["world"]))
{
	$world_id = (int)$_GET["world"];

	if(!t_Worlds::Get($world_id))
		$world_id = t_Worlds::Ordon;	
	
	$query = \Core\Main::$DB->query("SELECT `player_deaths`.`id`, `player_deaths`.`player_id` FROM `player_deaths` LEFT JOIN `players` ON `players`.`id` = `player_deaths`.`player_id` WHERE `players`.`deleted` = 0 AND `players`.`world_id` = {$world_id} ORDER BY `player_deaths`.`date` DESC LIMIT ".Core\Configs::Get(Core\Configs::eConf()->SHOW_LAST_DEATHS_LIMIT)."");
	
	$module .= "
	<table cellspacing='0' cellpadding='0' id='table'>
		<tr>
			<th>".tr("Últimas Mortes")."</th>
		</tr>";		
	
	if($query->numRows() != 0)
	{
	    $current_minute = 0;
	    
		$changedMinute = true;
		
		while($fetch = $query->fetch())
		{
			$deaths = new \Framework\Deaths();
			
			$death_values = $deaths->load($fetch->id);
			
			$deathPlayer = new \Framework\Player();
			$deathPlayer->load($fetch->player_id);		
			
			$minutes = floor((time() - $death_values['date']) / 60);
			
			if($minutes > $current_minute){
			    
				if($changedMinute){
					$time = getTimeString($minutes);
				
					$module .= "
					<tr>
						<td heigth='35' style='opacity: 0.90; filter: alpha(opacity=90); background-color: #6a6a62;'><b>{$time}.</b></td>
					</tr>
					";			        
				}
				
				$changedMinute = $current_minute == $minutes ? false : true;
				$current_minute = $minutes;
			}
			
			$skull_img = Framework\Player::getSkullImg($deathPlayer);
			$death = "<a href='?ref=character.view&name={$deathPlayer->getName()}'>{$deathPlayer->getName()}</a> {$skull_img} ".tr("foi morto no nivel @v1@ por ", $death_values['level']);
			
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
									$death .= " " . tr("e por um(a) ") . " ";
								}	
								else	
									$death .= " " . tr("e por")  . " ";
							}
						}
						else
						{
							if($killer["isEnv"] == 1)
							{			
								$death .= " " . tr("e por um(a)")  . " ";
							}	
							else	
								$death .= " " . tr("e por")  . " ";
						}	
					}		
					
					if($killer["isEnv"] == 1)
					{
						if($k == 1)
							$death .= tr("um(a)") . " ";
						
						$explodeKiller = explode(" ", $killer['killer'], 2);
						
						$death .= "".$explodeKiller[1];
					}
					else
					{
						$_killer = new \Framework\Player();	
						$_killer->load($killer['killer']);	
						
						if($deathPlayer->getId() == $_killer->getId())
						    continue;
	                    
						$skull_img = Framework\Player::getSkullImg($_killer);
						$death .= "<a href='?ref=character.view&name={$_killer->getName()}'>{$_killer->getName()}</a> {$skull_img}";
					}
				}
			}	
	
			$death .= ".";
			
			$module .= "
				<tr>
					<td>{$death}</td>
				</tr>					
			";			
		}
	}
	else
	{
		$module .= "
		<tr>
			<td colspan='2'>".tr("Não foi registrada nenhuma morte no servidor nas últimas 2 horas.")."</td>
		</tr>";		
	}
	
	$module .= "
	</table>";
}

?>
