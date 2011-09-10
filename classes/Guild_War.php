<?php
class Guild_War
{
	private $_id, $_guildid, $_opponentid, $_fraglimit, $_declarationdate, $_enddays, $_enddate, $_guildfee, $_opponentfee, $_guildfrags, $_opponentfrags, $_comment, $_status = -1, $_reply;
	
	/*
	 * Static Functions
	 */	
	
	static function ListWarsByGuild($guild_id)
	{	
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "SELECT `id` FROM `guild_wars` WHERE `guild_id` = '{$guild_id}' OR `opponent_id` = '{$guild_id}'";
		elseif(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "SELECT `id` FROM `guild_wars` WHERE `guild_id` = '{$guild_id}' OR `enemy_id` = '{$guild_id}'";		
		
		$query = Core::$DB->query($query_str);
		
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
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "SELECT `id` FROM `guild_wars` WHERE `status` = '".GUILD_WAR_STARTED."' AND `end_date` >= '".time()."' ORDER BY `declaration_date`";
		elseif(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "SELECT `id` FROM `guild_wars` WHERE `status` = '".GUILD_WAR_STARTED."' AND `end` >= '".time()."' ORDER BY `begin`";
					
		$query = Core::$DB->query($query_str);
		
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
	
	static function ListEndedWars()
	{
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "
			SELECT 
				`id` 
			FROM 
				`guild_wars` 
			WHERE 
				`status` = '".GUILD_WAR_DISABLED."' OR 
				(`end_date` <= '".time()."' OR 
					(`frag_limit` > 0 AND 
						(
							`guild_frags` >= `frag_limit` OR 
							`opponent_frags` >= `frag_limit`
						)
					)
				) 
			ORDER BY 
				`declaration_date`";
		elseif(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "
			SELECT 
				`id` 
			FROM 
				`guild_wars` 
			WHERE 
				`status` = '".GUILD_WAR_DISABLED."' OR 
				(`end` <= '".time()."' OR 
					(`frags` > 0 AND 
						(
							`guild_kills` >= `frags` OR 
							`enemy_kills` >= `frags`
						)
					)
				) 
			ORDER BY 
				`begin`";			
		
		$query = Core::$DB->query($query_str);
		
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
	
	static function ListNegotiationWars()
	{
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "
			SELECT 
				`guild_wars`.`id` 
			FROM 
				`guild_wars`
			LEFT JOIN 
				`".Tools::getSiteTable("guild_wars")."` as `guild_wars_site`
			ON
				`guild_wars`.`id` = `guild_wars_site`.`war_id` 
			WHERE 
				(
					(`guild_wars`.`status` = '".GUILD_WAR_DISABLED."' AND `guild_wars_site`.`reply` >= '0') 
					OR (`guild_wars`.`status` = '".GUILD_WAR_WAITING."' AND `guild_wars_site`.`reply` = '-1')
				) 
				AND `guild_wars`.`end_date` > UNIX_TIMESTAMP()
			ORDER BY 
				`guild_wars`.`declaration_date`";
		elseif(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "
			SELECT 
				`guild_wars`.`id` 
			FROM 
				`guild_wars`
			LEFT JOIN 
				`".Tools::getSiteTable("guild_wars")."` as `guild_wars_site`
			ON
				`guild_wars`.`id` = `guild_wars_site`.`war_id` 
			WHERE 
				(
					(
						`guild_wars`.`status` = '".GUILD_WAR_DISABLED."' 
						AND `guild_wars_site`.`reply` >= '0'
					) 
				) 
				AND `guild_wars`.`end` > UNIX_TIMESTAMP()
			ORDER BY 
				`guild_wars`.`begin`";		
					
		$query = Core::$DB->query($query_str);		
		
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
		if(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "
			SELECT 
				`guild_wars`.`id`, 
				`guild_wars`.`guild_id`, 
				`guild_wars`.`enemy_id`, 
				`guild_wars`.`frags`, 
				`guild_wars`.`begin`, 
				`guild_wars`.`end`, 
				`guild_wars`.`payment`, 
				`guild_wars`.`guild_kills`, 
				`guild_wars`.`enemy_kills`, 
				`guild_wars_site`.`comment`, 
				`guild_wars`.`status`, 
				`guild_wars_site`.`reply` 
			FROM 
				`guild_wars`
			LEFT JOIN
				`".Tools::getSiteTable("guild_wars")."` as `guild_wars_site` 
			ON
				`guild_wars_site`.`war_id` = `guild_wars`.`id`
			WHERE 
				`guild_wars`.`id` = '{$war_id}'";
		elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "
			SELECT 
				`guild_wars`.`id`, 
				`guild_wars`.`guild_id`, 
				`guild_wars`.`opponent_id`, 
				`guild_wars`.`frag_limit`, 
				`guild_wars`.`declaration_date`, 
				`guild_wars`.`end_date`, 
				`guild_wars`.`guild_fee`, 
				`guild_wars`.`opponent_fee`, 
				`guild_wars`.`guild_frags`, 
				`guild_wars`.`opponent_frags`, 
				`guild_wars_site`.`comment`, 
				`guild_wars`.`status`, 
				`guild_wars_site`.`reply` 
			FROM 
				`guild_wars`
			LEFT JOIN
				`".Tools::getSiteTable("guild_wars")."` as `guild_wars_site` 
			ON
				`guild_wars_site`.`war_id` = `guild_wars`.`id`
			WHERE 
				`guild_wars`.`id` = '{$war_id}'";		
		
		$query = Core::$DB->query($query_str);
	
		if($query->numRows() == 0)
		{			
			return false;
		}
		
		$fetch = $query->fetch();
		
		$this->_id = $fetch->id;
		$this->_guildid = $fetch->guild_id;
		$this->_comment = $fetch->comment;
		$this->_status = $fetch->status;
		$this->_reply = $fetch->reply;
		
		if(SERVER_DISTRO == DISTRO_TFS)
		{
			$this->_opponentid = $fetch->enemy_id;
			$this->_fraglimit = $fetch->frags;
			$this->_declarationdate = $fetch->begin;
			$this->_enddate = $fetch->end;
			$this->_guildfee = $fetch->payment;
			$this->_guildfrags = $fetch->guild_kills;
			$this->_opponentfrags = $fetch->enemy_kills;				
		}
		elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
		{
			$this->_opponentid = $fetch->opponent_id;
			$this->_fraglimit = $fetch->frag_limit;
			$this->_declarationdate = $fetch->declaration_date;
			$this->_enddate = $fetch->end_date;
			$this->_guildfee = $fetch->guild_fee;
			$this->_opponentfee = $fetch->opponent_fee;
			$this->_guildfrags = $fetch->guild_frags;
			$this->_opponentfrags = $fetch->opponent_frags;			
		}
		
		return true;
	}
	
	function Save()
	{
		if($this->_id)
		{
			if(SERVER_DISTRO == DISTRO_TFS)
				$query_str = "
				UPDATE 
					`guild_wars` 
				SET 
					`guild_id` = '{$this->_guildid}', 
					`enemy_id` = '{$this->_opponentid}', 
					`frags` = '{$this->_fraglimit}',
					`begin` = '{$this->_declarationdate}',
					`end` = '{$this->_enddate}',
					`payment` = '{$this->_guildfee}',
					`guild_kills` = '{$this->_guildfrags}',
					`enemy_kills` = '{$this->_opponentfrags}',
					`status` = '{$this->_status}'
				WHERE 
					`id` = '{$this->_id}'";
			elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
				$query_str = "
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
					`status` = '{$this->_status}'
				WHERE 
					`id` = '{$this->_id}'";
			
			Core::$DB->query($query_str);

			Core::$DB->query("				
				UPDATE 
					`".Tools::getSiteTable("guild_wars")."` 
				SET 
					`comment` = '{$this->_comment}', 
					`reply` = '{$this->_reply}'
				WHERE 
					`war_id` = '{$this->_id}'");
		}
		else
		{
			if(SERVER_DISTRO == DISTRO_TFS)
				$query_str = "				
				INSERT INTO
					`guild_wars`
					(`guild_id`, `enemy_id`, `frags`, `begin`, `end`, `payment`, `guild_kills`, `enemy_kills`, `status`)
					values
					('{$this->_guildid}', '{$this->_opponentid}', '{$this->_fraglimit}', '{$this->_declarationdate}', '{$this->_enddate}', '{$this->_guildfee}', '{$this->_guildfrags}', '{$this->_opponentfrags}', '{$this->_status}')";
			elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
				$query_str = "				
				INSERT INTO
					`guild_wars`
					(`guild_id`, `opponent_id`, `frag_limit`, `declaration_date`, `end_date`, `guild_fee`, `opponent_fee`, `guild_frags`, `opponent_frags`, `status`,)
					values
					('{$this->_guildid}', '{$this->_opponentid}', '{$this->_fraglimit}', '{$this->_declarationdate}', '{$this->_enddate}', '{$this->_guildfee}', '{$this->_opponentfee}', '{$this->_guildfrags}', '{$this->_opponentfrags}', '{$this->_comment}', '{$this->_status}', '{$this->_reply}')";			
				
			Core::$DB->query($query_str);		

			$this->_id = Core::$DB->lastInsertId();		
			
			Core::$DB->query("
				INSERT INTO
					`".Tools::getSiteTable("guild_wars")."`
					(`war_id`, `reply`, `comment`)
					values
					('{$this->_id}', '{$this->_reply}', '{$this->_comment}')");	
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
?>