<?php
$info = new OTS_ServerInfo(STATUS_ADDRESS, STATUS_PORT);
$status = $info->info(OTS_ServerStatus::REQUEST_BASIC_SERVER_INFO | OTS_ServerStatus::REQUEST_OWNER_SERVER_INFO | OTS_ServerStatus::REQUEST_MISC_SERVER_INFO | OTS_ServerStatus::REQUEST_PLAYERS_INFO | OTS_ServerStatus::REQUEST_MAP_INFO);

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
	$query = $db->query("SELECT name, vocation, level FROM players WHERE online = '1' ORDER BY name");
	
	if($query->numRows() == 0)
	{
		$playersonmsg = "Infelizmente ninguem está conectado em nosso servidor neste momento.";
	}	
	else	
	{		
		if($query->numRows() == 1)
			$playersonmsg = "Nós temos 1 jogador conectado em nosso servidor.";
		else	
			$playersonmsg = "Nós temos {$query->numRows()} jogadores conectados em nosso servidor.";				
	}
	
	$module .= "
	<tr>
		<td>{$playersonmsg}</td>
	</tr>";
	
	$module .= "
</table>";	
	
	if($query->numRows() > 0)
	{
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th width='35%'>Nome</th> <th width='25%'>Vocação</th> <th>Nível</th>
			</tr>
			";			
		
		while($fetch = $query->fetch())
		{			
			$module .= "
			<tr>
				<td><a href='?ref=character.view&name={$fetch->name}'>{$fetch->name}</a></td> <td>{$_vocationid[$fetch->vocation]}</td> <td>{$fetch->level}</td>
			</tr>";		
		}		
		
			$module .= "
		</table>";		
	}
}


	
	

?>