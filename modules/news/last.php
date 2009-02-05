<?
$query = $db_tenerian->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT 3");

while($fetch = $query->fetch())
{
	$module .=	'
	<p>'. $core->formatDate($fetch->post_data) .'</p>
	<div id="line1"></div>
	<h3>'. $fetch->topic .'</h3>
	<p>'. $fetch->post .'</p>
	';

}
?>
