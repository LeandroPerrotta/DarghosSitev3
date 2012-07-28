<?php
use \Core\Configs;
class View
{
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $guild, $memberLevel;	
	
	function View()
	{
		if(!$_GET['name'])
		{
			return;
		}
		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}
		
		if($_SESSION['login'])
		{
			$this->loggedAcc = new \Framework\Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
			
			$this->memberLevel = \Framework\Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId());
		}		
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{
		$this->guild = new \Framework\Guilds();
		
		if(!$this->guild->LoadByName($_GET['name']))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->GUILD_NOT_FOUND, $_GET['name']);
			return false;
		}
		
		return true;
	}
	
	function Draw()
	{
		global $module;
		
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_WARS))
		{
			if($this->guild->OnWar())
				$warStatus = "<span style='color: red;'>Esta guilda está em guerra com outra(s) guilda(s).</span>";
			else
				$warStatus = "Esta guilda não está em guerra.";
		}
		
		//guild info header
		$guildTable = new \Framework\HTML\Table();
		$guildTable->AddField("Logotipo", null, null, 3);
		$guildTable->AddRow();
		
		$guildDesc = "
			<p><h3>{$this->guild->GetName()}</h3></p>
			<p>{$this->guild->GetMotd()}</p>";
		
		$guildTable->AddField("<img src='".Configs::Get(Configs::eConf()->WEBSITE_FOLDER_GUILDS)."{$this->guild->GetImage()}' height='100' width='100' />");
		$guildTable->AddField($guildDesc, 90);
		$guildTable->AddField("<img src='".Configs::Get(Configs::eConf()->WEBSITE_FOLDER_GUILDS)."{$this->guild->GetImage()}' height='100' width='100' />");
		$guildTable->AddRow();
		
		$guildInfoTable = new \Framework\HTML\Table();
		$guildInfoTable->AddField("Informações da Guilda");
		$guildInfoTable->AddRow();
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_IN_FORMATION))
		{
			$guildFormStatus = ($this->guild->GetStatus() == \Framework\Guilds::STATUS_FORMED) ? "Esta guilda esta em <b>atividade</b>." : "Esta guilda esta em processo de formação e será disbandada se não possuir <b>".Configs::Get(Configs::eConf()->GUILDS_VICES_TO_FORMATION)." vice-lideres</b> até <b>".\Core\Main::formatDate($this->guild->GetFormationTime())."</b>.";
			$guildInfoTable->AddField($guildFormStatus);
			$guildInfoTable->AddRow();
		}
		
		if(Configs::Get(Configs::eConf()->ENABLE_MULTIWORLD))
		{
			$guildInfoTable->AddField("Esta guilda pertence ao mundo de <b>".\t_Worlds::GetString($this->guild->GetWorldId())."</b>.");
			$guildInfoTable->AddRow();
		}
		
		$guildInfoTable->AddField("Esta guilda foi criada em <b>".\Core\Main::formatDate($this->guild->GetCreationDate())."</b>.");
		$guildInfoTable->AddRow();
		
		$owner = new \Framework\Player();
		$owner->load($this->guild->GetOwnerId());
		
		$guildInfoTable->AddField("O personagem <b>{$owner->getName()}</b> é o atual dono desta guild.");
		$guildInfoTable->AddRow();		
		
		if($this->loggedAcc and $this->memberLevel > \Framework\Guilds::RANK_NO_MEMBER)
		{	
			$guildInfoTable->AddField("Saldo do banco: <b>{$this->guild->GetBalance()} moedas de ouro.</b>");
			$guildInfoTable->AddRow();			
		}	
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_POINTS))
		{
			$guildInfoTable->AddField("Pontos da guilda (força / total): <b>{$this->guild->GetBetterPoints()}/{$this->guild->GetPoints()}</b>");
			$guildInfoTable->AddRow();	
		}
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_WARS))
		{
			$guildInfoTable->AddField("Estado de Guerra: <b>{$warStatus}</b>");
			$guildInfoTable->AddRow();	
		}				
		
		//loading guild members and preparing table to draw
		$membersTable = new \Framework\HTML\Table();
		$membersTable->AddField("Rank", 25);
		$membersTable->AddField("Nome e apelido");
		$membersTable->AddField("Membro desde");
		$membersTable->AddRow();
		
		$lastRankName = "";
		$first = true;
		
		$totalLevel = 0;
		$membersCount = 0;
		
		foreach($this->guild->Ranks as $rank)
		{			
			if($first || $lastRankName != $rank->GetName())	
				$showRank = true;		
				
			$guildMembersCount += $rank->MemberCount();
			
			foreach($rank->Members as $member)
			{
				$member->LoadGuild();
				
				$online = ($member->getOnline() == 1) ? "[<span class='online'>Online</span>]" : "";
				$rankToWrite = ($showRank) ? "<b>{$rank->GetName()}</b>" : "";
				$memberNick = ($member->getGuildNick()) ? "(<i>{$member->getGuildNick()}</i>)" : "";
				
				$showRank = false;
				
				$nick = "<a href='?ref=character.view&name={$member->getName()}'>{$member->getName()}</a> {$memberNick} {$online}";
				
				$totalLevel += $member->getLevel();
				$membersCount++;
				
				$membersTable->AddField($rankToWrite);
				$membersTable->AddField($nick);
				$membersTable->AddField(\Core\Main::formatDate($member->getGuildJoinIn()));
				$membersTable->AddRow();		
			}
			
			$lastRankName = $rank->GetName();
			$first = false;
		}			
		
		$guildInfoTable->AddField("Esta guilda possui <b>{$membersCount} membros</b> no total.");
		$guildInfoTable->AddRow();		
		
		$guildInfoTable->AddField("O nível médio dos personagens desta guilda é <b>". ceil($totalLevel / $membersCount) ."</b>.");
		$guildInfoTable->AddRow();		
		
		$guildPage = "
		<div title='guild_page' class='viewable' style='margin: 0px; padding: 0px;'>
		
		{$guildTable->Draw()}
		
		{$guildInfoTable->Draw()}";
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT) && $this->loggedAcc && $this->memberLevel == \Framework\Guilds::RANK_LEADER)
		{
		$guildPage .= "
		<p>
			<a class='buttonstd' href='?ref=guilds.edit&name={$this->guild->GetName()}'>Editar Descrições</a>
			<a class='buttonstd' href='?ref=guilds.disband&name={$this->guild->GetName()}'>Desmanchar Guild</a>
			</p>
			";
			}	
		
		$guildPage .= "					
				
		<div style='margin-top: 32px;'><h3>Lista de Membros</h3></div>
		
		{$membersTable->Draw()}	
		";
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT) && $this->loggedAcc && $this->memberLevel > \Framework\Guilds::RANK_NO_MEMBER)
		{
			$guildPage .= "<p>";
			
			if($this->memberLevel >= \Framework\Guilds::RANK_VICE)
			$guildPage .= "
					<a class='buttonstd' href='?ref=guilds.members&name={$this->guild->GetName()}'>Editar Membros</a>				
			";
				
			if($this->memberLevel == \Framework\Guilds::RANK_LEADER)
			{	
				$guildPage .= "
					<a class='buttonstd' href='?ref=guilds.ranks&name={$this->guild->GetName()}'>Editar Ranks</a> <a class='buttonstd' href='?ref=guilds.passleadership&name={$this->guild->GetName()}'>Passar Liderança</a>				
				";	
			}
			
			if($this->memberLevel >= \Framework\Guilds::RANK_MEMBER_OPT_3)
			{	
				$guildPage .= "
					<a class='buttonstd' href='?ref=guilds.leave&name={$this->guild->GetName()}'>Sair da Guild</a>				
				";	
			}			
			
			$guildPage .= "</p>";
		}		
		

		$guildPage .= "	
		<div style='margin-top: 32px;'><h3>Personagens Convidados</h3></div>
		
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>Nome</th> <th>Data que foi Convidado</th>
			</tr>			
		";	
		
		if($this->guild->InvitesCount() != 0)
		{						
			foreach($this->guild->Invites as $invite)
			{
				list($player, $date) = $invite;
				
				$cancelInvite = ($this->memberLevel == \Framework\Guilds::RANK_LEADER) ? " [<a href='?ref=guilds.invite&name={$player->getName()}&c=t'>Cancelar convite</a>]" : "";
				
				$guildPage .= "
					<tr>
						<td><a href='?ref=character.view&name={$player->getName()}'>{$player->getName()}</a> {$cancelInvite}</td> <td>".\Core\Main::formatDate($date)."</td>
					</tr>	
				";	
			}	
		}
		else
		{
			$guildPage .= "
				<tr>
					<td colspan='2'>Nenhum jogador foi convidado por esta guilda.</td>
				</tr>	
			";				
		}
		
		$guildPage .= "
		</table>
		";			
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_MANAGEMENT) && $this->loggedAcc and $this->memberLevel >= \Framework\Guilds::RANK_VICE)
		{
			$guildPage .= "
				<p>
					<a class='buttonstd' href='?ref=guilds.invite&name={$this->guild->GetName()}'>Convidar Jogador</a>
				</p>				
			";
		}

		
		/*
		 * GUILD PAGE
		 */
		
		
		$guildPage .= "
		<br>		
		
		</div>
		";
		
		/*
		 * WARS
		 */
		$warPage = "";
		
		if(Configs::Get(Configs::eConf()->ENABLE_GUILD_WARS))
		{
			$warPage = "
			<div title='guild_wars' style='margin: 0px; padding: 0px;'>";
			
			$this->guild->LoadWars();
			
			$warPage .= "
			<p><h3>Guerras em andamento</h3></p>
			
			<table cellspacing='0' cellpadding='0' id='table'>
				<tr>
					<th>Oponente</th> <th>Iniciada em</th> <th>Termina em</th> <th>Ou após</th> <th></th>
				</tr>						
			";		
			
			$warsList = $this->guild->SearchWarsByStatus(\Framework\Guilds::WAR_STARTED);
			$warsWaitingList = $this->guild->SearchWarsByStatus(GUILD_WAR_WAITING);
			
			if(count($warsList) != 0)
			{			
				foreach($warsList as $guild_war)
				{
					$opponent = new \Framework\Guilds();
					
					if($guild_war->GetGuildId() == $this->guild->GetId())
						$opponent->Load($guild_war->GetOpponentId());
					elseif($guild_war->GetOpponentId() == $this->guild->GetId())
						$opponent->Load($guild_war->GetGuildId());
					
					$endWar = round(($guild_war->GetEndDate() - time()) / (60 * 60 * 24));	
						
					$warPage .= "
					<tr>
						<td>{$opponent->GetName()}</td> <td>".\Core\Main::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$endWar} dias</td> <td>{$guild_war->GetFragLimit()} mortes</td> <td><a href='?ref=guilds.wardetail&value={$guild_war->GetId()}'>ver</a></td>
					</tr>";
				}
			}			
			else
			{
				$warPage .= "
				<tr>
					<td colspan='5'>Esta guilda não está em guerra com nenhuma outra guilda.</td>
				</tr>";			
			}
			
			if(count($warsWaitingList) != 0)
			{
				$warPage .= "
				<tr>
					<td colspan='5'><span style='font-weight: bold;'>Guerras que estão para se iniciar no proximo server save.</span></td>
				</tr>";			
	
				foreach($warsWaitingList as $guild_war)
				{
					$opponent = new \Framework\Guilds();
					
					if($guild_war->GetGuildId() == $this->guild->GetId())
						$opponent->Load($guild_war->GetOpponentId());
					elseif($guild_war->GetOpponentId() == $this->guild->GetId())
						$opponent->Load($guild_war->GetGuildId());
					
					$endWar = round(($guild_war->GetEndDate() - time()) / (60 * 60 * 24));		
						
					$warPage .= "
					<tr>
						<td>{$opponent->GetName()}</td> <td>".\Core\Main::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$endWar} dias</td> <td>{$guild_war->GetFragLimit()} mortes</td> <td><a href='?ref=guilds.wardetail&value={$guild_war->GetId()}'>ver</a></td>
					</tr>";				
				}
			}
			
			$warPage .= "
			</table>
			";		
			
			
			if($this->loggedAcc and $this->memberLevel == \Framework\Guilds::RANK_LEADER)
			{			
				$warPage .= "
				<p>
				    <a class='buttonstd' href='?ref=guilds.declarewar&name={$this->guild->GetName()}'>Declarar Guerra</a>				
				</p>		
				";				
			}		
			
			$declarationsList = $this->guild->SearchWarsByStatus(\Framework\Guilds::WAR_DISABLED);
			
			$warPage .= "
			<p><h3>Guerras declaradas</h3></p>
			
			<table cellspacing='0' cellpadding='0' id='table'>
				<tr>
					<th>Oponente</th> <th>Declarada em</th> <th>Status</th>
				</tr>						
			";	
			
			if(count($declarationsList) != 0)
			{	
				$byGuild = null;
				$againstGuild = null;
				
				foreach($declarationsList as $guild_war)
				{			
					if($guild_war->GetReply() != -1)
					{					
						$opponent = new \Framework\Guilds();				
						
						if($guild_war->GetGuildId() == $this->guild->GetId())
						{
							if($guild_war->GetReply() == 0)
								$status = "Aguardando resposta...";
							if($guild_war->GetReply() == 1)	
								$status = "<a href='?ref=guilds.replywar&value={$guild_war->GetId()}'>Responder proposta de guerra.</a>";							
							
							$opponent->Load($guild_war->GetOpponentId());
							
							$byGuild .= "
							<tr>
								<td>{$opponent->GetName()}</td> <td>".\Core\Main::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$status}</td>
							</tr>";	
						}
						elseif($guild_war->GetOpponentId() == $this->guild->GetId())
						{
							if($guild_war->GetReply() == 1)
								$status = "Aguardando resposta...";
							if($guild_war->GetReply() == 0)	
								$status = "<a href='?ref=guilds.replywar&value={$guild_war->GetId()}'>Responder proposta de guerra.</a>";							
							
							$opponent->Load($guild_war->GetGuildId());
							
							$againstGuild .= "
							<tr>
								<td>{$opponent->GetName()}</td> <td>".\Core\Main::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$status}</td>
							</tr>";							
						}
					}
				}		
			}
	
			$warPage .= "
			<tr>
				<td colspan='3'><span style='font-weight: bold;'>Guerras declaradas por esta guilda:</span></td>
			</tr>";			
			
			if($byGuild)
			{
				$warPage .= $byGuild;
			}
			else
			{
				$warPage .= "
				<tr>
					<td colspan='3'>Esta guilda não declarou nenhuma guerra contra outra guilda.</td>
				</tr>";					
			}
			
			$warPage .= "
			<tr>
				<td colspan='3'><span style='font-weight: bold;'>Guerras declaradas contra esta guilda:</span></td>
			</tr>";				
			
			if($againstGuild)
			{
				$warPage .= $againstGuild;
			}
			else
			{
				$warPage .= "
				<tr>
					<td colspan='3'>Nenhuma guerra foi declarada contra esta guilda.</td>
				</tr>";					
			}		
			
			$warPage .= "
			</table>
			";			
			
			$warPage .= "
			</div>
			";
		}
		
		/*
		 * FIGHTS
		*/
		
		//guild info header
		$fightTodayTable = new \Framework\HTML\Table();
		$fightTodayTable->AddField("Ultimas 24h", null, null, 3);
		$fightTodayTable->AddRow();
		
		$fightWeekTable = new \Framework\HTML\Table();
		$fightWeekTable->AddField("Ultimos 7 dias", null, null, 3);
		$fightWeekTable->AddRow();		
		
		$fightMontlyTable = new \Framework\HTML\Table();
		$fightMontlyTable->AddField("Ultimos 30 dias", null, null, 3);
		$fightMontlyTable->AddRow();	
			
		$fightTable = new \Framework\HTML\Table();
		$fightTable->AddField("Geral", null, null, 3);
		$fightTable->AddRow();		
		
		$fightData = array(
				
				array("timestamp" => time() - (60 * 60 * 24), "table" => &$fightTodayTable)
				,array("timestamp" => time() - (60 * 60 * 24 * 7), "table" => &$fightWeekTable)
				,array("timestamp" => time() - (60 * 60 * 24 * 30), "table" => &$fightMontlyTable)
				,array("timestamp" => 0, "table" => &$fightTable)
		);
		
				
		$guilds = \Framework\Guilds::ActivedGuildsList($this->guild->GetWorldId());
		
		foreach($guilds as $g)
		{
			$g instanceof \Framework\Guilds;
			
			if($g->GetId() == $this->guild->GetId())
				continue;
			
			foreach($fightData as $f)
			{
				$frags = $this->guild->KillsCountAgainst($g->GetId(), $f["timestamp"]);
				$enemyFrags = $g->KillsCountAgainst($this->guild->GetId(), $f["timestamp"]);
				
				if($frags == 0 && $enemyFrags == 0)
				{
					continue;
				}
				
				$winning = $frags > $enemyFrags ? $this->guild : $g;			
				$loosing = $winning == $this->guild ? $g : $this->guild;
				
				$winningPoints = $frags;
				$loosingPoints = $enemyFrags;
				
				if($enemyFrags > $frags){
					$winningPoints = $enemyFrags;
					$loosingPoints = $frags;
				}
				
				$guild_result = "
				<div>
				<div style='display: inline-block; width: 157px; text-align: right;'>
				<a style='line-height: 22px;' href='?ref=guilds.details&name={$winning->GetName()}'>{$winning->GetName()}</a>
				</div>
				
				<div style='display: inline-block;'>
				<span style='font-weight: bold; font-size: 18px; margin-left: 5px; margin-right: 5px;'>vs</span>
				</div>
				
				<div style='display: inline-block;  width: 157px;'>
				<a style='line-height: 22px;' href='?ref=guilds.details&name={$loosing->GetName()}'>{$loosing->GetName()}</a>
				</div>
				</div>
				<div style='text-align: center;'>
				<div style='float: left; width: 165px; text-align: right;'>
				<h3 style='font-size: 40px;'>{$winningPoints}</h3>
				</div>
				
				<div style='display: inline-block;'>
				<span style='display: table-cell; font-weight: bold; height: 50px; width: 15px; font-size: 14px; vertical-align: middle; text-align: center;'>X</span>
				</div>
				
				<div style='float: right; width: 165px;  text-align: left;'>
				<h3 style='font-size: 40px;'>{$loosingPoints}</h3>
				</div>
				</div>
				";		
	
				$f["table"]->AddField("<img src='".Configs::Get(Configs::eConf()->WEBSITE_FOLDER_GUILDS)."{$winning->GetImage()}' height='100' width='100' />");
				$f["table"]->AddField($guild_result, 90);
				$f["table"]->AddField("<img src='".Configs::Get(Configs::eConf()->WEBSITE_FOLDER_GUILDS)."{$loosing->GetImage()}' height='100' width='100' />");
				$f["table"]->AddRow();	
			}		
		}	
		
		$fightPage = "
		<div title='guild_fights' style='margin: 0px; padding: 0px;'>";
			
		$fightPage .= "
		<p>Confrontos ocorrem quando um personagem de uma guilda é morto por um ou mais jogadores de outra guilda. Os confrontos são rastreados automaticamente, sem necessitar por exemplo que as guilds em questão estejam em guerra.</p>
		<p><h3>Confrontos</h3></p>
			
		{$fightTodayTable->Draw()}
		{$fightWeekTable->Draw()}
		{$fightMontlyTable->Draw()}
		{$fightTable->Draw()}
		
		</div>
		";		
		
		$module .= "
		<fieldset>
			<div id='horizontalSelector'>
				<span name='left_corner'></span>
				<ul>
					<li name='guild_page' checked='checked'><span>Profile</span></li>
					<li name='guild_fights'><span>Confrontos</span></li>";
				
					if(Configs::Get(Configs::eConf()->ENABLE_GUILD_WARS))
					{
						$module .= "
						<li name='guild_wars'><span>Guerras</span></li>";
					}				
				
					$module .= "
				</ul>
				<span name='right_corner'></span>
			</div>			
			
			{$guildPage}
			{$warPage}
			{$fightPage}
		</fieldset>	
		";
	}
}

$view = new View();
?>