<?
$query = $db_tenerian->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT 3");

while($fetch = $query->fetch())
{
	$module .=	'
	
	<div id="line1"></div>
	
	<p><h3>'. $fetch->topic .'</h3> <span style="text-align: right;">'. $core->formatDate($fetch->post_data) .'</span></p>
	
	
	
	<p>'. $fetch->post .'</p>
	';

}
?>
