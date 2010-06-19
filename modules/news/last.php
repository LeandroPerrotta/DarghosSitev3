<?
$query = $db->query("SELECT topics.id, topics.title, polls.end_date FROM ".DB_WEBSITE_PREFIX."forum_topics as topics, ".DB_WEBSITE_PREFIX."forum_polls as polls WHERE polls.end_date > UNIX_TIMESTAMP() AND topics.id = polls.topic_id ORDER by topics.id DESC LIMIT 5");

if($query->numRows() != 0)
{
	$polls = "";
	
	while($fetch = $query->fetch())
	{
		$polls .= "
		<tr>
			<td><span style='float:left;'><a href='?ref=forum.topic&v={$fetch->id}'>{$fetch->title}</a></span> <span style='float:right;'>Termina em ".Core::formatDate($fetch->end_date)."</span></td>
		</tr>
		";
	}
	
	$module .= "
	<table cellspacing='0' cellpadding='0' class='dropdowntable-2'>
	
		<tr>
			<th>Ultimas enquetes ativas <span class='tooglePlus'></span></th>
		</tr>
					
		{$polls}
		
	</table>";	
}

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

$notices = Forum_Topics::ListNoticeTopics();

$news = 0;

if($notices)
{
	$news = count($notices);
}

if($notices)
{	
	foreach($notices as $topic)
	{
		$comment = '<p style="text-align: right;"><a href="?ref=forum.topic&v='.$topic->GetId().'">Comentar</a> ('.$topic->GetPostCount().' já comentaram!)</p>';
		Core::sendMessageBox("<span style='float: left;'>".$topic->GetTitle()."</span> <span style='float: right;'>".Core::formatDate($topic->GetDate())."</span>", "{$topic->GetTopic()} {$comment}"); 	
	}
}

if($news < 3)
{
	$limit = 3 - $news;
	
	$query = $db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT {$limit}");
	
	while($fetch = $query->fetch())
	{
		$showcomment = (HIDE_FORUMLINKS != 0) ? '<p style="text-align: right;"><a href="'.$fetch->forum_url.'">Comentar está notícia</a></p>' : '';
		Core::sendMessageBox("<span style='float: left;'>".$fetch->topic."</span> <span style='float: right;'>".Core::formatDate($fetch->post_data)."</span>", "{$fetch->post} {$showcomment}"); 
	}
}
?>