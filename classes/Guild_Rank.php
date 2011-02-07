<?php 
class Guild_Rank
{
	private $_id, $_guildid, $_name, $_level;
	
	public $Members = array();
	
	/*
	 * Static Functions
	 */
	
	static function RankList($guild_id)
	{		
		$query = Core::$DB->query("SELECT `id` FROM `guild_ranks` WHERE `guild_id` = '{$guild_id}'");
		
		if($query->numRows() == 0)
			return false;		

		$_ranks = array();	
		
		
		for($i = 0; $i < $query->numRows(); ++$i)
		{			
			$fetch = $query->fetch();			
			
			$rank = new Guild_Rank();
			$rank->Load($fetch->id);
			
			$_ranks[] = $rank;
		}
		
		return $_ranks;
	}
	
	/*
	 * Constructor and Functions
	 */
	
	function Guild_Rank()
	{

	}

	function Load($rank_id)
	{		
		$query = Core::$DB->query("SELECT `id`, `guild_id`, `name`, `level` FROM `guild_ranks` WHERE `id` = '{$rank_id}'");
		
		if($query->numRows() == 0)
			return false;
	
		$fetch = $query->fetch();

		$this->_id = $fetch->id;
		$this->_guildid = $fetch->guild_id;
		$this->_name = $fetch->name;
		$this->_level = $fetch->level;
		
		if(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "SELECT `id` FROM `players` WHERE `rank_id` = '{$this->_id}'";
		elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "SELECT `player_id` FROM `guild_members` WHERE `rank_id` = '{$this->_id}'";
			
		$query = Core::$DB->query($query_str);
		
		if($query->numRows() != 0)
		{			
			for($i = 0; $i < $query->numRows(); ++$i)
			{					
							
				$fetch = $query->fetch();
				
				$character = new Character();
				
				if(!$character->load($fetch->player_id))
					return false;	
				
				$this->Members[] = $character;
				//echo "Rank:" . $character->getName() . "<br>";
			}			
		}	
	}
	
	function Save()
	{
		if($this->_id)
		{
			Core::$DB->query("
				UPDATE 
					`guild_ranks` 
				SET 
					`name` = '{$this->_name}', 
					`level` = '{$this->_level}', 
					`guild_id` = '{$this->_guildid}'
				WHERE 
					`id` = '{$this->_id}'
			");			
		}
		else
		{
			Core::$DB->query("
				INSERT INTO
					`guild_ranks`
					(`name`, `guild_id`, `level`)
					values
					('{$this->_name}', '{$this->_guildid}', '{$this->_level}')
			");			
		}
	}
	
	function Delete()
	{
		Core::$DB->query("DELETE FROM `guild_ranks` WHERE `id` = '{$this->_id}'");		
	}
	
	function MemberCount()
	{
		if(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "SELECT `id` FROM `players` WHERE `rank_id` = '{$this->_id}'";
		elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "SELECT `player_id` FROM `guild_members` WHERE `rank_id` = '{$this->_id}'";
			
		$query = Core::$DB->query($query_str);		
		return $query->numRows();
	}
	
	/*
	 * Getters and Setters
	 */
	
	function GetId()
	{
		return $this->_id;
	}
	
	function GetGuildId()
	{
		return $this->_guildid;
	}
	
	function GetName()
	{
		return $this->_name;
	}
	
	function GetLevel()
	{
		return $this->_level;
	}
	
	function SetGuildId($guild_id)
	{
		$this->_guildid = $guild_id;
	}
	
	function SetName($name)
	{
		$this->_name = $name;
	}
	
	function SetLevel($level)
	{
		$this->_level = $level;
	}
}
?>