<?php
if($_GET['name'])
{
	$guild = $core->loadClass("guilds");
	
	if(!$guild->loadByName($_GET['name']))
	{		
		$core->sendMessageBox("Erro!", "Esta guilda não existe em nosso banco de dados.");
	}
	else
	{
		$character_list = array();
		
		if($_SESSION['login'])
		{
			$account = $core->loadClass("Account");
			$account->load($_SESSION['login'][0], "password");
			
			$character_list = $account->getCharacterList(true);
			$character_listByName = $account->getCharacterList();
			
			$accountLevel = $account->getGuildLevel($guild->get("name"));
		}
		
		$guild->loadRanks();
		$guild->loadMembersList();
		$guild->loadInvitesList();
		
		$members = $guild->getMembersList();
		$invites = $guild->getInvites();
		$ranks = $guild->getRanks();
		
		if($guild->isOnWar())
		{
			$str = "<td>Está guilda <b>esta em modo de guerra</b> desde <b>".$core->formatDate($guild->getWarStart())."</b>.</td>";
		}
		else 
		{
			$str = "<td>Está guilda <b>não esta em modo de guerra</b>.";			
		}
		
		$module .=	"
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th colspan='3' style='text-align: center;'>Logotipo</th>
			</tr>		
			<tr>
				<td><img src='".GUILD_IMAGE_DIR."{$guild->get("image")}' height='100' width='100' /></td> <td width='90%'><div style='text-align: center; font: normal 30px Verdana, sans-serif;'>{$guild->get("name")}</div>{$guild->get("motd")}</td> <td><img src='".GUILD_IMAGE_DIR."{$guild->get("image")}'/></td>
			</tr>
		</table>
		
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>Informações da Guilda</th>
			</tr>
			<tr>
				<td>".(($guild->get("status") == 1) ? "Esta guilda está em <b>atividade</b>." : "Esta guilda está em processo de formação e será disbandada se não possuir <b>".GUILDS_VICELEADERS_NEEDED." vice-lideres</b> até <b>".$core->formatDate($guild->get("formationTime"))."</b>.")."</td>
			</tr>
			
			<tr>
				<td>Está guilda foi criada em <b>".$core->formatDate($guild->get("creationdata"))."</b>.</td>
			</tr>

					
		</table>";				

		if($_SESSION['login'] and $accountLevel == 1)
		{
			if($guild->isOnWar())
			{
				$button = "<a class='buttonstd' href='?ref=guilds.leavewar&name={$guild->get("name")}'>Desativar modo de Guerra</a>";
			}
			else 
			{
				$button = "<a class='buttonstd' href='?ref=guilds.joinwar&name={$guild->get("name")}'>Ativar modo de Guerra</a>";				
			}
			
			$module .= "
				<p>
					<a class='buttonstd' href='?ref=guilds.edit&name={$guild->get("name")}'>Editar Descrições</a>
				    <a class='buttonstd' href='?ref=guilds.disband&name={$guild->get("name")}'>Desmanchar Guild</a>
				    
				</p>				
			";
			
		}			
		
		$module .= "					
				
		<p><h3>Lista de Membros</h3></p>
		
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>Posição</th> <th>Nome e Titulo</th> <th>Membro Desde</th>
			</tr>		
		";			
				
		foreach($ranks as $value)
		{		
			$show_rank[$value['name']] = true;
		}
				
		foreach($members as $player_name => $guild_value)
		{
			
			$module .= "
				<tr>
					<td width='25%'><b>".(($show_rank[$guild_value['rank']]) ? $guild_value['rank'] : "&nbsp")."</b></td> 
					<td><a href='?ref=character.view&name=".$player_name."'>".$player_name."</a> ".(($guild_value['nick']) ? "(<i>{$guild_value['nick']}</i>)" : null)."</i></td> 
					<td>{$core->formatDate($guild_value['joinDate'])}</td>
				</tr>	
			";	

			$show_rank[$guild_value['rank']] = false;	
		}
		
		$module .= "
		</table>";
		
		if($_SESSION['login'] and $accountLevel)
		{
			$module .= "<p>";
			
			if($_SESSION['login'] and $accountLevel <= 2)
			$module .= "
					<a class='buttonstd' href='?ref=guilds.members&name={$guild->get("name")}'>Editar Membros</a>				
			";
				
			if($_SESSION['login'] and $accountLevel == 1)
			{	
				$module .= "
					<a class='buttonstd' href='?ref=guilds.ranks&name={$guild->get("name")}'>Editar Ranks</a> <a class='buttonstd' href='?ref=guilds.passleadership&name={$guild->get("name")}'>Passar Liderança</a>				
				";	
			}
			
			if($_SESSION['login'] and $accountLevel > 1)
			{	
				$module .= "
					<a class='buttonstd' href='?ref=guilds.leave&name={$guild->get("name")}'>Sair da Guild</a>				
				";	
			}			
			
			$module .= "</p>";
		}		
		
		$module .= "	
		<p><h3>Personagens Convidados</h3></p>
		
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>Nome</th> <th>Data que foi Convidado</th>
			</tr>			
		";
		
		if(count($invites) != 0)
		{
			$wasInvite = 0;
			
			foreach($invites as $player_name => $invite_date)
			{
				
				$module .= "
					<tr>
						<td><a href='?ref=character.view&name=".$player_name."'>".$player_name."</a></td> <td>{$core->formatDate($invite_date)}</td>
					</tr>	
				";	

				if($_SESSION['login'] and in_array($player_name, $character_listByName))
				{
					$wasInvite++;
				}
			}	
		}
		else
		{
			$module .= "
				<tr>
					<td>Nenhum personagem está convidado para esta guilda.</td> <td>&nbsp;</td>
				</tr>	
			";				
		}
		
		$module .= "
		</table>
		";
		
		if($_SESSION['login'] and $accountLevel and $accountLevel <= 2)
		{
			$module .= "
				<p>
					<a class='buttonstd' href='?ref=guilds.invite&name={$guild->get("name")}'>Convidar Jogador</a>
				</p>				
			";
		}
		
		if($wasInvite != 0)
		{
			$module .= "
				<p>
					<a class='buttonstd' href='?ref=guilds.acceptInvite&name={$guild->get("name")}'>Aceitar Convite</a>
				</p>				
			";			
		}

		$module .= "
		<br>
		";		
	}
}
?>