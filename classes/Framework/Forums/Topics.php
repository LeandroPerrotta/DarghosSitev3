<?php
namespace Framework\Forums;
use \Core\Configs;
class Topics
{
	const
		FLAGS_IS_POLL = 1
		,FLAGS_IS_NOTICE = 2
		
		,POLL_FLAGS_IS_MULTIPLE_SELECTION = 1
		,POLL_FLAGS_IS_ONLY_PREMIUM = 2
		
		,BAN_DAY = 0
		,BAN_WEEK = 1
		,BAN_MONTH = 2
		,BAN_PERSISTENT = 3
	;
	
	static function ListPollTopics()
	{
		$query = \Core\Main::$DB->query("SELECT id FROM ".\Core\Tools::getSiteTable("forum_topics")." WHERE (flags & ".self::FLAGS_IS_POLL.") != 0 AND `deleted` = 0 ORDER BY `date` DESC");
		
		if($query->numRows() == 0)
			return false;		
			
		$topics = array();	
			
		for($i = 0; $i < $query->numRows(); ++$i)
		{	
				$fetch = $query->fetch();
				$topics[] = new \Framework\Forums\Topics($fetch->id);
		}
		
		return $topics;
	}
	
	static function ListNoticeTopics($start, $results = NULL)
	{
		if(!$results)
			$results = Configs::Get(Configs::eConf()->NEWS_PER_PAGE);
		
		$query_str = "SELECT id FROM ".\Core\Tools::getSiteTable("forum_topics")." WHERE (flags & ".self::FLAGS_IS_NOTICE.") != 0  AND `deleted` = 0 ORDER BY `date` DESC LIMIT {$start}, {$results}";		
		$query = \Core\Main::$DB->query($query_str);
		
		if($query->numRows() == 0)
			return false;		
			
		$topics = array();	
			
		for($i = 0; $i < $query->numRows(); ++$i)
		{	
				$fetch = $query->fetch();
				$topics[] = new \Framework\Forums\Topics($fetch->id);
		}
		
		return $topics;
	}	
	
	static function TotalNoticeTopics()
	{
		$query = \Core\Main::$DB->query("SELECT id FROM ".\Core\Tools::getSiteTable("forum_topics")." WHERE (flags & ".self::FLAGS_IS_NOTICE.") != 0  AND `deleted` = 0 ORDER BY `date`");
		return $query->numRows();
	}
	
	static function DeletePost($post_id)
	{
		\Core\Main::$DB->query("DELETE FROM `".\Core\Tools::getSiteTable("forum_posts")."` WHERE `id` = '{$post_id}'");		
	}	
	
	private $_id, $_title, $_topic, $_date, $_authorid, $_deleted, $_isPoll = false, $_isNotice = false;
	private $_poll_id, $_poll_text, $_poll_topicid, $_poll_enddate, $_poll_flags, $_poll_minlevel, $_poll_ismultiple = false, $_poll_onlypremium = false;
	private $_poll_options = array();
	private $_posts = array();
	private $_postStart = 0;
	private $_postSize = 10;
	
	function __construct($id = null)
	{
		if($id)
			$this->Load($id);
	}	
	
	function Load($id)
	{	
		$query = \Core\Main::$DB->query("SELECT `id`, `title`, `topic`, `date`, `author_id`, `flags`, `deleted` FROM `".\Core\Tools::getSiteTable("forum_topics")."` WHERE `id` = '{$id}'");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();
		
		$this->_id = $fetch->id;
		$this->_title = $fetch->title;
		$this->_topic = $fetch->topic;
		$this->_date = $fetch->date;
		$this->_authorid = $fetch->author_id;
		$this->_deleted = $fetch->deleted;
		
		if(($fetch->flags & self::FLAGS_IS_POLL) != 0)
		{
			$this->_isPoll = true;
			$this->LoadPoll();
		}
		
		if(($fetch->flags & self::FLAGS_IS_NOTICE) != 0)
		{
			$this->_isNotice = true;
		}
		
		$this->LoadPosts();
		
		return true;		
	}
	
