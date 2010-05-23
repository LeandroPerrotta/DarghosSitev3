<?php

class Forum_User
{
	private $_id, $_player_id;
	
	function Forum_User($id = null)
	{
		if($id)
			$this->Load($id);
	}
	
	function Load($id)
	{
		$query = Core::$DB->query("SELECT id, player_id FROM ".DB_WEBSITE_PREFIX."forum_users WHERE `id` = '{$id}'");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();
		
		$this->_id = $fetch->id;
		$this->_player_id = $fetch->player_id;
		
		return true;
	}
}
?>