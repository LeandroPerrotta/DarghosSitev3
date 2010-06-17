<?php

define("FORUM_TOPIC_FLAGS_IS_POLL", 1);

define("FORUM_POLLS_FLAGS_IS_MULTIPLE_SELECTION", 1);
define("FORUM_POLLS_FLAGS_IS_ONLY_FOR_PREMIUM", 2);

class Forum_User
{
	private $_id, $_accountid, $_playerid;
	
	function Forum_User($id = null)
	{
		if($id)
			$this->Load($id);
	}
	
	function Save()
	{
		if($this->_id)
		{
			Core::$DB->query("
				UPDATE 
					`".DB_WEBSITE_PREFIX."forum_users` 
				SET 
					`account_id` = '{$this->_accountid}', 
					`player_id` = '{$this->_playerid}'
				WHERE 
					`id` = '{$this->_id}'
			");			
		}
		else
		{
			Core::$DB->query("
				INSERT INTO
					`".DB_WEBSITE_PREFIX."forum_users`
					(`account_id`, `player_id`)
					values
					('{$this->_accountid}', '{$this->_playerid}')
			");

			$this->_id = Core::$DB->lastInsertId();				
		}
	}
	
	function Load($id)
	{
		$query = Core::$DB->query("SELECT id, account_id, player_id FROM ".DB_WEBSITE_PREFIX."forum_users WHERE `id` = '{$id}'");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();
		
		$this->_id = $fetch->id;
		$this->_accountid = $fetch->account_id;
		$this->_playerid = $fetch->player_id;
		
		return true;
	}
	
	function LoadByAccount($account_id)
	{
		$query = Core::$DB->query("SELECT id FROM ".DB_WEBSITE_PREFIX."forum_users WHERE `account_id` = '{$account_id}'");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();		
		
		$this->Load($fetch->id);
		
		return true;
	}
	
	function GetPlayerId()
	{
		return $this->_playerid;
	}
	
	function GetAccountId()
	{
		return $this->_accountid;
	}
	
	function GetId()
	{
		return $this->_id;
	}
	
	function SetPlayerId($player_id)
	{
		$this->_playerid = $player_id;
	}	
	
	function SetAccountId($account_id)
	{
		$this->_accountid = $account_id;
	}
	
	function GetPollVote($poll_id)
	{		
		$query = Core::$DB->query("
			SELECT 
				`polls_opt`.`id`,
				`polls_opt`.`option`, 
				`vote`.`public` 
			FROM 
				`".DB_WEBSITE_PREFIX."forum_user_votes` AS `vote`, 
				`".DB_WEBSITE_PREFIX."forum_polls_opt` AS `polls_opt` 
			WHERE 
				`vote`.`user_id` = '{$this->_id}' AND 
				`vote`.`opt_id` = `polls_opt`.`id` AND
				`polls_opt`.`poll_id` = '{$poll_id}'");
		
		if($query->numRows() == 0)
			return false;
			
		$vote = array();
		
		$fetch = $query->fetch();
		
		$vote["id"] = $fetch->id;
		$vote["option"] = $fetch->option;
		$vote["public"] = $fetch->public;
			
		return $vote;	
	}
	
	function SetPollVote($option_id, $public)
	{
		Core::$DB->query("INSERT INTO `".DB_WEBSITE_PREFIX."forum_user_votes` values ('{$this->_id}', '{$option_id}', '".time()."', '{$public}')");		
	}
}

class Forum_Topics
{
	static function ListPollTopics()
	{
		$query = Core::$DB->query("SELECT id FROM ".DB_WEBSITE_PREFIX."forum_topics WHERE (flags & ".FORUM_TOPIC_FLAGS_IS_POLL.") != 0");
		
		if($query->numRows() == 0)
			return false;		
			
		$topics = array();	
			
		for($i = 0; $i < $query->numRows(); ++$i)
		{	
				$fetch = $query->fetch();
				$topics[] = new Forum_Topics($fetch->id);
		}
		
		return $topics;
	}
	
	private $_id, $_title, $_topic, $_date, $_authorid, $_isPoll = false;
	private $_poll_id, $_poll_text, $_poll_topicid, $_poll_enddate, $_poll_flags, $_poll_minlevel, $_poll_ismultiple = false, $_poll_onlypremium = false;
	private $_poll_options = array();
	private $_posts = array();
	
	function Forum_Topics($id = null)
	{
		if($id)
			$this->Load($id);
	}	
	
	function Load($id)
	{	
		$query = Core::$DB->query("SELECT id, title, topic, date, author_id, flags FROM ".DB_WEBSITE_PREFIX."forum_topics WHERE `id` = '{$id}'");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();
		
		$this->_id = $fetch->id;
		$this->_title = $fetch->title;
		$this->_topic = $fetch->topic;
		$this->_date = $fetch->date;
		$this->_authorid = $fetch->author_id;
		
		if(($fetch->flags & FORUM_TOPIC_FLAGS_IS_POLL) != 0)
		{
			$this->_isPoll = true;
			$this->LoadPoll();
		}
		
		$this->LoadPosts();
		
		return true;		
	}
	
	function Save()
	{
		if($this->_id)
		{
			
		}	
		else
		{
			$flag = 0;
			if($this->_isPoll)
			{
				$flag = pow(2, FORUM_TOPIC_FLAGS_IS_POLL - 1);
				echo "Flag: " . $flag;
			}

			Core::$DB->query("
				INSERT INTO
					`".DB_WEBSITE_PREFIX."forum_topics`
					(`title`, `topic`, `date`, `author_id`, `flags`)
					values
					('{$this->_title}', '{$this->_topic}', '{$this->_date}', '{$this->_authorid}', '{$flag}')
			");

			$this->_id = Core::$DB->lastInsertId();		

			if($this->_isPoll)
			{
				$flag = 0;
				if($this->_poll_onlypremium)
					$flag = pow(2, FORUM_POLLS_FLAGS_IS_ONLY_FOR_PREMIUM - 1);
				
				Core::$DB->query("
					INSERT INTO
						`".DB_WEBSITE_PREFIX."forum_polls`
						(`topic_id`, `text`, `end_date`, `flags`, `min_level`)
						values
						('{$this->_id}', '{$this->_poll_text}', '{$this->_poll_enddate}', '{$flag}', '{$this->_poll_minlevel}')
				");	

				$this->_poll_id = Core::$DB->lastInsertId();
			}
		}	
	}
	
	function AddPollOption($option)
	{
		Core::$DB->query("
			INSERT INTO
				`".DB_WEBSITE_PREFIX."forum_polls_opt`
				(`poll_id`, `option`)
				values
				('{$this->_poll_id}', '{$option}')
		");		
	}
	
	function LoadPosts()
	{
		$query = Core::$DB->query("SELECT id, user_id, date, topic_id, post FROM ".DB_WEBSITE_PREFIX."forum_posts WHERE `topic_id` = '{$this->_id}'");
	
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
		$query = Core::$DB->query("SELECT id, topic_id, text, end_date, flags, min_level FROM ".DB_WEBSITE_PREFIX."forum_polls WHERE `topic_id` = '{$this->_id}'");
		
		if($query->numRows() != 1)
			return false;	

		$fetch = $query->fetch();
		
		$this->_poll_id = $fetch->id;
		$this->_poll_topicid = $fetch->topic_id;
		$this->_poll_text = $fetch->text;
		$this->_poll_enddate = $fetch->end_date;
		$this->_poll_minlevel = $fetch->min_level;
		
		if(($fetch->flags & FORUM_POLLS_FLAGS_IS_MULTIPLE_SELECTION) != 0)
			$this->_poll_ismultiple = true;
			
		if(($fetch->flags & FORUM_POLLS_FLAGS_IS_ONLY_FOR_PREMIUM) != 0)
			$this->_poll_onlypremium = true;

		$query = Core::$DB->query("SELECT `id`, `poll_id`, `option` FROM `".DB_WEBSITE_PREFIX."forum_polls_opt` WHERE `poll_id` = '{$this->_poll_id}'");
		
		if($query->numRows() == 0)
			return false;	

		for($i = 0; $i < $query->numRows(); ++$i)
		{				
			$fetch = $query->fetch();	
			
			$votes = Core::$DB->query("SELECT `user_id` FROM `".DB_WEBSITE_PREFIX."forum_user_votes` WHERE `opt_id` = '{$fetch->id}'");
			
			$this->_poll_options[$fetch->id] = array (
				"poll_id" => $fetch->poll_id,
				"option" => $fetch->option,
				"votes" => $votes->numRows()
			);
		}	
	}
	
	function SendPost($post, $user_id)
	{
		Core::$DB->query("
			INSERT INTO
				`".DB_WEBSITE_PREFIX."forum_posts`
				(`user_id`, `date`, `topic_id`, `post`)
				values
				('{$user_id}', '".time()."', '{$this->_id}', '{$post}')
		");		
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
		return count($this->_posts);
	}
	
	function IsPoll()
	{
		return $this->_isPoll;
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