<?php
use \Core\Configs as g_Configs;
use \Core\Consts;

\Core\Main::requireWorldSelection();

if(isset($_GET["world"]))
{
	$world_id = (int)$_GET["world"];
	
	if(!t_Worlds::Get($world_id))
		$world_id = t_Worlds::Darghos;
	
	$towns = array(
		t_Towns::Quendor,
		t_Towns::Thorn,
		t_Towns::Aracura,
		t_Towns::Aaragon,
		t_Towns::IslandOfPeace,
		t_Towns::Salazart,
		t_Towns::Northrend,
		t_Towns::Kashmir
	);
	
	$town_id = t_Towns::GetByString($_GET["town"], false);
	
	$filter_onlyEmpty = false;
	if($_GET["onlyEmpty"] == "true")
		$filter_onlyEmpty = true;
	
	$filter_withBeds = false;
	if($_GET["withBeds"] == "true")
		$filter_withBeds = true;	
	
	$_townsSelector = new \Framework\HTML\SelectBox();
	$_townsSelector->SetName("town");
	
	foreach($towns as $t)
	{
		$selected = ($town_id && t_Towns::GetString($t) == t_Towns::GetString($town_id)) ? true : false;
		$_townsSelector->AddOption(t_Towns::GetString($t), t_Towns::GetString($t), $selected);
	}
	
	$module .= '
	<form action="'.$_SERVER["REQUEST_URI"].'" method="GET">
		<fieldset>
		
			<input type="hidden" name="ref" value="community.houses"/>
		
			<p>		
				<label for="town">Escolha a cidade</label>
				'.$_townsSelector->Draw().'
			</p>	
			
			<p>		
				<label for="filter">Filtros</label>
				<input type="checkbox" name="onlyEmpty" '.(($filter_onlyEmpty) ? 'checked="checked"' : '').' value="true" /> Exibir somente as casas sem um dono.
				<br><input type="checkbox" name="withBeds" '.(($filter_withBeds) ? 'checked="checked"' : '').' value="true" /> Exibir somente as casas que possuam camas.
			</p>
			
			<p class="line"></p>
			
			<p>
				<input class="button" type="submit" value="Enviar" />
			</p>		
		</fieldset>
	</form>	
	';	
	
	if($town_id)
	{
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "SELECT `id` FROM houses WHERE `world_id` = {$world_id} AND `town` = {$town_id} ORDER BY `name` ASC";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT * FROM houses ORDER BY `townid`";
		
		$query = \Core\Main::$DB->query($query_str);		
		
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>Lista de Casas</th>
			</tr>
			<tr>
				<td>Atualmente nós temos {$query->numRows()} casas na cidade de ". t_Towns::GetString($town_id) .".</td>
			</tr>
		</table>";
		
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th>Nome</th> <th>Dono</th> <th width='10%'>Aluguel</th> <th width='10%'>Tamanho</th> <th width='10%'>Camas</th>
			</tr>";
		
		while($fetch = $query->fetch())
		{		
			$houses = new \Framework\Houses($world_id);
			$houses->load($fetch->id, $world_id);
			
			if(!$houses->isValid())
			{
				$houses->delete();
				continue;
			}
			
			if($filter_onlyEmpty && !$houses->isEmpty())
				continue;
			
			if($filter_withBeds && $houses->get("beds") == 0)
				continue;
			
			$_owner_str = "<font style='color: green; font-weight: bold;'>Vazia</font>";
			
			if($houses->get("owner") != 0)
			{
				$player = new \Framework\Player();
				$player->load($houses->get("owner"));
						
				$_owner_str = "<a href='?ref=character.view&name={$player->get("name")}'>{$player->get("name")}</a>";
			}	
		
			$module .= "
			<tr>
				<td>{$houses->get("name")}</td> <td>{$_owner_str}</td> <td>{$houses->get("rent")}</td> <td>{$houses->get("size")} sqm</td> <td>{$houses->get("beds")}</td>
			</tr>";			
		}
		
		$module .= "
		</table>";	
	}
}

?>
