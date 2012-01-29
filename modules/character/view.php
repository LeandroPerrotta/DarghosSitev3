<?
use \Core\Configs;
if($_POST["player_name"] || $_GET['name'])
{		
	$name = ($_POST) ? $_POST["player_name"] : $_GET['name'];

	$player = new \Framework\Player();
	
	if(!$player->loadByName($name))
	{	
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_WRONG);
	}
	else
	{			
		$account = new \Framework\Account();
		$account->load($player->get("account_id"));
		
		$logged_acc = \Framework\Account::loadLogged();
		
		$deaths = new \Framework\Deaths();
		
		//$bans = $account->getBans();
		$bans = new \Framework\Bans();
		
		$houseid = $player->getHouse();
		$lastlogin = ($player->getLastLogin()) ? \Core\Main::formatDate($player->getLastLogin()) : "Nunca entrou.";
		$creation = \Core\Main::formatDate($player->getCreation());
		
		$premium = ($account->getPremDays() != 0) ? "<font style='color: green; font-weight: bold;'>Conta Premium" : "Conta Gratuita";	
		$realname = ($account->get("real_name") != "") ? $account->get("real_name") : "não configurado";
		$location = ($account->get("location") != "") ? $account->get("location") : "não configurado";
		$url = ($account->get("url") != "") ? $account->get("url") : "não configurado";
		
		$deathlist = $deaths->getDeathListOfPlayer($player->getId());
		$list = $account->getCharacterList();
		$oldnames = $player->loadOldNames();
	
		$module .= "
		<br>
		<div id='horizontalSelector'>
			<span name='left_corner'></span>
			<ul>
				<li name='profile' checked='true'><span>Profile</span></li>
				<li name='statistics'><span>Estatisticas</span></li>
				<li name='achievements'><span>Façanhas</span></li>
				<li name='accounts'><span>Conta</span></li>
			</ul>
			<span name='right_corner'></span>
		</div>
		
		<div title='profile' class='viewable' style='margin: 0px; padding: 0px;'>
		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='2'>Personagem</th>
			</tr>";		

			if($player->deletionStatus())
			{		
				$module .= "
				<tr>
					<td colspan='2'><font style='color: red; font-weight: bold;'>Este personagem esta agendado para ser deletado no dia ". \Core\Main::formatDate($player->deletionStatus()) . ".</font></td>
				</tr>";				
			}
		
			$module .= "
			<tr>
				<td width='25%'><b>Nome:</b></td> <td>{$player->getName()}</td>
			</tr>";
			
			if($oldnames)
			{		
				$i = 0;
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
				<td><b>Level:</b></td> <td>{$player->getLevel()}</td>
			</tr>	
			
			";
			
			if(Configs::Get(Configs::eConf()->ENABLE_PVP_SWITCH))
			{
				$pvp_str = $player->isPvpEnabled() ? "<span class='pvpEnabled'>Agressivo</span>" : "<span class='pvpDisabled'>Pacifico</span>";
				
				$module .= "
				<tr>
					<td><b>PvP:</b></td> <td>{$pvp_str}</td>
				</tr>";			
			}
			
			$_vocation = new t_Vocation($player->getVocation());
			$_town_str = t_Towns::GetString($player->getTownId());
			$_genre_str = t_Genre::GetString($player->getSex());
			
			$module .= "
			<tr>
				<td><b>Magic Level:</b></td> <td>{$player->getMagicLevel()}</td>
			</tr>			

			<tr>
				<td><b>Battleground Rating:</b></td> <td>{$player->getBattlegroundRating()}</td>
			</tr>			
			
			<tr>
				<td><b>Sexo:</b></td> <td>{$_genre_str}</td>
			</tr>	

			<tr>
				<td><b>Vocação:</b></td> <td>{$_vocation->GetByName()}</td>
			</tr>	

			<tr>
				<td><b>Residencia:</b></td> <td>{$_town_str}</td>
			</tr>";	

			if($houseid)
			{
				$houses = new \Framework\Houses();
				$houses->load($houseid);				
				
				$_house_town_str = t_Towns::GetString($houses->get("town"));
				
				if($houses->get("warnings") == 0)
				{
					$housemsg = "{$houses->get("name")} ({$_house_town_str}) com pagamento no dia  ".\Core\Main::formatDate($houses->get("paid")).".";
				}
				else
				{
					$housemsg = "{$houses->get("name")} ({$_house_town_str}) está com {$houses->get("warnings")} pagamento(s) atrazado(s).";
				}
				
				$module .= "
				<tr>
					<td><b>Casa</b></td><td>{$housemsg}</td>
				</tr>";						
			}
			
			if($player->LoadGuild())
			{
				$module .= "
				<tr>
					<td><b>Membro da Guild</b></td> <td>{$player->GetGuildRank()} da <a href='?ref=guilds.details&name={$player->GetGuildName()}'>{$player->GetGuildName()}</a></td>
				</tr>";					
			}
			
			if($player->get("comment"))
			{
				$module .= "
				<tr>
					<td><b>Comentario</b></td> <td>".nl2br(stripslashes($player->getComment()))."</td>
				</tr>";					
			}
			
			$module .= "			
			<tr>
				<td><b>Criado em:</b></td> <td>{$creation}</td>
			</tr>	

			<tr>
				<td><b>Último Login:</b></td> <td>{$lastlogin}</td>
			</tr>			
			
		</table>
		";

		if($logged_acc && $logged_acc->getGroup() == t_Group::Administrator)
		{
			$contribute = new \Framework\Contribute();
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
			
			$player->loadSkills();
			
			$module .= "
			<table cellspacing='0' cellpadding='0'>
				<tr>
					<th colspan='2'>Informações Avançadas</th>
				</tr>
				<tr>
					<td width='25%'><b>Horas de Stamina</b></td><td>".(($player->getStamina() > 0) ? round($player->getStamina() / 1000 / 60 / 60) : "Nenhuma")."</td>
				</tr>	
				<tr>
					<td width='25%'><b>Numero da Conta</b></td><td>{$account->getId()}</td>
				</tr>	
				<tr>
					<td><b>Nome da Conta</b></td><td>{$account->getName()}</td>
				</tr>
				<tr>
					<td><b>Email da Conta</b></td><td>{$account->getEmail()}</td>
				</tr>				
				<tr>
					<td><b>Dias de Premium</b></td><td>{$account->getPremDays()}</td>
				</tr>			
				<tr>
					<td><b>Alguma vez Premium?</b></td><td>{$alreadyIsPremiumHTML}</td>
				</tr>					
				<tr>
					<td><b>Posição</b></td><td>x:{$player->getPosX()} y:{$player->getPosY()} z:{$player->getPosZ()}</td>
				</tr>
				<tr>
					<td><b>Player ID</b></td><td>{$player->getId()}</td>
				</tr>	
				<tr>
					<td><b>Skill sword</b></td><td>{$player->getSkill(t_Skills::Sword)}</td>
				</tr>
				<tr>
					<td><b>Skill axe</b></td><td>{$player->getSkill(t_Skills::Axe)}</td>
				</tr>										
				<tr>
					<td><b>Skill club</b></td><td>{$player->getSkill(t_Skills::Club)}</td>
				</tr>										
				<tr>
					<td><b>Skill shield</b></td><td>{$player->getSkill(t_Skills::Shielding)}</td>
				</tr>										
				<tr>
					<td><b>Skill distance</b></td><td>{$player->getSkill(t_Skills::Distance)}</td>
				</tr>										
			</table>														
				";			
		}
		
		$module .= "
		</div>
		<div title='accounts' style='display: none; margin: 0px; padding: 0px;'>
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
						$banstring .= "Banido por: <b>".\Core\Tools::getBanReason($ban['reason'])."</b><br>
								   	   Duração: Até ".\Core\Main::formatDate($ban['expires']).".";
					}
					elseif($ban['type'] == 5)	
					{
						$banstring .= "Deletado por: <b>".\Core\Tools::getBanReason($ban['reason'])."</b><br>
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
		</table>
		</div>";

		$module .= "
		<div title='statistics' style='display: none; margin: 0px; padding: 0px;'>
		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='2'>Battlegrounds</th>
			</tr>	
			<tr>
				<td>Venceu {$player->getBattlegroundsWon()} partidas.</td>
			</tr>	
			<tr>
				<td>Empatou {$player->getBattlegroundsDraw()} partidas.</td>
			</tr>	
			<tr>
				<td>Perdeu {$player->getBattlegroundsLose()} partidas.</td>
			</tr>
		</table>		
		
		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='2'>Mortes Causadas</th>
			</tr>	
			<tr>
				<td>Matou {$player->getTotalKills()} jogadores.</td>
			</tr>	
			<tr>
				<td>Participou da morte de {$player->getTotalAssists()} jogadores.</td>
			</tr>				
			<tr>
				<td>Matou {$player->getTotalBgKills()} jogadores em battlegrounds.</td>
			</tr>						
			<tr>
				<td>Participou da morte de {$player->getTotalBgAssists()} jogadores em battlegrounds.</td>
			</tr>
		</table>

		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='2'>Mortes Sofridas</th>
			</tr>	
			<tr>
				<td>Foi morto {$player->getTotalDeaths()} vezes (total).</td>
			</tr>	
			<tr>
				<td>Foi morto {$player->getTotalDeathsPlayers()} vezes com participação de jogadores.</td>
			</tr>				
			<tr>
				<td>Foi morto {$player->getTotalDeathsEnv()} vezes com participação de criaturas.</td>
			</tr>						
			<tr>
				<td>Foi morto {$player->getTotalBgDeaths()} vezes em battlegrounds.</td>
			</tr>						
		</table>		
		";	
			
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
				
				$date = \Core\Main::formatDate($death_values['date']);
				
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
							$_killer = new \Framework\Player();	
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
		
		if($logged_acc && ($account->getId() == $logged_acc->getId() || $logged_acc->getGroup() >= t_Group::GameMaster))
		{
			$kills = $deaths->getKillsOfPlayer($player->getId());

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
					$killed = new \Framework\Player();
					$killed->load($kill["killed"]);
					
					$date = \Core\Main::formatDate($kill['date']);
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
		
		$module .= "
		</div>
		
		<div title='achievements' style='display: none; margin: 0px; padding: 0px;'>
		<table cellspacing='0' cellpadding='0'>
			<tr>
				<th colspan='2'>Battlegrounds</th>
			</tr>	
			<tr>
				<td><span class='".($player->hasAchievBattlegroundRating1500() ? "hasAchiev" : "notHasAchiev")."'>Conquistou 1.500 pontos de classificação (rating).</span></td>
			</tr>	
			<tr>
				<td><span class='".($player->hasAchievBattlegroundRating2000() ? "hasAchiev" : "notHasAchiev")."'>Conquistou 2.000 pontos de classificação (rating).</span></td>
			</tr>
			<tr>
				<td><span class='".($player->hasAchievBattlegroundInsaneKiller() ? "hasAchiev" : "notHasAchiev")."'>Matador Insano! Derrotou 25 oponentes sem ser derrotado nenhuma vez em uma Battleground.</span></td>
			</tr>
			<tr>
				<td><span class='".($player->hasAchievBattlegroundPerfect() ? "hasAchiev" : "notHasAchiev")."'>Partida Perfeita! Esmagou os oponentes em uma battleground com o magnifico resultado final de 50x0.</span></td>
			</tr>								
		</table>		
		</div>
		";		
		
		if($player->get("hide") == 0 || $logged_acc->getGroup() >= t_Group::GameMaster)
		{
			$module .= "
			<div title='accounts' style='display: none; margin: 0px; padding: 0px;'>
			<table cellspacing='0' cellpadding='0'>
				<tr>
					<th colspan='3'>Outros Personagens</th>
				</tr>					
			";			
			
			foreach($list as $player_name)
			{
				$character_list = new \Framework\Player();
				$character_list->loadByName($player_name);
				
				if($character_list->get("hide") == 0 || $logged_acc->getGroup() >= t_Group::GameMaster)
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
			</table>
			</div>";		
		}
		
		
		$module .= "
		<p id='line1'></p>
		";
	}
}


if($error)	
{
	\Core\Main::sendMessageBox("Erro!", $error);
}

$module .= '
<form action="?ref=character.view" method="post">
	<fieldset>
		
		<p>
			<label for="player_name">Nome</label><br />
			<input id="player_name" name="player_name" size="40" type="text" value="" autocomplete="off" onkeyup="requestSearchBox(this.value);" />

			<div id="player_suggestions">
				<div id="player_suggestions_list">
				</div>
			</div>
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';
?>

