<?php
$query = $db->query("SELECT id FROM players WHERE online = '1' ORDER BY name");

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th>Server Status</th>
	</tr>		
";	

if($query->numRows() != 0)
{
	$module .= "
	<tr>
		<td>Nós temos {$query->numRows()} jogadores conectados em nosso servidor.</td>
	</tr>		
</table>

<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th width='25%'>Nome</th> <th width='15%'>Vocação</th> <th>Nível</th>
	</tr>
	";		
	
	while($fetch = $query->fetch())
	{
		$character = $core->loadClass("Character");
		$character->load($fetch->id, "name, level, vocation");
		
		$module .= "
		<tr>
			<td><a href='?ref=character.view&name={$character->get("name")}'>{$character->get("name")}</a></td> <td>{$_vocationid[$character->get("vocation")]}</td> <td>{$character->get("level")}</td>
		</tr>";		
	}
	
	$module .= "		
</table>				
	";		
}
else
{
	$module .= "
	<tr>
		<td>Desculpe, mas ninguem está online.</td>
	</tr>		
</table>				
	";		
}

?>