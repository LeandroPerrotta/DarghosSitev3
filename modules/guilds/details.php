<?php
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
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		
		if($_SESSION['login'])
		{
			$this->loggedAcc = new Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
			
			$this->memberLevel = Guilds::GetAccountLevel($this->loggedAcc, $this->guild->GetId());
		}		
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{
		$this->guild = new Guilds();
		
		if(!$this->guild->LoadByName($_GET['name']))
		{
			$this->_message = Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']);
			return false;
		}
		
		return true;
	}
	
	function Draw()
	{
		global $module;
		
		
		if(ENABLE_GUILD_WARS)
		{
			if($this->guild->OnWar())
				$warStatus = "<span style='color: red;'>Esta guilda está em guerra com outra(s) guilda(s).</span>";
			else
				$warStatus = "Esta guilda não está em guerra.";
		}
		
		//guild info header
		$guildTable = new HTML_Table();
		$guildTable->AddField("Logotipo", null, null, 3);
		$guildTable->AddRow();
		
		$guildDesc = "
			<p><h3>{$this->guild->GetName()}</h3></p>
			<p>{$this->guild->GetMotd()}</p>";
		
		$guildTable->AddField("<img src='".GUILD_IMAGE_DIR."{$this->guild->GetImage()}' height='100' width='100' />");
		$guildTable->AddField($guildDesc, 90);
		$guildTable->AddField("<img src='".GUILD_IMAGE_DIR."{$this->guild->GetImage()}' height='100' width='100' />");
		$guildTable->AddRow();
		
		$guildInfoTable = new HTML_Table();
		$guildInfoTable->AddField("Informações da Guilda");
		$guildInfoTable->AddRow();
		
		if(ENABLE_GUILD_FORMATION)
		{
			$guildFormStatus = ($this->guild->GetStatus() == GUILD_STATUS_FORMED) ? "Esta guilda esta em <b>atividade</b>." : "Esta guilda esta em processo de formação e será disbandada se não possuir <b>".GUILDS_VICELEADERS_NEEDED." vice-lideres</b> até <b>".Core::formatDate($this->guild->GetFormationTime())."</b>.";
			$guildInfoTable->AddField($guildFormStatus);
			$guildInfoTable->AddRow();
		}
		
		$guildInfoTable->AddField("Esta guilda foi criada em <b>".Core::formatDate($this->guild->GetCreationDate())."</b>.");
		$guildInfoTable->AddRow();
		
		if(ENABLE_GUILD_POINTS)
		{
			$guildInfoTable->AddField("Pontos da guilda (força / total): <b>{$this->guild->GetBetterPoints()}/{$this->guild->GetPoints()}</b>");
			$guildInfoTable->AddRow();	
		}
		
		if(ENABLE_GUILD_WARS)
		{
			$guildInfoTable->AddField("Estado de Guerra: <b>{$warStatus}</b>");
			$guildInfoTable->AddRow();	
		}	
				
		$guildPage = "
		<div title='guild_page' class='viewable' style='margin: 0px; padding: 0px;'>
		
			{$guildTable->Draw()}
		
			{$guildInfoTable->Draw()}";				

		if($this->loggedAcc and $this->memberLevel == GUILD_RANK_LEADER)
		{			
			$guildPage .= "
			<p>
				<a class='buttonstd' href='?ref=guilds.edit&name={$this->guild->GetName()}'>Editar Descrições</a>
			    <a class='buttonstd' href='?ref=guilds.disband&name={$this->guild->GetName()}'>Desmanchar Guild</a>
			</p>				
			";	
		}					
		
		//loading guild members and preparing table to draw
		$membersTable = new HTML_Table();
		$membersTable->AddField("Rank", 25);
		$membersTable->AddField("Nome e apelido");
		$membersTable->AddField("Membro desde");
		$membersTable->AddRow();
		
		$lastRankName = "";
		$first = true;
		
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
				
				$membersTable->AddField($rankToWrite);
				$membersTable->AddField($nick);
				$membersTable->AddField(Core::formatDate($member->getGuildJoinIn()));
				$membersTable->AddRow();		
			}
			
			$lastRankName = $rank->GetName();
			$first = false;
		}			
		
		$guildPage .= "					
				
		<p><h3>Lista de Membros</h3></p>
		
		{$membersTable->Draw()}	
		";
		
		if($this->loggedAcc and $this->memberLevel > GUILD_RANK_NO_MEMBER)
		{
			$guildPage .= "<p>";
			
			if($this->memberLevel >= GUILD_RANK_VICE)
			$guildPage .= "
					<a class='buttonstd' href='?ref=guilds.members&name={$this->guild->GetName()}'>Editar Membros</a>				
			";
				
			if($this->memberLevel == GUILD_RANK_LEADER)
			{	
				$guildPage .= "
					<a class='buttonstd' href='?ref=guilds.ranks&name={$this->guild->GetName()}'>Editar Ranks</a> <a class='buttonstd' href='?ref=guilds.passleadership&name={$this->guild->GetName()}'>Passar Liderança</a>				
				";	
			}
			
			if($this->memberLevel >= GUILD_RANK_MEMBER_OPT_3)
			{	
				$guildPage .= "
					<a class='buttonstd' href='?ref=guilds.leave&name={$this->guild->GetName()}'>Sair da Guild</a>				
				";	
			}			
			
			$guildPage .= "</p>";
		}		
		

		$guildPage .= "	
		<p><h3>Personagens Convidados</h3></p>
		
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>Nome</th> <th>Data que foi Convidado</th>
			</tr>			
		";	
		
		if($this->guild->InvitesCount() != 0)
		{						
			foreach($this->guild->Invites as $invite)
			{
				list($character, $date) = $invite;
				
				$cancelInvite = ($this->memberLevel == GUILD_RANK_LEADER) ? " [<a href='?ref=guilds.invite&name={$character->getName()}&c=t'>Cancelar convite</a>]" : "";
				
				$guildPage .= "
					<tr>
						<td><a href='?ref=character.view&name={$character->getName()}'>{$character->getName()}</a> {$cancelInvite}</td> <td>".Core::formatDate($date)."</td>
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
		
		if($this->loggedAcc and $this->memberLevel >= GUILD_RANK_VICE)
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
		
		$warPage = "";
		
		if(ENABLE_GUILD_WARS)
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
			
			$warsList = $this->guild->SearchWarsByStatus(GUILD_WAR_STARTED);
			$warsWaitingList = $this->guild->SearchWarsByStatus(GUILD_WAR_WAITING);
			
			if(count($warsList) != 0)
			{			
				foreach($warsList as $guild_war)
				{
					$opponent = new Guilds();
					
					if($guild_war->GetGuildId() == $this->guild->GetId())
						$opponent->Load($guild_war->GetOpponentId());
					elseif($guild_war->GetOpponentId() == $this->guild->GetId())
						$opponent->Load($guild_war->GetGuildId());
					
					$endWar = round(($guild_war->GetEndDate() - time()) / (60 * 60 * 24));	
						
					$warPage .= "
					<tr>
						<td>{$opponent->GetName()}</td> <td>".Core::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$endWar} dias</td> <td>{$guild_war->GetFragLimit()} mortes</td> <td><a href='?ref=guilds.wardetail&value={$guild_war->GetId()}'>ver</a></td>
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
					$opponent = new Guilds();
					
					if($guild_war->GetGuildId() == $this->guild->GetId())
						$opponent->Load($guild_war->GetOpponentId());
					elseif($guild_war->GetOpponentId() == $this->guild->GetId())
						$opponent->Load($guild_war->GetGuildId());
					
					$endWar = round(($guild_war->GetEndDate() - time()) / (60 * 60 * 24));		
						
					$warPage .= "
					<tr>
						<td>{$opponent->GetName()}</td> <td>".Core::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$endWar} dias</td> <td>{$guild_war->GetFragLimit()} mortes</td> <td><a href='?ref=guilds.wardetail&value={$guild_war->GetId()}'>ver</a></td>
					</tr>";				
				}
			}
			
			$warPage .= "
			</table>
			";		
			
			
			if($this->loggedAcc and $this->memberLevel == GUILD_RANK_LEADER)
			{			
				$warPage .= "
				<p>
				    <a class='buttonstd' href='?ref=guilds.declarewar&name={$this->guild->GetName()}'>Declarar Guerra</a>				
				</p>		
				";				
			}		
			
			$declarationsList = $this->guild->SearchWarsByStatus(GUILD_WAR_DISABLED);
			
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
						$opponent = new Guilds();				
						
						if($guild_war->GetGuildId() == $this->guild->GetId())
						{
							if($guild_war->GetReply() == 0)
								$status = "Aguardando resposta...";
							if($guild_war->GetReply() == 1)	
								$status = "<a href='?ref=guilds.replywar&value={$guild_war->GetId()}'>Responder proposta de guerra.</a>";							
							
							$opponent->Load($guild_war->GetOpponentId());
							
							$byGuild .= "
							<tr>
								<td>{$opponent->GetName()}</td> <td>".Core::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$status}</td>
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
								<td>{$opponent->GetName()}</td> <td>".Core::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$status}</td>
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
		
		$module .= "
		<fieldset>
			<div class='autoaction' style='margin: 0px; margin-top: 20px; padding: 0px;'>
				<select>
					<option value='guild_page'>Pagina da Guilda</option>";
		
					if(ENABLE_GUILD_WARS)
					{
						$module .= "
						<option value='guild_wars'>Guerras da Guilda</option>";
					}
					
				$module .= "
				</select>
			</div>		
			
			{$guildPage}
			{$warPage}
		</fieldset>	
		";
	}
}

$view = new View();
?>