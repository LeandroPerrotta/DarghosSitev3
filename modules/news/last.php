<?
use \Core\Configs;
$query = \Core\Main::$DB->query("SELECT topics.id, topics.title, polls.end_date FROM ".\Core\Tools::getSiteTable("forum_topics")." as topics, ".\Core\Tools::getSiteTable("forum_polls")." as polls WHERE polls.end_date > UNIX_TIMESTAMP() AND topics.id = polls.topic_id ORDER by topics.id DESC LIMIT 5");

if($query->numRows() != 0)
{
	$polls = "";
	
	while($fetch = $query->fetch())
	{
		$polls .= "
		<tr>
			<td><span style='float:left;'><a href='?ref=forum.topic&v={$fetch->id}'>{$fetch->title}</a></span> <span style='float:right;'>Termina em ".\Core\Main::formatDate($fetch->end_date)."</span></td>
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

$query = \Core\Main::$DB->query("SELECT * FROM ".\Core\Tools::getSiteTable("fastnews")." ORDER by post_data DESC LIMIT 5");
$fastnews = "";

while($fetch = $query->fetch())
{	
	$fastnews .= "
	<tr>
		<td >
			<span class='littleFastNew' style='width: 559px; text-overflow:ellipsis; overflow: hidden; white-space: nowrap; float: left;'>".\Core\Main::formatDate($fetch->post_data)." - {$fetch->post}</span>
			<span class='fullFastNew' style='width: 559px; float: left; display: none; visibility: hidden;'>".\Core\Main::formatDate($fetch->post_data)." - {$fetch->post}</span>

			<span class='tooglePlus' style='float: right; vertical-align: top;'></span>		
			<br>
		</td>
	</tr>		
	";	
}

$module .= "
<table style='margin-bottom: 16px;' cellspacing='0' cellpadding='0' class='fastnews'>

	<tr>
		<th colspan='3'>".tr("Notícias Rápidas")."</th>
	</tr>
				
	{$fastnews}
	
</table>";
	
	
$powerfullGuilds = \Framework\Guilds::BestKDGuildList(0);

if(count($powerfullGuilds) > 0){

    $powerfullguilds_str = "          
<div>
  <ul class='guild_list'>";
    
    foreach($powerfullGuilds as $guild){
        $powerfullguilds_str .= "
                <li>
                    <a href='?ref=guilds.details&name={$guild->GetName()}'>
                        <img src='".Configs::Get(Configs::eConf()->WEBSITE_FOLDER_GUILDS)."{$guild->GetImage()}' height='100' width='100'/>
                    </a>
                    <p>
                        <a href='?ref=guilds.details&name={$guild->GetName()}'>{$guild->GetName()}</a>
                        <span>{$guild->GetKD()} k/d</span>
                    </p>
                </li>";
    }
    
    $powerfullguilds_str .= "
    </ul>
</div>";
}
else{
    $powerfullguilds_str .= "Indisponível no momento.";
}
	
	
$module .= "
<table style='margin-bottom: 16px;' cellspacing='0' cellpadding='0' class='fastnews'>
	
	<tr>
		<th >".tr("Guilds mais poderosas")."</th>
	</tr>
	
	<tr>
	   <td>{$powerfullguilds_str}</td>
    </tr>
	
	
</table>";

$page = 1;
$lastpage = ceil(\Framework\Forums\Topics::TotalNoticeTopics() / Configs::Get(Configs::eConf()->NEWS_PER_PAGE));

if($_GET["page"] && is_numeric($_GET["page"]))
	$page = min($_GET["page"], $lastpage);	
	
$last = $page * Configs::Get(Configs::eConf()->NEWS_PER_PAGE);
$first = $last - Configs::Get(Configs::eConf()->NEWS_PER_PAGE);

//echo  $first . " /  "  . $last;
	
$notices = \Framework\Forums\Topics::ListNoticeTopics($first);

$news = 0;

define("SUMMARY_TAG", 'blockquote');

if($notices)
{	
	$news = count($notices);
	
	$t = false;
	
	foreach($notices as $topic)
	{	
		$topic instanceof \Framework\Forums\Topics;
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
		
		$logged_acc = Framework\Account::loadLogged();
		$comment = (($logged_acc && $logged_acc->getGroup() >= t_Group::GameMaster) || Configs::Get(Configs::eConf()->ENABLE_PLAYERS_COMMENT_NEWS)) ? '<a id="new-comments" href="?ref=forum.topic&v='.$topic->GetId().'">'.$topic->GetPostCount().'</a>' : null;
		
		$user = new \Framework\Forums\User();
		$user->Load($topic->GetAuthorId());
		
		$author = new \Framework\Player();
		$author->load($user->GetPlayerId());
		
		$module .= "
		<div id='new-title-bar'>
			<h3 id='new-title'>
				{$topic->GetTitle()}
			</h3>
			<div id='infos-line'>
			postado em <span>".\Core\Main::formatDate($topic->GetDate())."</span> {$comment}
			</div>	
		</div>
		<div id='new-summary'>{$summary}</div>
		";
		
		//\Core\Main::sendMessageBox("<span id='newtitle'>".$topic->GetTitle()."</span> <span style='float: right;'>".\Core\Main::formatDate($topic->GetDate())."</span>", $summary." {$comment}"); 	
	}
}
else
{
	\Core\Main::sendMessageBox("Erro", tr("Não há mais noticias."));
}

$module .= "
<div>";

if($page > 1)
{
	$module .= "
	<a class='buttonstd' href='?ref=news.last&page=".($page - 1)."'><span>".tr("Anterior")."</span></a>
	";	
}

if($page < $lastpage)
{
	$module .= "
	<a class='buttonstd' style='float: right;' href='?ref=news.last&page=".($page + 1)."'><span>".tr("Proxima")."</span></a>
	";
}

$module .= "
</div>";
?>