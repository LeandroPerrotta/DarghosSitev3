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

class Guild_War
{
	private $_id, $_guildid, $_opponentid, $_fraglimit, $_declarationdate, $_enddays, $_enddate, $_guildfee, $_opponentfee, $_guildfrags, $_opponentfrags, $_comment, $_status = -1, $_reply;
	
	/*
	 * Static Functions
	 */	
	
	static function ListWarsByGuild($guild_id)
	{	
		$query = Core::$DB->query("SELECT `id` FROM `guild_wars` WHERE `guild_id` = '{$guild_id}' OR `opponent_id` = '{$guild_id}'");
		
		if($query->numRows() == 0)
			return false;
		
		$_wars = array();
		
		for($i = 0; $i < $query->numRows(); ++$i)
		{
			$fetch = $query->fetch();
			
			$guild_war = new Guild_War();
			$guild_war->Load($fetch->id);
			
			$_wars[] = $guild_war;
		}
		
		return $_wars;
	}
	
	static function ListStartedWars()
	{
		$query = Core::$DB->query("SELECT `id` FROM `guild_wars` WHERE `status` = '".GUILD_WAR_STARTED."' ORDER BY `declaration_date`");
		
		if($query->numRows() == 0)
		{
			return false;
		}
		
		$warList = array();
		
		for($i = 0; $i < $query->numRows(); ++$i)
		{	
			$fetch = $query->fetch();
			
			$guild_war = new Guild_War();
			$guild_war->Load($fetch->id);
			
			$warList[] = $guild_war;
		}			
		
		return $warList;
	}	
	
	/*
	 * Constructor and Functions
	 */	
	
	function Guild_War()
	{
		
	}
	
	function Load($war_id)
	{
		$query = Core::$DB->query("SELECT `id`, `guild_id`, `opponent_id`, `frag_limit`, `declaration_date`, `end_date`, `guild_fee`, `opponent_fee`, `guild_frags`, `opponent_frags`, `comment`, `status`, `reply` FROM `guild_wars` WHERE `id` = '{$war_id}'");
	
		if($query->numRows() == 0)
		{			
			return false;
		}
		
		$fetch = $query->fetch();
		
		$this->_id = $fetch->id;
		$this->_guildid = $fetch->guild_id;
		$this->_opponentid = $fetch->opponent_id;
		$this->_fraglimit = $fetch->frag_limit;
		$this->_declarationdate = $fetch->declaration_date;
		$this->_enddate = $fetch->end_date;
		$this->_guildfee = $fetch->guild_fee;
		$this->_opponentfee = $fetch->opponent_fee;
		$this->_guildfrags = $fetch->guild_frags;
		$this->_opponentfrags = $fetch->opponent_frags;
		$this->_comment = $fetch->comment;
		$this->_status = $fetch->status;
		$this->_reply = $fetch->reply;
		
		return true;
	}
	