	function Save()
	{
		if($this->_id)
		{
			\Core\Main::$DB->query("
				UPDATE
					`".\Core\Tools::getSiteTable("forum_topics")."`
				SET
					`title` = '{$this->_title}',
					`topic` = '{$this->_topic}',
					`deleted` = '{$this->_deleted}'
				WHERE
					`id` = '{$this->_id}'
			");			
		}	
		else
		{
			$flag = 0;
			if($this->_isPoll)
			{
				$flag += pow(2, self::FLAGS_IS_POLL - 1);
			}
			
			if($this->_isNotice)
			{
				$flag += pow(2, self::FLAGS_IS_NOTICE - 1);
			}

			\Core\Main::$DB->query("
				INSERT INTO
					`".\Core\Tools::getSiteTable("forum_topics")."`
					(`title`, `topic`, `date`, `author_id`, `flags`)
					values
					('{$this->_title}', '{$this->_topic}', '{$this->_date}', '{$this->_authorid}', '{$flag}')
			");

			$this->_id = \Core\Main::$DB->lastInsertId();		

			if($this->_isPoll)
			{
				$flag = 0;
				if($this->_poll_onlypremium)
					$flag = pow(2, self::POLL_FLAGS_IS_ONLY_PREMIUM - 1);
				
				\Core\Main::$DB->query("
					INSERT INTO
						`".\Core\Tools::getSiteTable("forum_polls")."`
						(`topic_id`, `text`, `end_date`, `flags`, `min_level`)
						values
						('{$this->_id}', '{$this->_poll_text}', '{$this->_poll_enddate}', '{$flag}', '{$this->_poll_minlevel}')
				");	

				$this->_poll_id = \Core\Main::$DB->lastInsertId();
			}
		}	
	}
	
	function AddPollOption($option)
	{
		\Core\Main::$DB->query("
			INSERT INTO
				`".\Core\Tools::getSiteTable("forum_polls_opt")."`
				(`poll_id`, `option`)
				values
				('{$this->_poll_id}', '{$option}')
		");		
	}
	
	function LoadPosts()
	{
		$query = \Core\Main::$DB->query("SELECT id, user_id, date, topic_id, post FROM ".\Core\Tools::getSiteTable("forum_posts")." WHERE `topic_id` = '{$this->_id}' ORDER by DATE ASC LIMIT {$this->_postStart}, {$this->_postSize}");
	
		if($query->numRows() == 0)
			return false;		
			
		for($i = 0; $i < $query->numRows(); ++$i)
		{	
			$fetch = $query->fetch();
			
			$this->_posts[] = array(
				"id" => $fetch->id,
				"user_id" => $fetch->user_id,
				"date" => $fetch->date,
				"topic_id" => $fetch->topic_id,
				"post" => $fetch->post
			);
		}
		
		return true;
	}
	
	function LoadPoll()
	{
		$query = \Core\Main::$DB->query("SELECT id, topic_id, text, end_date, flags, min_level FROM ".\Core\Tools::getSiteTable("forum_polls")." WHERE `topic_id` = '{$this->_id}'");
		
		if($query->numRows() != 1)
			return false;	

		$fetch = $query->fetch();
		
		$this->_poll_id = $fetch->id;
		$this->_poll_topicid = $fetch->topic_id;
		$this->_poll_text = $fetch->text;
		$this->_poll_enddate = $fetch->end_date;
		$this->_poll_minlevel = $fetch->min_level;
		
		if(($fetch->flags & self::POLL_FLAGS_IS_MULTIPLE_SELECTION) != 0)
			$this->_poll_ismultiple = true;
			
		if(($fetch->flags & self::POLL_FLAGS_IS_ONLY_PREMIUM) != 0)
			$this->_poll_onlypremium = true;

		$query = \Core\Main::$DB->query("SELECT `id`, `poll_id`, `option` FROM `".\Core\Tools::getSiteTable("forum_polls_opt")."` WHERE `poll_id` = '{$this->_poll_id}'");
		
		if($query->numRows() == 0)
			return false;	

		for($i = 0; $i < $query->numRows(); ++$i)
		{				
			$fetch = $query->fetch();	
			
			$votes = \Core\Main::$DB->query("SELECT `user_id` FROM `".\Core\Tools::getSiteTable("forum_user_votes")."` WHERE `opt_id` = '{$fetch->id}'");
			
			$this->_poll_options[$fetch->id] = array (
				"poll_id" => $fetch->poll_id,
				"option" => $fetch->option,
				"votes" => $votes->numRows()
			);
		}	
	}
	
	function SendPost($post, $user_id)
	{
		\Core\Main::$DB->query("
			INSERT INTO
				`".\Core\Tools::getSiteTable("forum_posts")."`
				(`user_id`, `date`, `topic_id`, `post`)
				values
				('{$user_id}', '".time()."', '{$this->_id}', '{$post}')
		");		
	}
	
	function SetPostStart($start)
	{
		$this->_postStart = $start;
	}
	
	function SetTitle($title)
	{
		$this->_title = $title;
	}
	
	function SetTopic($topic)
	{
		$this->_topic = $topic;
	}
	
	function SetDate($date)
	{
		$this->_date = $date;
	}
	
	function SetAuthorId($author_id)
	{
		$this->_authorid = $author_id;
	}
	
	function SetIsPoll($isPoll = true)
	{
		$this->_isPoll = $isPoll;
	}
	
	function SetIsNotice($isNotice = true)
	{
		$this->_isNotice = $isNotice;
	}
	
	function SetPollText($poll_text)
	{
		$this->_poll_text = $poll_text;
	}
	
	function SetPollMinLevel($poll_minlevel)
	{
		$this->_poll_minlevel = $poll_minlevel;
	}
	
	function SetPollEnd($poll_end)
	{
		$this->_poll_enddate = $poll_end;
	}
	
	function SetPollIsOnlyForPremium($onlyPremium = true)
	{
		$this->_poll_onlypremium = $onlyPremium;
	}
	
	function SetIsDeleted($isDeleted = true)
	{
		$this->_deleted = $isDeleted;
	}
	
	function GetId()
	{
		return $this->_id;
	}
	
	function GetTitle()
	{
		return $this->_title;
	}
	
	function GetTopic()
	{
		return $this->_topic;
	}
	
	function GetDate()
	{
		return $this->_date;
	}
	
	function GetAuthorId()
	{
		return $this->_authorid;
	}
	
	function GetPosts()
	{
		return $this->_posts;
	}
	
	function GetPostCount()
	{
		$query = \Core\Main::$DB->query("SELECT id FROM ".\Core\Tools::getSiteTable("forum_posts")." WHERE `topic_id` = '{$this->_id}'");
		return $query->numRows();
	}
	
	function GetLastPost()
	{
		$query = \Core\Main::$DB->query("SELECT user_id, date FROM ".\Core\Tools::getSiteTable("forum_posts")." WHERE `topic_id` = '{$this->_id}' ORDER BY date DESC LIMIT 1");
		
		$fetch = $query->fetch();
		
		$array = array();
		$array["user_id"] = $fetch->user_id;
		$array["date"] = $fetch->date;
		
		return $array;
	}
	
	function IsPoll()
	{
		return $this->_isPoll;
	}
	
	function IsNotice()
	{
		return $this->_isNotice;
	}
	
	function GetPollId()
	{
		return $this->_poll_id;
	}
	
	function GetPollText()
	{
		return $this->_poll_text;
	}
	
	function GetPollEnd()
	{
		return $this->_poll_enddate;
	}
	
	function GetPollMinLevel()
	{
		return $this->_poll_minlevel;
	}
	
	function GetPollOptions()
	{
		return $this->_poll_options;
	}
	
	function PollIsMultipleSelection()
	{
		return $this->_poll_ismultiple;
	}
	
	function PollIsOnlyForPremiums()
	{
		return $this->_poll_onlypremium;
	}
	
	function GetTotalVotes()
	{
		$t = 0;
		
		foreach($this->_poll_options as $key => $value)
		{
			$t += $value["votes"];
		}
		
		return $t;
	}
}
?>