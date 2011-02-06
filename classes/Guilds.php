<?php
define("GUILD_STATUS_IN_FORMATION", 0);
define("GUILD_STATUS_FORMED", 1);

define("GUILD_RANK_LEADER", 		6);
define("GUILD_RANK_VICE", 			5);
define("GUILD_RANK_MEMBER", 		4);
define("GUILD_RANK_MEMBER_OPT_1", 	3);
define("GUILD_RANK_MEMBER_OPT_2", 	2);
define("GUILD_RANK_MEMBER_OPT_3", 	1);
define("GUILD_RANK_NO_MEMBER", 		0);

define("GUILD_WAR_STARTED", 	1);
define("GUILD_WAR_WAITING", 	0);
define("GUILD_WAR_DISABLED", 	-1);

define("GUILD_DEFAULT_IMAGE", "default_logo.gif");

include_once("Guild_War.php");
include_once("Guild_Rank.php");

class Guilds
{
	private $_id, $_name, $_ownerid, $_creationdate, $_motd, $_image, $_status, $_formationTime, $_guildPoints, $_guildBetterPoints;
	public $Ranks = array(), $Invites = array(), $Wars = array();
	private $_trash_ranks = array();
	
	function Guilds()
	{
	}

	/**
	 * Analisa todos personagens membros de uma guild de uma determinada conta em busca do guild level mais alto.
	 * @param Account account <p>Conta a ser verificada.</p>
	 * @param int guild_id <p>Id da guild que os personagens devem pertencer.</p>
	 * @return guild level.
	 */		
	static function GetAccountLevel(Account $account, $guild_id)
	{
		$char_list = $account->getCharacterList(ACCOUNT_CHARACTERLIST_BY_ID);
		$level = 0;
		
		foreach($char_list as $player_id)
		{
			$character = new Character();
			$character->load($player_id);
			$character->LoadGuild();
			
			if($character->GetGuildId() == $guild_id && $character->GetGuildLevel() > $level)
				$level = $character->GetGuildLevel();
		}
		
		return $level;
	}
	
