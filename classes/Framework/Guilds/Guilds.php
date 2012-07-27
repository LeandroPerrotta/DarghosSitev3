<?php
namespace Framework;
use \Core\Configs as g_Configs;
use \Core\Consts;
class Guilds
{
	const
		STATUS_FORMATION = 0
		,STATUS_FORMED = 1
		
		,RANK_LEADER = 6
		,RANK_VICE = 5
		,RANK_MEMBER = 4
		,RANK_MEMBER_OPT_1 = 3
		,RANK_MEMBER_OPT_2 = 2
		,RANK_MEMBER_OPT_3 = 1
		,RANK_NO_MEMBER = 0
		
		,DEFAULT_IMAGE = "default_logo.gif"
		
		,WAR_STARTED = 1
		,WAR_DISABLED = 0
		;
		
	private $_id, $_worldId, $_name, $_ownerid, $_creationdate, $_motd, $_balance, $_image, $_status, $_formationTime, $_guildPoints, $_guildBetterPoints;
	public $Ranks = array(), $Invites = array(), $Wars = array();
	private $_trash_ranks = array();
	
	function __construct()
	{

	}

	static function isAtSameWorld(Guilds $guild, $comp_guild)
	{
		if(!is_array($comp_guild))
		{
			return $guild->getWorldId() == $comp_guild->getWorldId();
		}
	
		foreach($comp_guild as $g)
		{
			$g instanceof Guilds;
				
			if($g->getWorldId() != $guild->getWorldId())
				return false;
		}
	
		return true;
	}	
	
	/**
	 * Analisa todos personagens membros de uma guild de uma determinada conta em busca do guild level mais alto.
	 * @param Account account <p>Conta a ser verificada.</p>
	 * @param int guild_id <p>Id da guild que os personagens devem pertencer.</p>
	 * @return guild level.
	 */		
	static function GetAccountLevel(Account $account, $guild_id)
	{		
		$char_list = $account->getCharacterList(Account::PLAYER_LIST_BY_ID);
		$level = 0;
		
		foreach($char_list as $player_id)
		{
			$player = new Player();
			$player->load($player_id);
			$player->LoadGuild();
			
			if($player->GetGuildId() == $guild_id && $player->GetGuildLevel() > $level)
				$level = $player->GetGuildLevel();
		}
		
		if($level != self::RANK_LEADER && $account->getGroup() == \t_Group::Administrator)
		{
			return self::RANK_LEADER;	
		}	
		
		return $level;
	}
	
	static function GetGuildIdByRankId($rank_id){
		$query = \Core\Main::$DB->query("SELECT `guild_id` FROM `guild_ranks` WHERE `id` = `{$rank_id}`");
		if($query->numRows() == 0)
			return false;
	
		return $query->fetch()->guild_id;
	}	
	
	static function IsAccountGuildOwner(Account $account, Guilds $guild)
	{
		$char_list = $account->getCharacterList(Account::PLAYER_LIST_BY_ID);
		foreach($char_list as $player_id)
		{
			if($guild->GetOwnerId() == $player_id)
				return true;
		}
		
		return false;
	}
	
	static function ActivedGuildsList($world_id)
	{
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT `id` FROM `guilds`, `".\Core\Tools::getSiteTable("guilds")."` WHERE `status` = '".self::STATUS_FORMED."' ORDER BY `creationdate`";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "SELECT `guilds`.`id` FROM `guilds` LEFT JOIN `".\Core\Tools::getSiteTable("guilds")."` as `guild_site` ON `guild_site`.`guild_id` = `guilds`.`id` WHERE `guilds`.`world_id` = {$world_id} AND `guild_site`.`status` = '".self::STATUS_FORMED."' ORDER BY `guilds`.`creationdata`";
			
		$query = \Core\Main::$DB->query($query_str);
		
		if($query->numRows() == 0)
		{
			return false;
		}
		
		$guildList = array();
		
		for($i = 0; $i < $query->numRows(); ++$i)
		{	
			$fetch = $query->fetch();
			
			$guild = new Guilds();
			$guild->Load($fetch->id);
			
			$guildList[] = $guild;
		}			
		
		return $guildList;
	}
	
