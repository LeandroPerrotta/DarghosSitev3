<?
$query = $db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT 3");

while($fetch = $query->fetch())
{
	$showcomment = (HIDE_FORUMLINKS != 0) ? '<p style="text-align: right;"><a href="'.$fetch->forum_url.'">Comentar está notícia</a></p>' : '';
	$core->sendMessageBox("<span style='float: left;'>".$fetch->topic."</span> <span style='float: right;'>{$core->formatDate($fetch->post_data)}</span>", "{$fetch->post} {$showcomment}"); 
}
?>
