<?
if($_POST or $_GET)
{		
	$name = ($_POST) ? $_POST["player_name"] : $_GET['name'];

	$character = new Character();
	
	if(!$character->loadByName($name))
	{	
		$error = Lang::Message(LMSG_CHARACTER_WRONG);
	}
	else
	{			
		$account = new Account();
		$account->load($character->get("account_id"));
		
		$deaths = new Deaths();
		
		//$bans = $account->getBans();
		$bans = new Bans();
		
		$houseid = $character->getHouse();
		$lastlogin = ($character->getLastLogin()) ? Core::formatDate($character->getLastLogin()) : "Nunca entrou.";
		
		$premium = ($account->getPremDays() != 0) ? "<font style='color: green; font-weight: bold;'>Conta Premium" : "Conta Gratuita";	
		$realname = ($account->get("real_name") != "") ? $account->get("real_name") : "não configurado";
		$location = ($account->get("location") != "") ? $account->get("location") : "não configurado";
		$url = ($account->get("url") != "") ? $account->get("url") : "não configurado";
		
		$deathlist = $deaths->getDeathListOfPlayer($character->getId());
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
					<td colspan='2'><font style='color: red; font-weight: bold;'>Este personagem esta agendado para ser deletado no dia {Core::formatDate($character->deletionStatus())}.</font></td>
				</tr>";				
			}
		
			$module .= "
			<tr>
				<td width='25%'><b>Nome:</b></td> <td>{$character->getName()}</td>
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
				<td><b>Level:</b></td> <td>{$character->getLevel()}</td>
			</tr>	
			
			<tr>
				<td><b>Magic Level:</b></td> <td>{$character->getMagicLevel()}</td>
			</tr>			

			<tr>
				<td><b>Sexo:</b></td> <td>{$_sexid[$character->getSex()]}</td>
			</tr>	

			<tr>
				<td><b>Vocação:</b></td> <td>{$_vocationid[$character->getVocation()]}</td>
			</tr>	

			<tr>
				<td><b>Residencia:</b></td> <td>{$_townid[$character->getTownId()]["name"]}</td>
			</tr>";	

			if($houseid)
			{
				$houses = new Houses();
				$houses->load($houseid);				
				
				if($houses->get("warnings") == 0)
				{
					$housemsg = "{$houses->get("name")} ({$_townid[$houses->get("townid")]["name"]}) com pagamento no dia  ".Core::formatDate($houses->get("paid")).".";
				}
				else
				{
					$housemsg = "{$houses->get("name")} ({$_townid[$houses->get("townid")]["name"]}) está com {$houses->get("warnings")} pagamento(s) atrazado(s).";
				}
				
				$module .= "
				<tr>
					<td><b>Casa</b></td><td>{$housemsg}</td>
				</tr>";						
			}
			
			if($character->LoadGuild())
			{
				$module .= "
				<tr>
					<td><b>Membro da Guild</b></td> <td>{$character->GetGuildRank()} da <a href='?ref=guilds.details&name={$character->GetGuildName()}'>{$character->GetGuildName()}</a></td>
				</tr>";					
			}
			
			if($character->get("comment"))
			{
				$module .= "
				<tr>
					<td><b>Comentario</b></td> <td>".nl2br(stripslashes($character->getDescription()))."</td>
				</tr>";					
			}
			
			$module .= "
			<tr>
				<td><b>Último Login:</b></td> <td>{$lastlogin}</td>
			</tr>	
			
		</table>";

		$_gmAcc = new Account();
		if($_SESSION['login'] and $_gmAcc->load($_SESSION['login'][0]) and $_gmAcc->getGroup() >= 6)
		{
			include_once("classes/contribute.php");
			$contribute = new Contribute();
			$oders = $contribute->getOrdersListByAccount($account->getId());
			
			$alreadyIsPremium = false;
			$numberOfPremiums = 0;
			
			if(is_array($oders))
			{	
				foreach($oders as $orderId)
				{
					$contribute->load($orderId, "id, name, target, type, period, cost, generated_in, status");
					
					if($contribute->get("status") == 1 OR $contribute->get("status") == 2)
					{
						$numberOfPremiums++;
						$alreadyIsPremium = true;
					}
				}
			}	

			$alreadyIsPremiumHTML = ($alreadyIsPremium) ? "Sim" : "Não";
			
			if($alreadyIsPremium)
			{
				$alreadyIsPremiumHTML .= " ({$numberOfPremiums}x)";
			}
			
			$module .= "
			<table cellspacing='0' cellpadding='0'>
				<tr>
					<th colspan='2'>Informações Avançadas</th>
				</tr>
				<tr>
					<td width='25%'><b>Numero da Conta</b></td><td>{$account->getId()}</td>
				</tr>	
				<tr>
					<td><b>Nome da Conta</b></td><td>{$account->getName()}</td>
				</tr>							
				<tr>
					<td><b>Dias de Premium</b></td><td>{$account->getPremDays()}</td>
				</tr>			
				<tr>
					<td><b>Alguma vez Premium?</b></td><td>{$alreadyIsPremiumHTML}</td>
				</tr>					
				<tr>
					<td><b>Posição</b></td><td>x:{$character->getPosX()} y:{$character->getPosY()} z:{$character->getPosZ()}</td>
				</tr>
			</table>														
				";			
		}
		
		$module .= "
		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='2'>Informaçães da Conta</th>
			</tr>";
			
			if($bans->isBannished($account->getId()))
			{
				$ban = $bans->getBannishment($account->getId());
						
				if($ban['type'] == 3 OR $ban['type'] == 5)
				{
					$banstring .= "<font color='red'>";
					
					if($ban['type'] == 3)
					{
						$banstring .= "Banido por: <b>".Tools::getBanReason($ban['reason'])."</b><br>
								   	   Duração: Até ".Core::formatDate($ban['expires']).".";
					}
					elseif($ban['type'] == 5)	
					{
						$banstring .= "Deletado por: <b>".Tools::getBanReason($ban['reason'])."</b><br>
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
			
			foreach($deathlist as $death_id)
			{
				$death_values = $deaths->load($death_id);
				
				$date = Core::formatDate($death_values['date']);
				
				$death = "Morto no Nivel {$death_values['level']} por ";
				
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
							$_killer = new Character();	
							$_killer->load($killer['killer']);	

							$death .= "<a href='?ref=character.view&name={$_killer->getName()}'>{$_killer->getName()}</a>";
						}
					}
				}

				$death .= ".";
				
				$module .= "
					<tr>
						<td width='25%'>{$date}</td> <td>{$death}</td>
					</tr>					
				";	
			}
			
			$module .= "
			</table>";		
		}
		
		$_gmAcc = new Account();
		
		if(($_SESSION['login']) and (($account->getId() == $_SESSION['login'][0]) or ($_gmAcc->load($_SESSION['login'][0]) and $_gmAcc->getGroup() >= 5)))
		{
			$kills = $deaths->getKillsOfPlayer($character->getId());

			if(is_array($kills))
			{
				$module .= "
				
				<table cellspacing='0' cellpadding='0'>
					<tr>
						<th colspan='3'>Assassinatos Recentes</th>
					</tr>					
				";				
				
				foreach($kills as $kill)
				{
					$killed = new Character();
					$killed->load($kill["killed"]);
					
					$date = Core::formatDate($kill['date']);
					$isInjust = ($kill["injust"] == 1) ? "<font color='#ec0404'><b>injustificada</b></font>" : "<font color='#00ff00'><b>justificada</b></font>";
					
					$module .= "
						<tr>
							<td width='25%'>{$date}</td> <td width='50%'>Matou <a href='?ref=character.view&name={$killed->getName()}'>{$killed->getName()}</a> no level {$kill["level"]}.</td> <td>{$isInjust}</td>
						</tr>					
					";					
				}
				
				$module .= "
				</table>";						
			}
			else
			{
				$module .= "
				
				<table cellspacing='0' cellpadding='0'>
					<tr>
						<th colspan='3'>Assassinatos Recentes</th>
					</tr>

					<tr>
						<td>Você não matou nenhum personagem recentemente.</td>
					</tr>					
					
				</table>	
				";					
			}
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
				$character_list = new Character();
				$character_list->loadByName($player_name);
				
				if($character_list->get("hide") == 0)
				{
					$character_status = ($character_list->getOnline() == 1) ? "<font style='color: green; font-weight: bold;'>On-line</font>" : "<font style='color: red; font-weight: bold;'>Off-line</font>";
					
					$module .= "
						<tr>
							<td width='25%'>{$character_list->getName()}</td> <td width='10%'>{$character_list->getLevel()}</td> <td>{$character_status}</td>
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
	Core::sendMessageBox("Erro!", $error);
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