	static function FormingGuildsList($world_id)
	{
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT `id` FROM `guilds`, `".\Core\Tools::getSiteTable("guilds")."` WHERE `id` = `guild_id` AND `status` = '".self::STATUS_FORMATION."' ORDER BY `creationdate`";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "SELECT `id` FROM `guilds`, `".\Core\Tools::getSiteTable("guilds")."` WHERE `world_id` = {$world_id} AND `id` = `guild_id` AND `status` = '".self::STATUS_FORMATION."' ORDER BY `creationdata`";
			
		$query = \Core\Main::$DB->query($query_str);		

		if($query->numRows() == 0)
		{
			return false;
		}
		
		$guildList = array();
		
		for($i = 0; $i < $query->numRows(); ++$i)
		{	
			$fetch = $query->fetch();
			
			$guild = new Guilds();
			$guild->Load($fetch->id);
			
			$guildList[] = $guild;
		}			
		
		return $guildList;		
	}
	
	function Load($id)
	{
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "
			SELECT 
				`guilds`.`id`, 
				`guilds`.`name`, 
				`guilds`.`owner_id`, 
				`guilds`.`creationdate`, 
				`guilds`.`motd`, 
				`guilds_site`.`image`, 
				`guilds_site`.`status`, 
				`guilds_site`.`guilds`.`formationTime`, 
				`guilds_site`.`guild_points`, 
				`guilds_site`.`guild_better_points` 
			FROM 
				`guilds` 
			LEFT JOIN 
				`".\Core\Tools::getSiteTable("guilds")."` as `guilds_site`
			ON
				`guilds`.`id` = `guilds_site`.`guild_id`
			WHERE 
				`guilds`.`id` = '{$id}'";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "
			SELECT 
				`guilds`.`id`, 
				`guilds`.`world_id`, 				
				`guilds`.`name`, 
				`guilds`.`ownerid`, 
				`guilds`.`creationdata`, 
				`guilds`.`motd`, 
				`guilds`.`balance`, 
				`guilds_site`.`image`, 
				`guilds_site`.`status`, 
				`guilds_site`.`formationTime`, 
				`guilds_site`.`guild_points`, 
				`guilds_site`.`guild_better_points` 
			FROM 
				`guilds` 
			LEFT JOIN 
				`".\Core\Tools::getSiteTable("guilds")."` as `guilds_site`
			ON
				`guilds`.`id` = `guilds_site`.`guild_id`
			WHERE 
				`guilds`.`id` = '{$id}'";		
			
		$query = \Core\Main::$DB->query($query_str);
	
		if($query->numRows() != 1)
		{
			return false;
		}
		
		$fetch = $query->fetch();

		$this->_id = $fetch->id;
		$this->_name = $fetch->name;
		$this->_motd = $fetch->motd;
		$this->_image = $fetch->image;
		$this->_status = $fetch->status;
		$this->_formationTime = $fetch->formationTime;
		$this->_guildPoints = $fetch->guild_points;
		$this->_guildBetterPoints = $fetch->guild_better_points;
		
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
		{
			$this->_worldId = $fetch->world_id;
			$this->_ownerid = $fetch->ownerid;
			$this->_creationdate = $fetch->creationdata;
			$this->_balance = $fetch->balance;
		}
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
		{
			$this->_ownerid = $fetch->owner_id;
			$this->_creationdate = $fetch->creationdate;			
		}
		
		$this->Ranks = Guilds\Rank::RankList($this->_id);
		
		//loading guild invites
		$query = \Core\Main::$DB->query("SELECT `player_id`, `guild_id`, `date` FROM `guild_invites` WHERE `guild_id` = '{$this->_id}'");
		
		if($query->numRows() != 0)
		{
			for($i = 0; $i < $query->numRows(); ++$i)
			{
				$fetch = $query->fetch();
				
				$player = new \Framework\Player();
				$player->load($fetch->player_id);
				
				$this->Invites[] = array($player, $fetch->date);	
			}
		}		
		
		return true;
	}
	
	function LoadByRankId($rank_id)
	{
		$query = \Core\Main::$DB->query("SELECT `guild_id` FROM `guild_ranks` WHERE `id` = '{$rank_id}'");
		
		if($query->numRows() != 1)
		{
			return false;
		}
		
		$fetch = $query->fetch();
		
		$this->Load($fetch->guild_id);
		
		return true;
	}
	
	function LoadByName($name)
	{
		$query = \Core\Main::$DB->query("SELECT `id` FROM `guilds` WHERE `name` = '{$name}'");
		
		if($query->numRows() != 1)
		{
			return false;
		}	

		$fetch = $query->fetch();
		
		$this->Load($fetch->id);
		
		return true;		
	}
	
