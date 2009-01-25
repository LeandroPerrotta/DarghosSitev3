<?
$query = $db_tenerian->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT 3");

while($fetch = $query->fetch())
{
?>

	<p><? echo date("d/m/y - H:i", $fetch->post_data); ?> - <? echo date("d/m/y - H:i", $fetch->post_update); ?></p>
	<div id="line1"></div>
	<h3><? echo $fetch->topic; ?></h3>
	<p><? echo $fetch->post; ?> </p>

<?
}
?>