	static function ActivedGuildsList()
	{
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "SELECT `id` FROM `guilds`, `".Tools::getSiteTable("guilds")."` WHERE `status` = '".GUILD_STATUS_FORMED."' ORDER BY `creationdate`";
		elseif(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "SELECT `id` FROM `guilds`, `".Tools::getSiteTable("guilds")."` WHERE `status` = '".GUILD_STATUS_FORMED."' ORDER BY `creationdata`";
			
		$query = Core::$DB->query($query_str);
		
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
	
	static function FormingGuildsList()
	{
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "SELECT `id` FROM `guilds`, `".Tools::getSiteTable("guilds")."` WHERE `id` = `guild_id` AND `status` = '".GUILD_STATUS_IN_FORMATION."' ORDER BY `creationdate`";
		elseif(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "SELECT `id` FROM `guilds`, `".Tools::getSiteTable("guilds")."` WHERE `id` = `guild_id` AND `status` = '".GUILD_STATUS_IN_FORMATION."' ORDER BY `creationdata`";
			
		$query = Core::$DB->query($query_str);		

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
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
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
				`".Tools::getSiteTable("guilds")."` as `guilds_site`
			ON
				`guilds`.`id` = `guilds_site`.`guild_id`
			WHERE 
				`guilds`.`id` = '{$id}'";
		elseif(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "
			SELECT 
				`guilds`.`id`, 
				`guilds`.`name`, 
				`guilds`.`ownerid`, 
				`guilds`.`creationdata`, 
				`guilds`.`motd`, 
				`guilds_site`.`image`, 
				`guilds_site`.`status`, 
				`guilds_site`.`guilds`.`formationTime`, 
				`guilds_site`.`guild_points`, 
				`guilds_site`.`guild_better_points` 
			FROM 
				`guilds` 
			LEFT JOIN 
				`".Tools::getSiteTable("guilds")."` as `guilds_site`
			ON
				`guilds`.`id` = `guilds_site`.`guild_id`
			WHERE 
				`guilds`.`id` = '{$id}'";		
			
		$query = Core::$DB->query($query_str);
	
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
		
		if(SERVER_DISTRO == DISTRO_TFS)
		{
			$this->_ownerid = $fetch->ownerid;
			$this->_creationdate = $fetch->creationdata;
		}
		elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
		{
			$this->_ownerid = $fetch->owner_id;
			$this->_creationdate = $fetch->creationdate;			
		}
		
		$this->Ranks = Guild_Rank::RankList($this->_id);
		
		//loading guild invites
		$query = Core::$DB->query("SELECT `player_id`, `guild_id`, `date` FROM `guild_invites` WHERE `guild_id` = '{$this->_id}'");
		
		if($query->numRows() != 0)
		{
			for($i = 0; $i < $query->numRows(); ++$i)
			{
				$fetch = $query->fetch();
				
				$character = new Character();
				$character->load($fetch->player_id);
				
				$this->Invites[] = array($character, $fetch->date);	
			}
		}		
		
		return true;
	}
	
	function LoadByRankId($rank_id)
	{
		$query = Core::$DB->query("SELECT `guild_id` FROM `guild_ranks` WHERE `id` = '{$rank_id}'");
		
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
		$query = Core::$DB->query("SELECT `id` FROM `guilds` WHERE `name` = '{$name}'");
		
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
			if(SERVER_DISTRO == DISTRO_OPENTIBIA)
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
			elseif(SERVER_DISTRO == DISTRO_TFS)
				$query_str = "
				UPDATE 
					`guilds`
				SET 
					`name` = '{$this->_name}', 
					`ownerid` = '{$this->_ownerid}', 
					`creationdata` = '{$this->_creationdate}', 
					`motd` = '{$this->_motd}'
				WHERE 
					`id` = '{$this->_id}'";
				
						
			
			Core::$DB->query($query_str);
			
			Core::$DB->query("
				UPDATE 
					`".Tools::getSiteTable("guilds")."`
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
			if(SERVER_DISTRO == DISTRO_OPENTIBIA)
				$query_str = "				
				INSERT INTO
					`guilds`
					(`name`, `owner_id`, `creationdate`, `motd`)
					values
					('{$this->_name}', '{$this->_ownerid}', '{$this->_creationdate}', '{$this->_motd}', '{$this->_image}', '{$this->_status}', '{$this->_formationTime}')";
			elseif(SERVER_DISTRO == DISTRO_TFS)
				$query_str = "				
				INSERT INTO
					`guilds`
					(`name`, `ownerid`, `creationdata`, `motd`)
					values
					('{$this->_name}', '{$this->_ownerid}', '{$this->_creationdate}', '{$this->_motd}', '{$this->_image}', '{$this->_status}', '{$this->_formationTime}')";		
				
			Core::$DB->query($query_str);
			
			$this->_id = Core::$DB->lastInsertId();
			
			Core::$DB->query("
			INSERT INTO
					`".Tools::getSiteTable("guilds")."`
					(`guild_id`,`image`, `status`, `formationTime`)
					values
					('{$this->_id}', '{$this->_image}', '{$this->_status}', '{$this->_formationTime}')");
			
			$this->Ranks = Guild_Rank::RankList($this->_id);
		}
	}
	
	function LoadWars()
	{
		$_wars = Guild_War::ListWarsByGuild($this->_id);
		
		if(!$_wars)
			return false;
			
		$this->Wars = $_wars;
		
		return true;	
	}
	
	function Delete()
	{
		$this->EreaseInvites();

		Core::$DB->query("DELETE FROM `guild_members` WHERE `player_id` = '{$this->_ownerid}'");
		
		foreach($this->Ranks as $rank)
		{
			$rank->Delete();
		}
		
		Core::$DB->query("DELETE FROM `guilds` WHERE `id` = '{$this->_id}'");
	}	
	
	function SearchWarsByStatus($status)
	{
		$wars = array();
		foreach($this->Wars as $guild_war)
		{			
			$guild_war instanceof Guild_War;

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
		$lowestRank = $this->SearchRankByLevel(GUILD_RANK_MEMBER);
		
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
		$this->Ranks = Guild_Rank::RankList($this->_id);
	}
	
	function InvitesCount()
	{
		return count($this->Invites);
	}
	
	function EreaseInvites()
	{
		$invites = 
		
		list($character, $invite_date) = $this->Invites;
		
		foreach($this->Invites as $invite)
		{
			list($character, $invite_date) = $invite;
			$character->removeInvite();
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
				$char_list = $account->getCharacterList(ACCOUNT_CHARACTERLIST_BY_ID);
				
				if(in_array($member->getId(), $char_list))
					return true;
			}
		}
		
		return false;
	}
	
	function OnWar()
	{
		$query = Core::$DB->query("SELECT `id` FROM `guild_wars` WHERE (`guild_id` = '{$this->_id}' OR `opponent_id` = '{$this->_id}') AND '".time()."' < `end_date` AND (`status` = '".GUILD_WAR_STARTED."' OR `status` = '".GUILD_WAR_WAITING."')");
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
			if($guild_war->GetStatus() == GUILD_WAR_DISABLED && $guild_war->GetReply() == -1)
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
	
	function GetImage()
	{
		return $this->_image;
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