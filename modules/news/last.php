<?
$query = $db_tenerian->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT 3");

while($fetch = $query->fetch())
{
	$core->sendMessageBox("<span style='float: left;'>".$fetch->topic."</span> <span style='float: right;'>".$core->formatDate($fetch->post_data)."</span>", $fetch->post);
}
?>
