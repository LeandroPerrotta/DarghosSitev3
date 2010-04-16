<?
$query = $db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."fastnews ORDER by post_data DESC LIMIT 5");
$fastnews = "";

while($fetch = $query->fetch())
{
	$resumoArray = explode(" ", $fetch->post);
	$resumo = "";
	
	for($x = 0; $x <= 6; $x++)
	{
		$resumo .= $resumoArray[$x]." ";
	}
	
	$resumo .= "...";
	
	$fastnews .= "
	<tr>
		<td>
			<span class='littleFastNew' style='width: 90%; float: left;'>".Core::formatDate($fetch->post_data)." - {$resumo}</span>
			<span class='fullFastNew' style='width: 90%; float: left; display: none; visibility: hidden;'>".Core::formatDate($fetch->post_data)." - {$fetch->post}</span>

			<span class='tooglePlus' style='float: right; vertical-align: top;'></span>		
			<br>
		</td>
	</tr>		
	";	
}

$module .= "
<table cellspacing='0' cellpadding='0' class='fastnews'>

	<tr>
		<th colspan='3'>Notícias Rápidas</th>
	</tr>
				
	{$fastnews}
	
</table>";

$query = $db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT 3");

while($fetch = $query->fetch())
{
	$showcomment = (HIDE_FORUMLINKS != 0) ? '<p style="text-align: right;"><a href="'.$fetch->forum_url.'">Comentar está notícia</a></p>' : '';
	Core::sendMessageBox("<span style='float: left;'>".$fetch->topic."</span> <span style='float: right;'>".Core::formatDate($fetch->post_data)."</span>", "{$fetch->post} {$showcomment}"); 
}
?>
