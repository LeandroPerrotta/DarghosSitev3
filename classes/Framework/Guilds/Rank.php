<?php 
namespace Framework\Guilds;
use \Core\Configs as g_Configs;
use \Core\Consts;
class Rank
{
	private $_id, $_guildid, $_name, $_level;
	
	public $Members = array();
	
	/*
	 * Static Functions
	 */
	
	static function RankList($guild_id)
	{		
		$query = \Core\Main::$DB->query("SELECT `id` FROM `guild_ranks` WHERE `guild_id` = '{$guild_id}'");
		
		if($query->numRows() == 0)
			return false;		

		$_ranks = array();	
		
		
		for($i = 0; $i < $query->numRows(); ++$i)
		{			
			$fetch = $query->fetch();			
			
			$rank = new Rank();
			$rank->Load($fetch->id);
			
			$_ranks[] = $rank;
		}
		
		return $_ranks;
	}
	
	/*
	 * Constructor and Functions
	 */
	
	function __construct()
	{

	}

	function Load($rank_id)
	{		
		$query = \Core\Main::$DB->query("SELECT `id`, `guild_id`, `name`, `level` FROM `guild_ranks` WHERE `id` = '{$rank_id}'");
		
		if($query->numRows() == 0)
			return false;
	
		$fetch = $query->fetch();

		$this->_id = $fetch->id;
		$this->_guildid = $fetch->guild_id;
		$this->_name = $fetch->name;
		$this->_level = $fetch->level;
		
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "SELECT `id` FROM `players` WHERE `rank_id` = '{$this->_id}'";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT `player_id` FROM `guild_members` WHERE `rank_id` = '{$this->_id}'";
			
		$query = \Core\Main::$DB->query($query_str);

		if($query->numRows() != 0)
		{		
			for($i = 0; $i < $query->numRows(); ++$i)
			{					
				$fetch = $query->fetch();
				
				$player = new \Framework\Player();

				if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)		
					$player_id = $fetch->id;
				elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
					$player_id = $fetch->player_id;

				if(!$player->load($player_id))
					return false;	
				
				$this->Members[] = $player;
				//echo "Rank:" . $player->getName() . "<br>";
			}			
		}
		
		return true;
	}
	
	function Save()
	{
		if($this->_id)
		{
			\Core\Main::$DB->query("
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
			\Core\Main::$DB->query("
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
		\Core\Main::$DB->query("DELETE FROM `guild_ranks` WHERE `id` = '{$this->_id}'");		
	}
	
	function MemberCount()
	{
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "SELECT `id` FROM `players` WHERE `rank_id` = '{$this->_id}'";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT `player_id` FROM `guild_members` WHERE `rank_id` = '{$this->_id}'";
			
		$query = \Core\Main::$DB->query($query_str);		
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