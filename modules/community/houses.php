<?php
$query = $db->query("SELECT * FROM houses");

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th>Lista de Casas</th>
	</tr>
	<tr>
		<td>Atualmente nós temos {$query->numRows()} casas em nosso servidor.</td>
	</tr>
</table>";		

$module .= "
<table cellspacing='0' cellpadding='0' id='table'>
	<tr>
		<th width='20%'>Nome</th> <th width='10%'>Cidade</th> <th width='25%'>Dono</th> <th width='10%'>Aluguel</th> <th>Tamanho</th>
	</tr>";

while($fetch = $query->fetch())
{
	$houses = $core->loadClass("Houses");
	$houses->load($fetch->id);
	
	if($houses->get("owner") == 0)
	{
		$module .= "
		<tr>
			<td>{$houses->get("name")}</td> <td>{$_townid[$houses->get("townid")]}</td> <td><font style='color: green; font-weight: bold;'>Vazia</font></td> <td>{$houses->get("rent")}</td> <td>{$houses->get("size")} sqm</td>
		</tr>";		
	}	
	else
	{
		$character = $core->loadClass("Character");
		$character->load($houses->get("owner"), "name");		
		
		$module .= "
		<tr>
			<td>{$houses->get("name")}</td> <td>{$_townid[$houses->get("townid")]["name"]}</td> <td><a href='?ref=character.view&name={$character->get("name")}'>{$character->get("name")}</a></td> <td>{$houses->get("rent")}</td> <td>{$houses->get("size")} sqm</td>
		</tr>";			
	}
}

$module .= "
</table>";	

?>
