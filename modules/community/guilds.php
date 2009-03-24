<?php
$query = $db_tenerian->query("SELECT * FROM guilds WHERE status = '1' ORDER BY creation");	

$module .= "
<p>
	<a class='button' href='?ref=guilds.create'>Criar nova Guild</a>
</p>	
	
<p><h3>Guildas em Atividade</h3></p>	

<table cellspacing='0' cellpadding='0' id='table'>";

if($query->numRows() != 0)
{
	$module .= "
	<tr>
		<th width='3%'>&nbsp </th> <th width='25%'>Descrição</th>
	</tr>	
	";
	
	while($fetch = $query->fetch())
	{	
		$module .= "
		<tr>
			<td><img src='".GUILD_IMAGE_DIR."{$fetch->image}'' height='100' width='100' /> </td> <td style='vertical-align: middle; height: 50px;'><a href='?ref=guilds.details&name={$fetch->name}'>{$fetch->name}</a><br>{$fetch->motd}</td>
		</tr>";	
	}
}
else
{
		$module .= "
		<tr>
			<th width='25%'>Descrição</th>
		</tr>			
		
		<tr>
			<td>Não existe nenhuma guilda ativa neste momento.</td>
		</tr>";		
}

$module .= "
</table>";

unset($query);
$query = $db_tenerian->query("SELECT * FROM guilds WHERE status = '0' ORDER BY creation");	

$module .= "
<p><h3>Guildas em Formação</h3></p>	

<table cellspacing='0' cellpadding='0' id='table'>";

if($query->numRows() != 0)
{
	$module .= "
	<tr>
		<th width='3%'>&nbsp </th> <th width='25%'>Descrição</th>
	</tr>	
	";	
	
	while($fetch = $query->fetch())
	{	
		$module .= "
		<tr>
			<td><img src='".GUILD_IMAGE_DIR."{$fetch->image}'' height='100' width='100' /> </td> <td style='vertical-align: middle; height: 50px;'><a href='?ref=guilds.details&name={$fetch->name}'>{$fetch->name}</a><br>{$fetch->motd}</td>
		</tr>";	
	}
}	
else
{
		$module .= "
		<tr>
			<th width='25%'>Descrição</th>
		</tr>		
		
		<tr colspan='2'>
			<td>Não existe nenhuma guilda em formação neste momento.</td>
		</tr>";		
}


$module .= "
</table>";
?>