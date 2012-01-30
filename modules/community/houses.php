<?php
use \Core\Configs as g_Configs;
use \Core\Consts;

\Core\Main::requireWorldSelection();

if(isset($_GET["world"]))
{
	$world_id = (int)$_GET["world"];
	
	if(!t_Worlds::Get($world_id))
		$world_id = t_Worlds::Darghos;	
	
	if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
		$query_str = "SELECT * FROM houses WHERE world_id = {$world_id} ORDER BY `town`";
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
		$houses = new \Framework\Houses($world_id);
		$houses->load($fetch->id);
		
		if(!$houses->isValid())
		{
			$houses->delete();
			continue;
		}
		
		$_town_str = ($world_id == t_Worlds::Darghos) ? t_Towns::GetString($houses->getTown()) : t_TownsGlobal::GetString($houses->getTown());
		
		$_owner_str = "<font style='color: green; font-weight: bold;'>Vazia</font>";
		
		if($houses->get("owner") != 0)
		{
			$player = new \Framework\Player();
			$player->load($houses->get("owner"));
					
			$_owner_str = "<a href='?ref=character.view&name={$player->get("name")}'>{$player->get("name")}</a>";
	
		}	
	
		$module .= "
		<tr>
			<td>{$houses->get("name")}</td> <td>{$_town_str}</td> <td>{$_owner_str}</td> <td>{$houses->get("rent")}</td> <td>{$houses->get("size")} sqm</td>
		</tr>";			
	}
	
	$module .= "
	</table>";	
}

?>