	function Save()
	{
		if($this->_id)
		{
			if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
				$query_str = "
				UPDATE 
					`guilds`
				SET 
					`name` = '{$this->_name}', 
					`owner_id` = '{$this->_ownerid}', 
					`creationdate` = '{$this->_creationdate}', 
					`motd` = '{$this->_motd}'
				WHERE 
					`id` = '{$this->_id}'";
			elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
				$query_str = "
				UPDATE 
					`guilds`
				SET 
					`world_id` = '{$this->_worldId}', 
					`name` = '{$this->_name}', 
					`ownerid` = '{$this->_ownerid}', 
					`creationdata` = '{$this->_creationdate}', 
					`motd` = '{$this->_motd}',
					`balance` = '{$this->balance}'
				WHERE 
					`id` = '{$this->_id}'";
				
						
			
			\Core\Main::$DB->query($query_str);
			
			\Core\Main::$DB->query("
				UPDATE 
					`".\Core\Tools::getSiteTable("guilds")."`
				SET 
					`image` = '{$this->_image}', 
					`status` = '{$this->_status}', 
					`formationTime` = '{$this->_formationTime}' 
				WHERE 
					`guild_id` = '{$this->_id}'
			");			
		}
		else
		{
			if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
				$query_str = "				
				INSERT INTO
					`guilds`
					(`name`, `owner_id`, `creationdate`, `motd`)
					values
					('{$this->_name}', '{$this->_ownerid}', '{$this->_creationdate}', '{$this->_motd}')";
			elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
				$query_str = "				
				INSERT INTO
					`guilds`
					(`world_id`, `name`, `ownerid`, `creationdata`, `motd`, `balance`, `checkdata`)
					values
					('{$this->_worldId}', '{$this->_name}', '{$this->_ownerid}', '{$this->_creationdate}', '{$this->_motd}', '0', '".(time() + (60 * 60 * 24 * 7))."')";		
				
			\Core\Main::$DB->query($query_str);
			
			$this->_id = \Core\Main::$DB->lastInsertId();
			
			\Core\Main::$DB->query("
			INSERT INTO
					`".\Core\Tools::getSiteTable("guilds")."`
					(`guild_id`,`image`, `status`, `formationTime`)
					values
					('{$this->_id}', '{$this->_image}', '{$this->_status}', '{$this->_formationTime}')");
			
			$this->Ranks = Guilds\Rank::RankList($this->_id);
		}
	}
	
	function LoadWars()
	{
		$_wars = Guilds\War::ListWarsByGuild($this->_id);
		
		if(!$_wars)
			return false;
			
		$this->Wars = $_wars;
		
		return true;	
	}
	
	function Delete()
	{
		$this->EreaseInvites();

		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "UPDATE `players` SET `rank_id` = '0' WHERE `rank_id` = '{$this->_id}'";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "DELETE FROM `guild_members` WHERE `player_id` = '{$this->_ownerid}'";		
		
		\Core\Main::$DB->query($query_str);
		
		foreach($this->Ranks as $rank)
		{
			$rank->Delete();
		}
		
		\Core\Main::$DB->query("DELETE FROM `".\Core\Tools::getSiteTable("guilds")."` WHERE `guild_id` = '{$this->_id}'");
		\Core\Main::$DB->query("DELETE FROM `guilds` WHERE `id` = '{$this->_id}'");
	}	
	
	function SearchWarsByStatus($status)
	{
		$wars = array();
		foreach($this->Wars as $guild_war)
		{			
			$guild_war instanceof Guilds\War;

			if($guild_war->GetStatus() == $status)
			{
				if($status == GUILD_WAR_WAITING && $guild_war->GetEndDate() < time())
					continue;
				
				$wars[] = $guild_war;
			}
		}
		
		return $wars;
	}
	
	function SearchRankByLevel($level)
	{
		foreach($this->Ranks as $rank)
		{
			if($rank->GetLevel() == $level)
				return $rank;
		}
		
		return false;
	}
	
	function SearchRankByName($name)
	{
		foreach($this->Ranks as $rank)
		{
			if($rank->GetName() == $name)
				return $rank;
		}
		
		return false;		
	}
	
	function SearchRankByLowest()
	{
		$lowestRank = $this->SearchRankByLevel(self::RANK_MEMBER);
		
		foreach($this->Ranks as $rank)
		{
			if($rank->GetLevel() < $lowestRank->GetLevel())
			{
				$lowestRank = $rank;	
			}
		}

		return $lowestRank;
	}
	
	function SearchMemberByName($player_name)
	{
		foreach($this->Ranks as $rank)
		{	
			foreach($rank->Members as $member)
			{								
				if($member->getName() == $player_name)
					return $member;
			}
		}	

		return false;		
	}
	
	function AddRankToDelete(Guild_Rank $rank)
	{
		$this->_trash_ranks[] = $rank;
	}
	
	function DeleteRanks()
	{
		if(count($this->_trash_ranks) == 0)
			return;
		
		foreach($this->_trash_ranks as $rank)
		{
			$rank->Delete();
		}
		
		$this->_trash_ranks = array();
		$this->Ranks = Guilds\Rank::RankList($this->_id);
	}
	
	function InvitesCount()
	{
		return count($this->Invites);
	}
	
	function EreaseInvites()
	{				
		foreach($this->Invites as $invite)
		{
			list($player, $invite_date) = $invite;
			$player->removeInvite();
		}
	}
	
	function MembersCount()
	{
		$members = 0;
		
		foreach($this->Ranks as $rank)
		{
			$members += $rank->MemberCount();
		}
		
		return $members;
	}
	
	function IsMember($player_id)
	{
		foreach($this->Ranks as $rank)
		{	
			foreach($rank->Members as $member)
			{			
				//echo $member->getName() . "<br>";
					
				if($member->getId() == $player_id)
					return true;
			}
		}	

		return false;
	}
	
	function IsMemberByAccount(Account $account)
	{
		foreach($this->Ranks as $rank)
		{	
			foreach($rank->Members as $member)
			{
				$char_list = $account->getCharacterList(Account::PLAYER_LIST_BY_ID);
				
				if(in_array($member->getId(), $char_list))
					return true;
			}
		}
		
		return false;
	}
	
	function OnWar()
	{
		$query_str = "";
		
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "SELECT `id` FROM `guild_wars` WHERE (`guild_id` = '{$this->_id}' OR `enemy_id` = '{$this->_id}') AND '".time()."' < `end` AND (`status` = '".\Framework\Guilds::WAR_STARTED."')";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT `id` FROM `guild_wars` WHERE (`guild_id` = '{$this->_id}' OR `opponent_id` = '{$this->_id}') AND '".time()."' < `end_date` AND (`status` = '".\Framework\Guilds::WAR_STARTED."' OR `status` = '".GUILD_WAR_WAITING."')";
		
			$query = \Core\Main::$DB->query($query_str);
		return (($query->numRows() != 0) ? true : false);
	}
	
	function IsAtWarAgainst($guild_id)
	{
		if(!$this->OnWar())
			return false;
			
		$this->LoadWars();
		
		foreach($this->Wars as $guild_war)
		{
			//check if guild war has already end
			if($guild_war->GetStatus() == self::WAR_DISABLED && $guild_war->GetReply() == -1)
				continue;
			
			if($guild_war->GetGuildId() == $guild_id)
				return true;
		}
		
		return false;
	}
	
	//setter & getters
	
	function SetId($id)
	{
		$this->_id = $id;
	}
	
	function SetWorldId($world_id)
	{
		$this->_worldId = $world_id;
	}
	
	function SetName($name)
	{
		$this->_name = $name;
	}
	
	function SetOwnerId($owner_id)
	{
		$this->_ownerid = $owner_id;
	}
	
	function SetCreationDate($creation_date)
	{
		$this->_creationdate = $creation_date;
	}
	
	function SetMotd($motd)
	{
		$this->_motd = $motd;
	}
	
	function SetBalance($balance)
	{
		$this->_balance = $balance;
	}
	
	function SetImage($image)
	{
		$this->_image = $image;
	}
	
	function SetStatus($status)
	{
		$this->_status = $status;
	}
	
	function SetFormationTime($formation_time)
	{
		$this->_formationTime = $formation_time;
	}
	
	function GetId()
	{
		return $this->_id;
	}
	
	function GetWorldId()
	{
		return $this->_worldId;
	}
	
	function GetName()
	{
		return $this->_name;
	}
	
	function GetOwnerId()
	{
		return $this->_ownerid;
	}

	function GetCreationDate()
	{
		return $this->_creationdate;
	}	
	
	function GetMotd()
	{
		return $this->_motd;
	}	
	
	function GetBalance()
	{
		return $this->_balance;
	}
	
	function GetImage()
	{
		return ($this->_image != "") ? $this->_image : self::DEFAULT_IMAGE;
	}	

	function GetStatus()
	{
		return $this->_status;
	}		

	function GetFormationTime()
	{
		return $this->_formationTime;
	}		

	function GetPoints()
	{
		return $this->_guildPoints;
	}		

	function GetBetterPoints()
	{
		return $this->_guildBetterPoints;
	}		
}

?>
