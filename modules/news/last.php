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
<table style='margin-bottom: 16px;' cellspacing='0' cellpadding='0' class='fastnews'>

	<tr>
		<th colspan='3'>Notícias Rápidas</th>
	</tr>
				
	{$fastnews}
	
</table>";

$page = 1;
$lastpage = ceil(Forum_Topics::TotalNoticeTopics() / SHOW_NEWS);

if($_GET["page"] && is_numeric($_GET["page"]))
	$page = min($_GET["page"], $lastpage);	
	
$last = $page * SHOW_NEWS;
$first = $last - SHOW_NEWS;

//echo  $first . " /  "  . $last;
	
$notices = Forum_Topics::ListNoticeTopics($first);

$news = 0;

define("SUMMARY_TAG", 'blockquote');

if($notices)
{	
	$news = count($notices);
	
	$t = false;
	
	foreach($notices as $topic)
	{	
		$topic instanceof Forum_Topics;
		libxml_use_internal_errors(true);
		/*$xml = new SimpleXMLElement("<?xml version=\"1.0\" ?><root>" . htmlspecialchars($topic->GetTopic()) . "</root>");*/

$xmlStr = "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<root>
	".$topic->getTopic()."
</root>
";
		
		$xml = simplexml_load_string($xmlStr);
		
		$summary = "";
		
		if ($xml && $xml->{SUMMARY_TAG})
		{
			$summary = $xml->{SUMMARY_TAG}->asXML() . "<br><a href='?ref=forum.topic&v={$topic->GetId()}'>» Ler mais...</a>";
		}	
		else
		{
			$summary = $topic->GetTopic();
		}
		
		$comment = (ENABLE_NEW_COMMENTS == 1) ? '<a id="new-comments" href="?ref=forum.topic&v='.$topic->GetId().'">'.$topic->GetPostCount().'</a>' : null;
		
		$user = new Forum_User();
		$user->Load($topic->GetAuthorId());
		
		$author = new Character();
		$author->load($user->GetPlayerId());
		
		$module .= "
		<div id='new-title-bar'>
			<h3 id='new-title'>
				{$topic->GetTitle()}
			</h3>
			<div id='infos-line'>
			por <a href='?ref=character.view&name={$author->getName()}'>{$author->getName()}</a>, <span>".Core::formatDate($topic->GetDate())."</span> {$comment}
			</div>	
		</div>
		<div id='new-summary'>{$summary}</div>
		";
		
		//Core::sendMessageBox("<span id='newtitle'>".$topic->GetTitle()."</span> <span style='float: right;'>".Core::formatDate($topic->GetDate())."</span>", $summary." {$comment}"); 	
	}
}
else
{
	Core::sendMessageBox("Erro", "Não há mais noticias.");
}

$module .= "
<div>";

if($page > 1)
{
	$module .= "
	<a class='buttonstd' href='?ref=news.last&page=".($page - 1)."'><span>Anterior</span></a>
	";	
}

if($page < $lastpage)
{
	$module .= "
	<a class='buttonstd' style='float: right;' href='?ref=news.last&page=".($page + 1)."'><span>Proximas</span></a>
	";
}

$module .= "
</div>";

/*
if($news < 3)
{
	$limit = 3 - $news;
	
	$query = $db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."news ORDER by post_data DESC LIMIT {$limit}");
	
	while($fetch = $query->fetch())
	{
		$showcomment = (ENABLE_NEW_COMMENTS == 1) ? '<p style="text-align: right;"><a href="'.$fetch->forum_url.'">Comentar está notícia</a></p>' : '';
		Core::sendMessageBox("<span style='float: left;'>".$fetch->topic."</span> <span style='float: right;'>".Core::formatDate($fetch->post_data)."</span>", "{$fetch->post} {$showcomment}"); 
	}
}
*/
?>