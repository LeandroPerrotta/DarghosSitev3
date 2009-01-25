<?
$query = $db_tenerian->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT 3");

while($fetch = $query->fetch())
{
	$module .=	'
	<p>'. date("d/m/y - H:i", $fetch->post_data) .' - '. date("d/m/y - H:i", $fetch->post_update) .'</p>
	<div id="line1"></div>
	<h3>'. $fetch->topic .'</h3>
	<p>'. $fetch->post .'</p>
	';

}
?>
