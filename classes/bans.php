<?php
class Bans
{
	private $db;

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}	
	
	function isBannished($account_id)
	{
		$query = $this->db->query("SELECT id FROM bans WHERE value = '{$account_id}' AND active = '1'");
		
		if($query->numRows() != 0)
			return true;
			
		return false;	
	}
	
	function isNameLocked($player_id)
	{
		$query = $this->db->query("SELECT id FROM bans WHERE type = '".BANTYPE_NAMELOCK."' AND value = '{$player_id}' AND active = '1'");
		
		if($query->numRows() != 0)
			return true;
			
		return false;		
	}
	
	function getBannishment($value)
	{
		$query = $this->db->query("SELECT id, type, expires, added, admin_id, comment, reason, action, statement FROM bans WHERE value = '{$value}' AND active = '1'");

		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$ban = array();
			
			$ban['id'] = $fetch->id;
			$ban['type'] = $fetch->type;
			$ban['expires'] = $fetch->expires;
			$ban['added'] = $fetch->added;
			$ban['admin_id'] = $fetch->admin_id;
			$ban['comment'] = $fetch->comment;
			$ban['reason'] = $fetch->reason;
			$ban['action'] = $fetch->action;
			$ban['statement'] = $fetch->statement;
			
			return $ban;
		}
		else
			return false;
	}
	
	function getNameLock($player_id)
	{
		$query = $this->db->query("SELECT id, added, admin_id, comment, reason, action, statement FROM bans WHERE type = '".BANTYPE_NAMELOCK."' AND value = '{$player_id}' AND active = '1'");

		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$ban = array();
			
			$ban['id'] = $fetch->id;
			$ban['added'] = $fetch->added;
			$ban['admin_id'] = $fetch->admin_id;
			$ban['comment'] = $fetch->comment;
			$ban['reason'] = $fetch->reason;
			$ban['action'] = $fetch->action;
			$ban['statement'] = $fetch->statement;
			
			return $ban;
		}
		else
			return false;
	}	
}
?>