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
				`vote`.`opt_id` 
			FROM 
				`".DB_WEBSITE_PREFIX."forum_user_votes` AS `vote`, 
				`".DB_WEBSITE_PREFIX."forum_polls_opt` AS `polls_opt` 
			WHERE 
				`vote`.`user_id` = '{$this->_id}' AND 
				`vote`.`opt_id` = `polls_opt`.`id` AND
				`polls_opt`.`poll_id` = '{$poll_id}'");
		
		if($query->numRows() == 0)
			return false;
			
		return $query->fetch()->opt_id;	
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
			
			$this->_poll_options[$fetch->id] = array (
				"poll_id" => $fetch->poll_id,
				"option" => $fetch->option
			);
		}	
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
}
?>