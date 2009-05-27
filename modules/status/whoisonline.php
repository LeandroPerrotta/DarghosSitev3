<?php
$info = new OTS_ServerInfo(STATUS_ADDRESS, STATUS_PORT);
$status = $info->info(OTS_ServerStatus::REQUEST_BASIC_SERVER_INFO | OTS_ServerStatus::REQUEST_OWNER_SERVER_INFO | OTS_ServerStatus::REQUEST_MISC_SERVER_INFO | OTS_ServerStatus::REQUEST_PLAYERS_INFO | OTS_ServerStatus::REQUEST_MAP_INFO);
$who = $info->info(OTS_ServerStatus::REQUEST_EXT_PLAYERS_INFO);

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th>Server Status</th>
	</tr>";	

if(!$status)
{
	$module .= "
	<tr>
		<td>O nosso servidor se encontra neste momento <font color='#ec0404'><b>off-line</b></font>.</td>
	</tr>";
	
	
	$module .= "
</table>";		
}
else
{
	if($status->getOnlinePlayers() == 0)
		$playersonmsg = "Infelizmente ninguem est� conectado em nosso servidor neste momento.";
	elseif($status->getOnlinePlayers() == 1)
		$playersonmsg = "N�s temos 1 jogador conectado em nosso servidor.";
	else	
		$playersonmsg = "N�s temos {$status->getOnlinePlayers()} jogadores conectados em nosso servidor.";
	
	$module .= "
	<tr>
		<td>{$playersonmsg}</td>
	</tr>";
	
	$module .= "
</table>";	
	
	if(is_array($who->getPlayers()))
	{
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th width='25%'>Nome</th> <th width='15%'>Voca��o</th> <th>N�vel</th>
			</tr>
			";			
		
		foreach($who->getPlayers() as $player => $level)
		{
			$character = $core->loadClass("Character");
			$character->loadByName($player);
			
			$module .= "
			<tr>
				<td><a href='?ref=character.view&name={$character->get("name")}'>{$character->get("name")}</a></td> <td>{$_vocationid[$character->get("vocation")]}</td> <td>{$character->get("level")}</td>
			</tr>";		
		}		
		
			$module .= "
		</table>";		
	}
}


	
	

?>