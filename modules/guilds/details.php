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
		
		if($this->guild->OnWar())
			$warStatus = "<span style='color: red;'>Esta guilda está em guerra com outra(s) guilda(s).</span>";
		else
			$warStatus = "Esta guilda não está em guerra.";
		
		$guildPage = "
		<div title='guild_page' class='viewable' style='margin: 0px; padding: 0px;'>
		
			<table cellspacing='0' cellpadding='0' id='table'>
				<tr>
					<th colspan='3' style='text-align: center;'>Logotipo</th>
				</tr>		
				<tr>
					<td><img src='".GUILD_IMAGE_DIR."{$this->guild->GetImage()}' height='100' width='100' /></td> <td width='90%'><div style='text-align: center; font: normal 30px Verdana, sans-serif;'>{$this->guild->GetName()}</div>{$this->guild->GetMotd()}</td> <td><img src='".GUILD_IMAGE_DIR."{$this->guild->GetImage()}'/></td>
				</tr>
			</table>
		
			<table cellspacing='0' cellpadding='0' id='table'>
				<tr>
					<th>Informações da Guilda</th>
				</tr>
				<tr>
					<td>".(($this->guild->GetStatus() == GUILD_STATUS_FORMED) ? "Esta guilda esta em <b>atividade</b>." : "Esta guilda esta em processo de formação e será disbandada se não possuir <b>".GUILDS_VICELEADERS_NEEDED." vice-lideres</b> até <b>".Core::formatDate($this->guild->GetFormationTime())."</b>.")."</td>
				</tr>
				
				<tr>
					<td>Esta guilda foi criada em <b>".Core::formatDate($this->guild->GetCreationDate())."</b>.</td>
				</tr>
				
				<tr>
					<td>Estado de Guerra: <b>{$warStatus}</b></td>
				</tr>
											
			</table>";				

		if($this->loggedAcc and $this->memberLevel == GUILD_RANK_LEADER)
		{			
			$guildPage .= "
			<p>
				<a class='buttonstd' href='?ref=guilds.edit&name={$this->guild->GetName()}'>Editar Descrições</a>
			    <a class='buttonstd' href='?ref=guilds.disband&name={$this->guild->GetName()}'>Desmanchar Guild</a>
			</p>				
			";	
		}			
		
		$guildPage .= "					
				
		<p><h3>Lista de Membros</h3></p>
		
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>Posição</th> <th>Nome e Titulo</th> <th>Membro Desde</th>
			</tr>		
		";			
				
		foreach($this->guild->Ranks as $rank)
		{			
			$showRank = true;
			
			foreach($rank->Members as $member)
			{
				$member->LoadGuild();
				
				$online = ($member->getOnline() == 1) ? "[<span class='online'>Online</span>]" : "";
				$rankToWrite = ($showRank) ? "<b>{$rank->GetName()}</b>" : "";
				$memberNick = ($member->getGuildNick()) ? "(<i>{$member->getGuildNick()}</i>)" : "";
				
				$showRank = false;
				
				$guildPage .= "
					<tr>
						<td width='25%'>{$rankToWrite}</td> 
						<td><a href='?ref=character.view&name={$member->getName()}'>{$member->getName()}</a> {$memberNick}</td> 
						<td>".Core::formatDate($member->getGuildJoinIn())."</td>
					</tr>	
				";				
			}
		}
		
		$guildPage .= "
		</table>";
		
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
				
				$guildPage .= "
					<tr>
						<td><a href='?ref=character.view&name='{$character->getName()}'>{$character->getName()}</a></td> <td>".Core::formatDate($date)."</td>
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
				<td colspan='4'><span style='font-weight: bold;'>Guerras que estão para se iniciar no proximo server save.</span></td>
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
					<td>{$opponent->GetName()}</td> <td>".Core::formatDate($guild_war->GetDeclarationDate())."</td> <td>{$endWar} dias</td> <td>{$guild_war->GetFragLimit()} mortes</td>
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
		
		$module .= "
		<fieldset>
			<div class='autoaction' style='margin: 0px; margin-top: 20px; padding: 0px;'>
				<select>
					<option value='guild_page'>Pagina da Guilda</option>
					<option value='guild_wars'>Guerras da Guilda</option>
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