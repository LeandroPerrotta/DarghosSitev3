<?php

$module .= "
<p>
	<table cellspacing='0' cellpadding='0' id='table'>
	
		<tr>
			<th width='20%'>Personagem </th> <th width='20%'>Item </th> <th width='15%'>Data </th> <th width='10%'>Custo </th> <th>Status </th>
		</tr>
";		

$query = $db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."itemshop WHERE account_id = '{$_SESSION['login'][0]}' ORDER BY time DESC");

if($query->numRows() != 0)
{
	while($fetch = $query->fetch())
	{
		$character = $core->loadClass("character");
		$character->load($fetch->player_id, "name");
		
		$itemshop_list = $core->loadClass("itemshop_list");
		$itemshop_list->load($fetch->itemlist_id);		
		
		//$status = $_itemshopstatus[$fetch->received];
		
		$module .= "
		<tr>
			<td><a href='?ref=character.view&name={$character->get("name")}'>{$character->get("name")}</a></td> <td>{$itemshop_list->get("count")}x {$itemshop_list->get("name")}</td> <td>{$core->formatDate($fetch->time)}</td> <td>{$itemshop_list->get("cost")} dias</td> <td>$status</td>
		</tr>";			
	}
}
else
	$module .= "
		<tr>
			<td colspan='5'>Voc� ainda n�o comprou nada em nosso Item Shop.</td>
		</tr>";

$module .= "
	</table>
</p>";
?>