	function Save()
	{
		if($this->_id)
		{
			Core::$DB->query("
				UPDATE 
					`guild_wars` 
				SET 
					`guild_id` = '{$this->_guildid}', 
					`opponent_id` = '{$this->_opponentid}', 
					`frag_limit` = '{$this->_fraglimit}',
					`declaration_date` = '{$this->_declarationdate}',
					`end_date` = '{$this->_enddate}',
					`guild_fee` = '{$this->_guildfee}',
					`opponent_fee` = '{$this->_opponentfee}',
					`guild_frags` = '{$this->_guildfrags}',
					`opponent_frags` = '{$this->_opponentfrags}',
					`comment` = '{$this->_comment}',
					`status` = '{$this->_status}',
					`reply` = '{$this->_reply}'
				WHERE 
					`id` = '{$this->_id}'
			");			
		}
		else
		{
			Core::$DB->query("
				INSERT INTO
					`guild_wars`
					(`guild_id`, `opponent_id`, `frag_limit`, `declaration_date`, `end_date`, `guild_fee`, `opponent_fee`, `guild_frags`, `opponent_frags`, `comment`, `status`, `reply`)
					values
					('{$this->_guildid}', '{$this->_opponentid}', '{$this->_fraglimit}', '{$this->_declarationdate}', '{$this->_enddate}', '{$this->_guildfee}', '{$this->_opponentfee}', '{$this->_guildfrags}', '{$this->_opponentfrags}', '{$this->_comment}', '{$this->_status}', '{$this->_reply}')
			");		

			$this->_id = Core::$DB->lastInsertId();		
		}
	}
	
	/*
	 * Getters and Setters
	 */	
	
	function SetId($id)
	{
		$this->_id = $id;
	}
	
	function SetGuildId($guild_id)
	{
		$this->_guildid = $guild_id;
	}
	
	function SetOpponentId($guild_id)
	{
		$this->_opponentid = $guild_id;
	}
	
	function SetFragLimit($frag_limit)
	{
		$this->_fraglimit = $frag_limit;
	}
	
	function SetDeclarationDate($declaration_date)
	{
		$this->_declarationdate = $declaration_date;
	}
	
	function SetEndDate($end_date)
	{
		$this->_enddate = $end_date;
	}
	
	function SetGuildFee($fee)
	{
		$this->_guildfee = $fee;
	}
	
	function SetOpponentFee($fee)
	{
		$this->_opponentfee = $fee;
	}
	
	function SetGuildFrags($frags)
	{
		$this->_guildfrags = $frags;
	}
	
	function SetOpponentFrags($frags)
	{
		$this->_opponentfrags = $frags;
	}
	
	function SetComment($comment)
	{
		$this->_comment = $comment;
	}
	
	function SetStatus($status)
	{
		$this->_status = $status;
	}
	
	function SetReply($reply)
	{
		$this->_reply = $reply;
	}
	
	function GetId()
	{
		return $this->_id;
	}
	
	function GetGuildId()
	{
		return $this->_guildid;
	}
	
	function GetOpponentId()
	{
		return $this->_opponentid;
	}
	
	function GetFragLimit()
	{
		return $this->_fraglimit;
	}
	
	function GetDeclarationDate()
	{
		return $this->_declarationdate;
	}
	
	function GetEndDate()
	{
		return $this->_enddate;
	}
	
	function GetGuildFee()
	{
		return $this->_guildfee;
	}
	
	function GetOpponentFee()
	{
		return $this->_opponentfee;
	}
	
	function GetGuildFrags()
	{
		return $this->_guildfrags;
	}
	
	function GetOpponentFrags()
	{
		return $this->_opponentfrags;
	}
	
	function GetComment()
	{
		return $this->_comment;
	}
	
	function GetStatus()
	{
		return $this->_status;
	}
	
	function GetReply()
	{
		return $this->_reply;
	}	
}

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
		
		$query = Core::$DB->query("SELECT `player_id` FROM `guild_members` WHERE `rank_id` = '{$this->_id}'");
		
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
		$query = Core::$DB->query("SELECT `player_id` FROM `guild_members` WHERE `rank_id` = '{$this->_id}'");
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
		$query = Core::$DB->query("SELECT `id` FROM `guilds` WHERE `status` = '".GUILD_STATUS_FORMED."' ORDER BY `creationdate`");
		
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
		$query = Core::$DB->query("SELECT `id` FROM `guilds` WHERE `status` = '".GUILD_STATUS_IN_FORMATION."' ORDER BY `creationdate`");

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
		$query = Core::$DB->query("SELECT `id`, `name`, `owner_id`, `creationdate`, `motd`, `image`, `status`, `formationTime`, `guild_points`, `guild_better_points` FROM `guilds` WHERE `id` = '{$id}'");
	
		if($query->numRows() != 1)
		{
			return false;
		}
		
		$fetch = $query->fetch();

		$this->_id = $fetch->id;
		$this->_name = $fetch->name;
		$this->_ownerid = $fetch->owner_id;
		$this->_creationdate = $fetch->creationdate;
		$this->_motd = $fetch->motd;
		$this->_image = $fetch->image;
		$this->_status = $fetch->status;
		$this->_formationTime = $fetch->formationTime;
		$this->_guildPoints = $fetch->guild_points;
		$this->_guildBetterPoints = $fetch->guild_better_points;
		
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
			Core::$DB->query("
				UPDATE 
					`guilds` 
				SET 
					`name` = '{$this->_name}', 
					`owner_id` = '{$this->_ownerid}', 
					`creationdate` = '{$this->_creationdate}', 
					`motd` = '{$this->_motd}', 
					`image` = '{$this->_image}', 
					`status` = '{$this->_status}', 
					`formationTime` = '{$this->_formationTime}' 
				WHERE 
					`id` = '{$this->_id}'
			");
		}
		else
		{
			Core::$DB->query("
				INSERT INTO
					`guilds`
					(`name`, `owner_id`, `creationdate`, `motd`, `image`, `status`, `formationTime`)
					values
					('{$this->_name}', '{$this->_ownerid}', '{$this->_creationdate}', '{$this->_motd}', '{$this->_image}', '{$this->_status}', '{$this->_formationTime}')
			");
			
			$this->_id = Core::$DB->lastInsertId();
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
			if($guild_war->GetStatus() == $status)
			{
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
		$query = Core::$DB->query("SELECT `id` FROM `guild_wars` WHERE `guild_id` = '{$this->_id}' AND `status` = '".GUILD_WAR_STARTED."'");
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