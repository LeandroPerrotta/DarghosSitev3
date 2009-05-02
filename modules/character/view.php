<?
$post = $core->extractPost();
$get = $_GET['name'];

if($post or $get)
{	
	$name = ($post) ? $post[0] : $get;

	$character = $core->loadClass("character");
	
	if(!$character->loadByName($name, "name, account_id, level, sex, vocation, town_id, lastlogin, comment, hide, rank_id, guildnick"))
	{	
		$error = "O personagem <b>".stripcslashes($name)."</b> não existe.";
	}
	else
	{			
		$account = $core->loadClass("Account");
		$account->load($character->get("account_id"), "premdays, real_name, location, url");
		
		//$bans = $account->getBans();
		$bans = $core->loadClass('bans');
		
		$houseid = $character->getHouse();
		$lastlogin = ($character->get("lastlogin")) ? $core->formatDate($character->get("lastlogin")) : "Nunca entrou.";
		
		$premium = ($account->get("premdays") != 0) ? "<font style='color: green; font-weight: bold;'>Conta Premium" : "Conta Gratuita";	
		$realname = ($account->get("real_name") != "") ? $account->get("real_name") : "não configurado";
		$location = ($account->get("location") != "") ? $account->get("location") : "não configurado";
		$url = ($account->get("url") != "") ? $account->get("url") : "não configurado";
		
		$deathlist = $character->loadLastDeaths();
		$list = $account->getCharacterList();
		$oldnames = $character->loadOldNames();
	
		$module .= "
		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='2'>Personagem</th>
			</tr>";		

			if($character->deletionStatus())
			{		
				$module .= "
				<tr>
					<td colspan='2'><font style='color: red; font-weight: bold;'>Este personagem está agendado para ser deletado no dia {$core->formatDate($character->deletionStatus())}.</font></td>
				</tr>";				
			}
		
			$module .= "
			<tr>
				<td width='25%'><b>Nome:</b></td> <td>{$character->get("name")}</td>
			</tr>";
			
			if($oldnames)
			{		
				foreach($oldnames as $name => $time)
				{
					$i++;
						
					$oldnames_string .= $name;

					if($i != count($oldnames))
						$oldnames_string .= ", ";		
				}
				
				$module .= "
				<tr>
					<td width='25%'><b>Nomes Antigos:</b></td> <td>{$oldnames_string}</td>
				</tr>";				
			}			
			
			$module .= "
			<tr>
				<td><b>Level:</b></td> <td>{$character->get("level")}</td>
			</tr>	

			<tr>
				<td><b>Sexo:</b></td> <td>{$_sexid[$character->get("sex")]}</td>
			</tr>	

			<tr>
				<td><b>Vocação:</b></td> <td>{$_vocationid[$character->get("vocation")]}</td>
			</tr>	

			<tr>
				<td><b>Residencia:</b></td> <td>{$_townid[$character->get("town_id")]["name"]}</td>
			</tr>";	

			if($houseid)
			{
				$houses = $core->loadClass("Houses");
				$houses->load($houseid);				
				
				$module .= "
				<tr>
					<td><b>Casa</b></td> <td>{$houses->get("name")} ({$_townid[$houses->get("townid")]["name"]}) com pagamento no dia  {$core->formatDate($houses->get("paid"))}</td>
				</tr>";						
			}
			
			if($character->loadGuild())
			{
				$module .= "
				<tr>
					<td><b>Membro da Guild</b></td> <td>{$character->getGuildInfo("rank_name")} da <a href='?ref=guilds.details&name={$character->getGuildInfo("name")}'>{$character->getGuildInfo("name")}</a></td>
				</tr>";					
			}
			
			if($character->get("comment"))
			{
				$module .= "
				<tr>
					<td><b>Comentario</b></td> <td>".nl2br(strip_tags($character->get("comment")))."</td>
				</tr>";					
			}
			
			$module .= "
			<tr>
				<td><b>Último Login:</b></td> <td>{$lastlogin}</td>
			</tr>	
			
		</table>

		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='2'>Informações da Conta</th>
			</tr>";
			
			if($bans->isBannished($account->getId()))
			{
				$ban = $bans->getBannishment($account->getId());
						
				if($ban['type'] == 3 OR $ban['type'] == 5)
				{
					$banstring .= "<font color='red'>";
					
					if($ban['type'] == 3)
					{
						$banstring .= "Banido por: <b>{$tools->getBanReason($ban['reason'])}</b><br>
								   	   Duração: Até {$core->formatDate($ban['expires'])}.";
					}
					elseif($ban['type'] == 5)	
					{
						$banstring .= "Deletado por: <b>{$tools->getBanReason($ban['reason'])}</b><br>
								   	   Duração: permanentemente.";		
					}			   	   				   	   
								   
					$banstring .= "</font>";
					
					$module .= "
					<tr>
						<td width='25%'><b>Punição:</b></td> <td>{$banstring}</td>
					</tr>";			
				}
			}

			$module .= "
			<tr>
				<td width='25%'><b>Tipo de Conta:</b></td> <td>{$premium}</td>
			</tr>	
			
			<tr>
				<td><b>Nome Real:</b></td> <td>{$realname}</td>
			</tr>	

			<tr>
				<td><b>Location:</b></td> <td>{$location}</td>
			</tr>
			
			<tr>
				<td><b>Website:</b></td> <td>{$url}</td>
			</tr>						
		</table>";

		if(is_array($deathlist))
		{
			$module .= "
			
			<table cellspacing='0' cellpadding='0'>
				<tr>
					<th colspan='2'>Mortes Recentes</th>
				</tr>					
			";
			
			foreach($deathlist as $i => $values)
			{
				$monsters = $core->loadClass("monsters");
				
				$time = $core->formatDate($values['time']);
				
				$death = "Morto no Nivel {$values['level']} por ";
				
				if($values['killed_by'] == "field item")
				{
					$death .= "um campo de dano";
				}	
				elseif($monsters->load($values['killed_by']))
				{
					$death .= "um ".$values['killed_by'];
				}	
				elseif(is_int($values['killed_by']))
				{
					$Killer = $core->loadClass("character");	
					$Killer->load($values['killed_by']);
					
					$death .= "<a href='?ref=character.view&name={$Killer->getName()}'>{$Killer->getName()}</a>";
				}
				else
				{
					$death .= "<a href='?ref=character.view&name={$values['killed_by']}'>{$values['killed_by']}</a>";
				}	
					
				if($values['altkilled_by'])	
				{
					if($values['altkilled_by'] == "field item")
					{
						$death .= "e um campo de dano";		
					}				
					elseif($monsters->load($values['altkilled_by']))
					{
						$death .= " e um ".$values['altkilled_by'];
					}
					elseif(is_int($values['altkilled_by']))	
					{
						$altKiller = $core->loadClass("character");	
						$altKiller->load($values['killed_by']);
						
						$death .= " e por <a href='?ref=character.view&name={$altKiller->getName()}'>{$altKiller->getName()}</a>";			
					}
					else
					{
						$death .= " e por <a href='?ref=character.view&name={$values['altkilled_by']}'>{$values['altkilled_by']}</a>";		
					}	
				}	

				$death .= ".";
				
				$module .= "
					<tr>
						<td witdh='10%'>{$time}</td> <td>{$death}</td>
					</tr>					
				";	
			}
			
			$module .= "
			</table>";		
		}
		
		if($character->get("hide") == 0)
		{
			$module .= "
			<table cellspacing='0' cellpadding='0'>
				<tr>
					<th colspan='3'>Outros Personagens</th>
				</tr>					
			";			
			
			foreach($list as $player_name)
			{
				$character_list = $core->loadClass("character");
				$character_list->loadByName($player_name, "name, level, online, hide");
				
				if($character_list->get("hide") == 0)
				{
					$character_status = ($character_list->getOnline() == 1) ? "<font style='color: green; font-weight: bold;'>On-line</font>" : "<font style='color: red; font-weight: bold;'>Off-line</font>";
					
					$module .= "
						<tr>
							<td width='25%'>{$character_list->get("name")}</td> <td width='10%'>{$character_list->get("level")}</td> <td>{$character_status}</td>
						</tr>					
					";						
				}				
			}

			$module .= "
			</table>";		
		}
		
		
		$module .= "
		<p id='line1'></p>
		";
	}
}


if($error)	
{
	$core->sendMessageBox("Erro!", $error);
}

$module .= '
<form action="?ref=character.view" method="post">
	<fieldset>
		
		<p>
			<label for="player_name">Nome</label><br />
			<input name="player_name" size="40" type="text" value="" />
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';
?>