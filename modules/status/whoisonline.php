<?php
$info = new OTS_ServerInfo(STATUS_ADDRESS, STATUS_PORT);
$status = $info->info(OTS_ServerStatus::REQUEST_BASIC_SERVER_INFO | OTS_ServerStatus::REQUEST_OWNER_SERVER_INFO | OTS_ServerStatus::REQUEST_MISC_SERVER_INFO | OTS_ServerStatus::REQUEST_PLAYERS_INFO | OTS_ServerStatus::REQUEST_MAP_INFO);

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th colspan='4'>Server Status</th>
	</tr>";	

if(!$status)
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
	
	$query = $db->query("SELECT name, vocation, level, town_id, account_id FROM players WHERE online = '1' ORDER BY name");
	
	$_totalplayers = $query->numRows();
	
	$_sorcerers = 0;	
	$_druids = 0;	
	$_paladins = 0;	
	$_knights = 0;	
	
	$_islandofpeace = 0;
	$_quendor = 0;
	$_thorn = 0;
	$_aracura = 0;
	$_salazart = 0;
	$_northrend = 0;
	$_kashmir = 0;	
	
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
			elseif($town["name"] == "Aracura")
				$_aracura++;
			elseif($town["name"] == "Salazart")
				$_salazart++;
			elseif($town["name"] == "Northrend")
				$_northrend++;
			elseif($town["name"] == "Kashmir")
				$_kashmir++;
				
			if($_isadmin)
			{
				$_characc = new Account();
				$_characc->load($fetch->account_id);
				
				if($_characc->getPremDays() > 0)
					$_premiums++;
			}
			
			$players_list .= "
			<tr>
				<td><a href='?ref=character.view&name={$fetch->name}'>{$fetch->name}</a></td> <td>{$_vocationid[$fetch->vocation]}</td> <td>{$fetch->level}</td>
			</tr>";		
		}			
	}
	
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
		<td colspan='2'>Island of Peace:</td><td colspan='2'>".Tools::getPercentOf($_islandofpeace, $_totalplayers)."%</td>
	</tr>
	<tr>
		<td>Quendor:</td><td>".Tools::getPercentOf($_quendor, $_totalplayers)."%</td><td>Thorn:</td><td>".Tools::getPercentOf($_thorn, $_totalplayers)."%</td>
	</tr>
	<tr>
		<td>Aracura:</td><td>".Tools::getPercentOf($_aracura, $_totalplayers)."%</td><td>Salazart:</td><td>".Tools::getPercentOf($_salazart, $_totalplayers)."%</td>		
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