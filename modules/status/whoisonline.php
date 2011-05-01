<?php
$status_query = Core::$DB->query("SELECT `players`, `online`, `uptime`, `afk`, `date` FROM `serverstatus` ORDER BY `date` DESC LIMIT 1");
$stats_fetch = $status_query->fetch();

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th colspan='4'>Server Status</th>
	</tr>";	

if($stats_fetch->online == 0 || $stats_fetch->date < time - 60 * 5)
{
	$module .= "
	<tr>
		<td colspan='4'>O nosso servidor se encontra neste momento <font color='#ec0404'><b>off-line</b></font>.</td>
	</tr>";
	
	
	$module .= "
</table>";		
}
else
{
	$_isadmin = false;
		
	if($_SESSION['login'])
	{
		$_loginacc = new Account();
		$_loginacc->load($_SESSION['login'][0]);
		
		if($_loginacc->getGroup() == GROUP_ADMINISTRATOR)
		{
			$_isadmin = true;
		}
	}		
	
	if($_isadmin)
	{
		$_premiums = 0;
	}
	
	if(SERVER_DISTRO == DISTRO_TFS)
		$query = $db->query("
		SELECT 
			name, 
			vocation, 
			level, 
			town_id, 
			account_id, 
			promotion,
			afk
		FROM 
			`players`
		WHERE
			`online` = '1'
		ORDER BY name");
	else
		$query = $db->query("SELECT name, vocation, level, town_id, account_id FROM players WHERE online = '1' ORDER BY name");
	
	$_totalplayers = $stats_fetch->players + $stats_fetch->afk;
	$_afkPlayers = $stats_fetch->afk;
	
	$_sorcerers = 0;	
	$_druids = 0;	
	$_paladins = 0;	
	$_knights = 0;	
	
	$_islandofpeace = 0;
	$_quendor = 0;
	$_thorn = 0;
	$_aaragon = 0;
	$_salazart = 0;
	$_northrend = 0;
	$_kashmir = 0;	
	$_aracura = 0;	
	
	if($_totalplayers == 0)
	{
		$playersonmsg = "Infelizmente ninguem está conectado em nosso servidor neste momento.";
	}	
	else	
	{		
		$players_list = "";
		
		if($_totalplayers == 1)
			$playersonmsg = "Nós temos 1 jogador conectado em nosso servidor.";
		else	
			$playersonmsg = "Nós temos {$_totalplayers} jogadores conectados em nosso servidor.";	
			
		if($_afkPlayers == 1)
			$playersonmsg .= " Destes, 1 está treinando.";
		elseif($_afkPlayers == 0)
			$playersonmsg .= " Destes, nenhum está treinando.";
		elseif($_afkPlayers > 1)
			$playersonmsg .= " Destes, {$_afkPlayers} estão treinando.";

		while($fetch = $query->fetch())
		{			
			if(Tools::isSorcerer($fetch->vocation))
				$_sorcerers++;
			elseif(Tools::isDruid($fetch->vocation))
				$_druids++;				
			elseif(Tools::isPaladin($fetch->vocation))
				$_paladins++;				
			else
				$_knights++;	
				
			$town = $_townid[$fetch->town_id];
			
			if($town["name"] == "Island of Peace")
				$_islandofpeace++;
			elseif($town["name"] == "Quendor")
				$_quendor++;
			elseif($town["name"] == "Thorn")
				$_thorn++;
			elseif($town["name"] == "Aaragon")
				$_aaragon++;
			elseif($town["name"] == "Salazart")
				$_salazart++;
			elseif($town["name"] == "Northrend")
				$_northrend++;
			elseif($town["name"] == "Kashmir")
				$_kashmir++;
			elseif($town["name"] == "Aracura")
				$_aracura++;

			$_characc = new Account();
			$_characc->load($fetch->account_id);				
				
			if($_isadmin)
			{

				
				if($_characc->getPremDays() > 0)
					$_premiums++;
			}
			
			$isAfk = $fetch->afk;
			
			$vocation_id = $fetch->vocation;
			
			if(SERVER_DISTRO == DISTRO_TFS && $fetch->promotion == 1 && $_characc->getPremDays() > 0)
			{
				$vocation_id += 4;
			}
			
			//var_dump($isAfk);
			
			$players_list .= "
			<tr>
				<td><a ".(($isAfk) ? "class='afkPlayer'" : null)." href='?ref=character.view&name={$fetch->name}'>{$fetch->name}</a></td> <td>{$_vocationid[$vocation_id]}</td> <td>{$fetch->level}</td>
			</tr>";		
		}			
	}
	
	if($_totalplayers > 0)
	{
		$module .= "
		<tr>
			<td colspan='4'>{$playersonmsg}</td>
		</tr>
		<tr>
			<td colspan='4'><b>Destes, são das vocações:</b></td>
		</tr>
		<tr>
			<td>Sorcerer's:</td><td>".Tools::getPercentOf($_sorcerers, $_totalplayers)."%</td><td>Druid's:</td><td>".Tools::getPercentOf($_druids, $_totalplayers)."%</td>
		</tr>
		<tr>
			<td>Paladin's:</td><td>".Tools::getPercentOf($_paladins, $_totalplayers)."%</td><td>Knight's:</td><td>".Tools::getPercentOf($_knights, $_totalplayers)."%</td>
		</tr>
		<tr>
			<td colspan='4'><b>Destes, se localizam nas cidades:</b></td>
		</tr>
		<tr>
			<td>Island of Peace:</td><td>".Tools::getPercentOf($_islandofpeace, $_totalplayers)."%</td>
			<td>Quendor:</td><td>".Tools::getPercentOf($_quendor, $_totalplayers)."%</td>
		</tr>
		<tr>
			<td>Thorn:</td><td>".Tools::getPercentOf($_thorn, $_totalplayers)."%</td><td>Aracura:</td><td>".Tools::getPercentOf($_aracura, $_totalplayers)."%</td>
		</tr>
		<tr>
			<td>Aaragon:</td><td>".Tools::getPercentOf($_aaragon, $_totalplayers)."%</td><td>Salazart:</td><td>".Tools::getPercentOf($_salazart, $_totalplayers)."%</td>		
		</tr>
		<tr>
			<td>Northrend:</td><td>".Tools::getPercentOf($_northrend, $_totalplayers)."%</td><td>Kashmir:</td><td>".Tools::getPercentOf($_kashmir, $_totalplayers)."%</td>	
		</tr>";
		
		if($_isadmin)
		{
			$module .= "
			<tr>
				<td colspan='4'><b>Destes, são:</b></td>
			</tr>
			<tr>
				<td>Free Account's:</td><td>".Tools::getPercentOf($_totalplayers - $_premiums, $_totalplayers)."%</td><td>Premium Account's:</td><td>".Tools::getPercentOf($_premiums, $_totalplayers)."%</td>
			</tr>		
			";
		}
	}
	else
	{
		$module .= "
		<tr>
			<td colspan='4'>{$playersonmsg}</td>
		</tr>";		
	}
	
	$module .= "
</table>";	
	
	if($query->numRows() > 0)
	{
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th width='35%'>Nome</th> <th width='25%'>Vocação</th> <th>Nível</th>
			</tr>

			{$players_list}
			
		</table>";		
	}
}


	
	

?>