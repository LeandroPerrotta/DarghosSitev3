<?php
namespace Framework\Forums;
class User
{
	private $_memberid, $_accountid, $_playerid;
	private $_bans = array();
	private $account;
	
	function __construct($id = null)
	{
		if($id)
			$this->Load($id);
	}
	
	function Save($insert = true)
	{
		if(!$insert)
		{
			\Core\Main::$DB->query("
				UPDATE 
					`".\Core\Tools::getSiteTable("forum_users")."` 
				SET 
					`account_id` = '{$this->_accountid}', 
					`player_id` = '{$this->_playerid}'
				WHERE 
					`member_id` = '{$this->_memberid}'
			");			
		}
		else
		{
			\Core\Main::$DB->query("
				INSERT INTO
					`".\Core\Tools::getSiteTable("forum_users")."`
					(`member_id`, `account_id`, `player_id`)
					values
					('{$this->_memberid}', '{$this->_accountid}', '{$this->_playerid}')
			");		
		}
	}
	
	function UpdateLoginInfoExternalForum(\Framework\Account $account, $password){
	    
	    $passwd = sha1(strtolower($account->getName()) . $password);
	    $password_salt = substr(md5(mt_rand()), 0, 4);	 

	    \Core\Main::$DB->query("
            UPDATE
                `".\Core\Tools::getForumTable("members")."`
            SET
	            `passwd` = '{$passwd}',
	            `member_name` = '{$account->getName()}'
            WHERE
                `id_member` = '{$this->_memberid}'
        ");
	}

    function UpdatePlayerNameExternalForum($player_name){
    
        \Core\Main::$DB->query("
            UPDATE
                `".\Core\Tools::getForumTable("members")."`
            SET
                `real_name` = '{$player_name}'
            WHERE
                `id_member` = '{$this->_memberid}'
        ");
    }  
	
	function InsertExternalForum(\Framework\Account $account, \Framework\Player $player, $password){
	    
	    //From SFM engine @ Subs-Members.php
	    //'passwd' => sha1(strtolower($regOptions['username']) . $regOptions['password']),
	    //'password_salt' => substr(md5(mt_rand()), 0, 4) ,
	    $passwd = sha1(strtolower($account->getName()) . $password);
	    $password_salt = substr(md5(mt_rand()), 0, 4);
	    
	    \Core\Main::$DB->query("
            INSERT INTO
            `".\Core\Tools::getForumTable("members")."`
            (`member_name`, `date_registered`, `real_name`, `passwd`, `email_address`, `hide_email`, `id_post_group`, `password_salt`, `buddy_list`, `message_labels`, `openid_uri`, `signature`, `ignore_boards`)
            values
            ('{$account->getName()}', UNIX_TIMESTAMP(), '{$player->getName()}', '{$passwd}', '{$account->getEmail()}', '1', '4', '{$password_salt}', '', '', '', '', '')
    			");  
	    
	    $this->_memberid = \Core\Main::$DB->lastInsertId();
	    
	    \Core\Main::$DB->query("
	            UPDATE
	            `".\Core\Tools::getForumTable("settings")."`
	            SET
	                `value` = '{$player->getName()}'
                WHERE
                    `variable` = 'latestRealName'
	       ");	   
	     
	    \Core\Main::$DB->query("
	            UPDATE
	            `".\Core\Tools::getForumTable("settings")."`
	            SET
	                `value` = '{$this->_memberid}'
                WHERE
                    `variable` = 'latestMember'
	       ");	    
	}
	
	function Load($member_id)
	{
		$query = \Core\Main::$DB->query("SELECT member_id, account_id, player_id FROM ".\Core\Tools::getSiteTable("forum_users")." WHERE `member_id` = '{$member_id}'");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();
		
		$this->_memberid = $fetch->member_id;
		$this->_accountid = $fetch->account_id;
		$this->_playerid = $fetch->player_id;
		
		$this->account = new \Framework\Account();
		$this->account->load($this->_accountid);
		
		return true;
	}
	
	function LoadByAccount($account_id)
	{
		$query = \Core\Main::$DB->query("SELECT `member_id` FROM ".\Core\Tools::getSiteTable("forum_users")." WHERE `account_id` = '{$account_id}'");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();		
		
		return $this->Load($fetch->member_id);
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
	
	function GetMemberId()
	{
		return $this->_memberid;
	}
	
	function SetMemberId($member_id)
	{
		$this->_memberid = $member_id;
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
				`vote`.`user_id` = '{$this->_memberid}' AND 
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
		\Core\Main::$DB->query("INSERT INTO `".\Core\Tools::getSiteTable("forum_user_votes")."` values ('{$this->_memberid}', '{$option_id}', '".time()."', '{$public}')");		
	}
	
	function AddBan($type, $date, $reason, $author)
	{
		\Core\Main::$DB->query("INSERT INTO `".\Core\Tools::getSiteTable("forum_bans")."` (`user_id`, `date`, `type`, `author`, `reason`) values ('{$this->_memberid}', '{$date}', '{$type}', '{$author}', '{$reason}')");	
	}
	
	function LoadBans()
	{
		$query = \Core\Main::$DB->query("SELECT `date`,`type`,`reason`,`author` FROM `".\Core\Tools::getSiteTable("forum_bans")."` WHERE `user_id` = '{$this->_memberid}' ORDER BY `date` DESC");
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