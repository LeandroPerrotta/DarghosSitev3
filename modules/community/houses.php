<?php
use \Core\Configs as g_Configs;
use \Core\Consts;
if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
	$query_str = "SELECT * FROM houses ORDER BY `town`";
elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
	$query_str = "SELECT * FROM houses ORDER BY `townid`";

$query = \Core\Main::$DB->query($query_str);

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
		<th>Nome</th> <th>Cidade</th> <th>Dono</th> <th width='10%'>Aluguel</th> <th width='10%'>Tamanho</th>
	</tr>";

while($fetch = $query->fetch())
{
	$houses = new \Framework\Houses();
	$houses->load($fetch->id);
	
	if(!$houses->isValid())
	{
		$houses->delete();
		continue;
	}
	
	$_town = new t_Towns($houses->get("townid"));
	
	if($houses->get("owner") == 0)
	{
		$module .= "
		<tr>
			<td>{$houses->get("name")}</td> <td>{$_town->GetType()}</td> <td><font style='color: green; font-weight: bold;'>Vazia</font></td> <td>{$houses->get("rent")}</td> <td>{$houses->get("size")} sqm</td>
		</tr>";		
	}	
	else
	{
		$player = new \Framework\Player();
		$player->load($houses->get("owner"));		
		
		$module .= "
		<tr>
			<td>{$houses->get("name")}</td> <td>{$_town->getType()}</td> <td><a href='?ref=character.view&name={$player->get("name")}'>{$player->get("name")}</a></td> <td>{$houses->get("rent")}</td> <td>{$houses->get("size")} sqm</td>
		</tr>";			
	}
}

$module .= "
</table>";	

?>
