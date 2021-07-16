<?php
namespace Framework\Forums;
class User
{
	private $_id, $_accountid, $_playerid;
	private $_bans = array();
	private $account;
	
	function __construct($id = null)
	{
		if($id)
			$this->Load($id);
	}
	
	function Save()
	{
		if($this->_id)
		{
			\Core\Main::$DB->query("
				UPDATE 
					`".\Core\Tools::getSiteTable("forum_users")."` 
				SET 
					`account_id` = '{$this->_accountid}', 
					`player_id` = '{$this->_playerid}'
				WHERE 
					`id` = '{$this->_id}'
			");			
		}
		else
		{
			\Core\Main::$DB->query("
				INSERT INTO
					`".\Core\Tools::getSiteTable("forum_users")."`
					(`account_id`, `player_id`)
					values
					('{$this->_accountid}', '{$this->_playerid}')
			");

			$this->_id = \Core\Main::$DB->lastInsertId();				
		}
	}
	
	function Load($id)
	{
		$query = \Core\Main::$DB->query("SELECT id, account_id, player_id FROM ".\Core\Tools::getSiteTable("forum_users")." WHERE `id` = '{$id}'");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();
		
		$this->_id = $fetch->id;
		$this->_accountid = $fetch->account_id;
		$this->_playerid = $fetch->player_id;
		
		$this->account = new \Framework\Account();
		$this->account->load($this->_accountid);
		
		return true;
	}
	
	function LoadByAccount($account_id)
	{
		$query = \Core\Main::$DB->query("SELECT id FROM ".\Core\Tools::getSiteTable("forum_users")." WHERE `account_id` = '{$account_id}'");
		
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
	
	function GetAccount()
	{
		return $this->account;
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
		$query = \Core\Main::$DB->query("
			SELECT 
				`polls_opt`.`id`,
				`polls_opt`.`option`, 
				`vote`.`public` 
			FROM 
				`".\Core\Tools::getSiteTable("forum_user_votes")."` AS `vote`, 
				`".\Core\Tools::getSiteTable("forum_polls_opt")."` AS `polls_opt` 
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
		\Core\Main::$DB->query("INSERT INTO `".\Core\Tools::getSiteTable("forum_user_votes")."` values ('{$this->_id}', '{$option_id}', '".time()."', '{$public}')");		
	}
	
	function AddBan($type, $date, $reason, $author)
	{
		\Core\Main::$DB->query("INSERT INTO `".\Core\Tools::getSiteTable("forum_bans")."` (`user_id`, `date`, `type`, `author`, `reason`) values ('{$this->_id}', '{$date}', '{$type}', '{$author}', '{$reason}')");	
	}
	
	function LoadBans()
	{
		$query = \Core\Main::$DB->query("SELECT `date`,`type`,`reason`,`author` FROM `".\Core\Tools::getSiteTable("forum_bans")."` WHERE `user_id` = '{$this->_id}' ORDER BY `date` DESC");
		if($query && $query->numRows() == 0)
		{
			return false;
		}

		for($i = 0; $i < $query->numRows(); ++$i)
		{	
			$fetch = $query->fetch();
			$this->_bans[] = array(
				"date" => "{$fetch->date}",
				"type" => "{$fetch->type}",
				"reason" => "{$fetch->reason}",
				"author" => "{$fetch->author}"
			);
		}		

		return true;
	}
	
	function IsBannished()
	{
		if(!$this->LoadBans())
			return false;
			
		foreach($this->_bans as $key => $values)
		{
			if($values["type"] == Topics::BAN_DAY && $values["date"] + (60 * 60 * 24) > time())
			{
				return $values;
			}
			elseif($values["type"] == Topics::BAN_WEEK && $values["date"] + (60 * 60 * 24 * 7) > time())
			{
				return $values;
			}
			elseif($values["type"] == Topics::BAN_MONTH && $values["date"] + (60 * 60 * 24 * 30) > time())
			{
				return $values;
			}
			elseif($values["type"] == Topics::BAN_PERSISTENT)
			{
				return $values;
			}
		}
		
		return false;
	}
}
?>