<?
namespace Framework;
use \Core\Configs as g_Configs;
use \Core\Consts;
class Player
{
	const
		PH_TYPE_LOG = 1
		, PH_TYPE_ACHIEVEMENT = 2
		;
		
	const
		PH_LOG_BATTLEGROUND_WIN = 1
		, PH_LOG_BATTLEGROUND_LOST = 2
		, PH_LOG_BATTLEGROUND_DRAW = 3
		, PH_LOG_DUNGEON_ARIADNE_TROLLS_ATTEMPS = 4
		, PH_LOG_DUNGEON_ARIADNE_TROLLS_COMPLETED = 5
		, PH_LOG_BATTLEGROUND_FLAGS_CAPTURED = 6
		, PH_LOG_BATTLEGROUND_FLAGS_RETURNED = 7
		, PH_LOG_BATTLEGROUND_FLAGS_KILLED = 8
		, PH_LOG_BATTLEGROUND_FLAGS_DROPED = 9
		, PH_LOG_BATTLEGROUND_PERFECT_MATCHES = 10
		;
		
	const
		PH_ACHIEV_BATTLEGROUND_RANK_VETERAN = 1
		, PH_ACHIEV_BATTLEGROUND_RANK_LEGEND = 2
		, PH_ACHIEV_BATTLEGROUND_ISANE_KILLER = 3
		, PH_ACHIEV_BATTLEGROUND_PERFECT = 4
		, PH_ACHIEV_BATTLEGROUND_RANK_BRAVE = 5
		, PH_ACHIEV_BATTLEGROUND_FLAG_CATCHER = 6
		, PH_ACHIEV_BATTLEGROUND_FLAG_CAPTURED = 7
		, PH_ACHIEV_BATTLEGROUND_MANY_FLAGS_RETUREND = 8
		, PH_ACHIEV_BATTLEGROUND_FLAG_KILLER = 9
		, PH_ACHIEV_BATTLEGROUND_MANY_FLAG_CAPTURED = 10
		, PH_ACHIEV_BATTLEGROUND_SAVE_THE_DAY = 11
		, PH_ACHIEV_BATTLEGROUND_EPIC_MATCH = 12
		, PH_ACHIEV_BATTLEGROUND_PERFECT_COLLECTOR = 13
		
		, PH_ACHIEV_DUNGEON_ARIADNE_TROLLS_GOT_ALL_TOTEMS = 1000
		, PH_ACHIEV_DUNGEON_ARIADNE_TROLLS_GOT_GHAZRAN_TONGUE = 1001
		, PH_ACHIEV_DUNGEON_ARIADNE_TROLLS_COMPLETE_IN_ONLY_ONE_ATTEMP = 1002
		, PH_ACHIEV_DUNGEON_ARIADNE_TROLLS_COMPLETE_WITHOUT_ANYONE_DIE = 1003
		
		, PH_ACHIEV_MISC_GOT_LEVEL_100 = 2000
		, PH_ACHIEV_MISC_GOT_LEVEL_200 = 2001
		, PH_ACHIEV_MISC_GOT_LEVEL_300 = 2002
		, PH_ACHIEV_MISC_GOT_LEVEL_400 = 2003
		, PH_ACHIEV_MISC_GOT_LEVEL_500 = 2004
		;
		
	static function listByBestRating()
	{
		$query = \Core\Main::$DB->query("
				SELECT
				`name`,
				`battleground_rating`
				FROM
				`players`
				ORDER BY
				`battleground_rating`
				DESC
				LIMIT 5");
	
		return $query;
	}

	static function isAtSameWorld(Player $player, $comp_player)
	{
		if(!is_array($comp_player))
		{
			return $player->getWorldId() == $comp_player->getWorldId();
		}
		
		foreach($comp_player as $p)
		{
			$p instanceof Player;
			
			if($p->getWorldId() != $player->getWorldId())
				return false;
		}
		
		return true;
	}
		
	private 
		$db
		,$temp_data
		,$data = array()
		,$skills = array()
		,$guild = array() /* deprecated? */
		,$_group
	;
	
	private $site_data = array(
		"visible" => 1
	);
	
	private $_loadGuild = false;
	
	private $_guild_name, /* readonly */
			$_guild_id, /* readonly */
			$_guild_rank, /* readonly */
			$_guild_rank_id, 
			$_guild_nick, 
			$_guild_join_in, 
			$_guild_level; /* readonly */
	
	//instances of guild and rank		
	public $Rank, $Guild;
	
	function __construct()
	{
		$this->db = \Core\Main::$DB;
	}	

	function save()
	{		
		
		if(isset($this->data['id']))
		{			
			$hasUpdate = false;
			$temp = array();
			foreach($this->data as $field => $value)
			{			
				if($this->temp_data[$field] == $value)
					continue;
							
				$temp[] = "`{$field}` = '{$value}'";
			}
			
			
			if(count($temp) > 0)
			{
				$update = implode(", ", $temp);
				$this->db->query("UPDATE players SET {$update} WHERE id = '".$this->data['id']."'");
			}
			
			$query_str = "
			UPDATE 
				`".\Core\Tools::getSiteTable("players")."` 
			SET 
				`creation` = '{$this->site_data["creation"]}',
				`comment` = '" . \Core\Main::$DB->escapeString($this->site_data["comment"]) . "',
				`visible` = '{$this->site_data["visible"]}'";
			
			if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
				$query_str .= ",`guildjoin` = '{$this->_guild_join_in}'";
				
				$query_str .= "
			WHERE 
				`player_id` = '{$this->data['id']}'";

			$this->db->query($query_str);
			
			if($this->_loadGuild)
			{
				if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
				{
					//first, erease all guild member information from player
					\Core\Main::$DB->query("DELETE FROM `guild_members` WHERE `player_id` = '{$this->data['id']}'");			
					
					//player have rank id, saving guild member information
					if($this->_guild_rank_id)
					{				
						//add a new guild information
						\Core\Main::$DB->query("
							INSERT INTO `guild_members`
								(`player_id`, `rank_id`, `nick`, `join_in`) 
								values
								('{$this->data["id"]}', '{$this->_guild_rank_id}', '{$this->_guild_nick}', '{$this->_guild_join_in}')
						");
					}
				}
				elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
				{
					$this->db->query("UPDATE `players` SET `rank_id` = '{$this->_guild_rank_id}', `guildnick` = '".$this->_guild_nick."' WHERE `id` = '{$this->data["id"]}'");			
				}
			}
		}
		//create new character!!
		else
		{
			$insert_fields = "";
			$insert_values = "";			
			
			foreach($this->data as $field => $value)
			{
				$i++;
				
				if($i == count($this->data))
				{
					$insert_fields .= "".$field."";
					$insert_values .= "'".$value."'";
				}
				else
				{
					$insert_fields .= "".$field.", ";
					$insert_values .= "'".$value."', ";
				}			
			}

			$this->db->query("INSERT INTO players ($insert_fields) values($insert_values)");			
			$this->data['id'] = $this->db->lastInsertId();
			
			$this->db->query("
			INSERT INTO 
				`".\Core\Tools::getSiteTable("players")."` 
				(
					`player_id`,
					`creation`,
					`comment`,
					`visible`		
				)
			VALUES (
				'{$this->data['id']}',
				'{$this->site_data["creation"]}',
				'{$this->site_data["comment"]}',
				'{$this->site_data["visible"]}'
			)");
		}
	}	
	
	function load($player_id)
	{
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT id, name, group_id, account_id, level, vocation, maglevel, health, healthmax, experience, lookbody, lookfeet, lookhead, looklegs, looktype, lookaddons, maglevel, mana, manamax, manaspent, soul, town_id, posx, posy, posz, conditions, cap, sex, lastlogin, lastip, save, skull_type, lastlogout, balance, stamina, direction, loss_experience, loss_mana, loss_skills, loss_items, online, skull_time FROM players WHERE id = '".$player_id."'";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
		{
			$query_str = "
			SELECT 
				id, name, world_id, group_id, account_id, level, vocation, maglevel, health, healthmax
				, experience, lookbody, lookfeet, lookhead, looklegs, looktype, lookaddons
				, maglevel, mana, manamax, manaspent, soul, town_id, posx, posy, posz, conditions
				, cap, sex, lastlogin, lastip, save, skull, skulltime, lastlogout, balance, stamina
				, direction, loss_experience, loss_mana, loss_skills, loss_items, deleted, description
				, online, promotion, battleground_rating";
		
			if(g_Configs::Get(g_Configs::eConf()->ENABLE_PVP_SWITCH))
				$query_str .= ", pvpEnabled";
			
			$query_str .= " FROM players WHERE id = '{$player_id}' AND deleted = 0";
		}
			
		$query = $this->db->query($query_str);		
		
		if($query->numRows() != 1)
		{
			return false;
		}	
			
		$this->data = $query->fetchAssocArray();
		
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
		    $this->data["world_id"] = 0; //hack
		
		$this->temp_data = $this->data; //usaremos para posterioremente identificar valores modificados
		
		$query = $this->db->query("SELECT `creation`, `visible`, `comment` FROM `".\Core\Tools::getSiteTable("players")."` WHERE `player_id` = '{$this->data["id"]}'");
		
		if($query->numRows() > 0)
		{
			$this->site_data = $query->fetchAssocArray();
		}
		
		return true;			
	}
	
	function LoadGuild()
	{
		$this->_loadGuild = true;
		
		//precisamos implementar o guild system do TFS...
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "
						SELECT 
							`p`.`rank_id`, 
							`p`.`guildnick`, 
							`players_site`.`guildjoin` 
						FROM 
							`players` as `p`
						LEFT JOIN 
							`".\Core\Tools::getSiteTable("players")."` as `players_site` 
						ON 
							`p`.`id` = `players_site`.`player_id` 
						WHERE `p`.`id` = '{$this->data["id"]}'";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT `rank_id`, `nick`, `join_in` FROM `guild_members` WHERE `player_id` = '{$this->data["id"]}'";
		
		//loading guild infos of player
		$query = \Core\Main::$DB->query($query_str);
		
		if($query->numRows() == 1)
		{			
			$fetch = $query->fetch();
			
			if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			{
				$this->_guild_nick = $fetch->guildnick;
				$this->_guild_join_in = $fetch->guildjoin;
			}
			elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			{
				$this->_guild_nick = $fetch->nick;
				$this->_guild_join_in = $fetch->join_in;
			}
			
			//loading guild rank of member
			$rank = new Guilds\Rank();
			if(!$rank->load($fetch->rank_id)) 
			{
				return false;		
			}
			
			$this->Rank = $rank;
			
			$this->_guild_rank_id = $rank->GetId();
			$this->_guild_rank = $rank->GetName();
			$this->_guild_level = $rank->GetLevel();
			
			//loading guild
			$guild = new Guilds();
			$guild->Load($rank->GetGuildId());
			
			$this->Guild = $guild;
			
			$this->_guild_id = $guild->GetId();
			$this->_guild_name = $guild->GetName();
			
			return true;
		}		
	
		return false;
	}
	
	function getHouse()
	{
		$query = $this->db->query("SELECT id FROM houses WHERE owner = '".$this->data['id']."'");
		
		if($query->numRows() != 0)
		{
			return $query->fetch()->id;
		}
		else
			return false;
	}
	
	function loadSkills()
	{
		$query = $this->db->query("SELECT value, skillid FROM player_skills WHERE player_id = '".$this->data['id']."'");	
		
		if($query->numRows() != 0)
		{	
			while($fetch = $query->fetch())
			{
				$this->skills[$fetch->skillid] = $fetch->value;	
			}
		}
	}
	
	function saveSkills(){
	    foreach($this->skills as $skillid => $value){
	        $query = $this->db->query("UPDATE `player_skills` SET `value` = {$value} WHERE `skillid` = {$skillid} AND `player_id` = {$this->data['id']}");
	    }
	}
	
	function getSkill($skillid)
	{
		return $this->skills[$skillid];
	}
	
	function setSkill($skillid, $value){
	    $this->skills[$skillid] = $value;
	}
	
	function loadByName($player_name)
	{
		$query = $this->db->query("SELECT id FROM players WHERE name = '".$player_name."' AND deleted = 0");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->id);
			return true;
		}
		else
			return false;
	}

	function addItem($slot, $slot_pid, $itemid, $count) 
	{	
		//echo "{$this->data['id']}<br>";
		$this->db->query("INSERT INTO `player_items` VALUES ('".$this->data['id']."', '".$slot_pid."', '".$slot."', '".$itemid."', '".$count."', '')");
	}	
	
	function deletionStatus()
	{
		$query = $this->db->query("SELECT * FROM ".\Core\Tools::getSiteTable("playerdeletion")." WHERE player_id = '{$this->data['id']}'");
		
		if($query->numRows() != 0)
		{
			return $query->fetch()->time;
		}	
		else
			return false;
	}
	
	function cancelDeletion()
	{
		$this->db->query("DELETE FROM ".\Core\Tools::getSiteTable("playerdeletion")." WHERE player_id = '{$this->data['id']}'");
	}
	
	function addToDeletion()
	{
		$this->db->query("INSERT INTO ".\Core\Tools::getSiteTable("playerdeletion")." (player_id, time) values('{$this->data['id']}','".(time() + (60 * 60 * 24 * g_Configs::Get(g_Configs::eConf()->CHARACTER_DELETION_WAIT_DAYS)))."')");
	}		
	
	function getInvite()
	{
		$query = $this->db->query("SELECT guild_id, date FROM guild_invites WHERE player_id = '{$this->data['id']}'");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			return array($fetch->guild_id, $fetch->date);
		}
		else
			return false;
	}	
	
	function inviteToGuild($guild_id)
	{
		$this->db->query("INSERT INTO guild_invites (`player_id`, `guild_id`, `date`) values('{$this->data['id']}', '{$guild_id}', '".time()."')");
	}
	
	function acceptInvite()
	{
		$invite = $this->getInvite();
		
		if($invite)
		{
			list($guild_id, $inviteDate) = $invite;
			
			$guild = new Guilds();
			if($guild->Load($guild_id))
			{
				$rank = $guild->SearchRankByLowest();
				
				$this->LoadGuild();
				
				$this->setGuildRankId($rank->GetId());
				$this->setGuildNick("");
				$this->setGuildJoinIn(time());
				
				$this->save();
				
				$this->removeInvite();
			}
		}
	}
	
	function removeInvite()
	{
		$this->db->query("DELETE FROM guild_invites WHERE player_id = '{$this->data['id']}'");
	}
	
	function loadAccount()
	{		
		$account = new Account();
		$account->load($this->data["account_id"]);	
			
		return $account;	
	}
	
	function loadOldNames()
	{
		$query = $this->db->query("SELECT `value`, `time` FROM ".\Core\Tools::getSiteTable("changelog")." WHERE `type` = 'name' and `key` = '{$this->data["id"]}' ORDER BY `time` DESC");
		$names = array();
		
		if($query->numRows() != 0)
		{
			while($fetch = $query->fetch())
			{
				$names[$fetch->value] = $fetch->time;
			}
			
			return $names;
		}
		else
			return false;
	}
	
	function isPremium()
	{
		$account = new Account();
		
		$account->load($this->getAccountId());
		
		if($account->getPremDays() == 0)
			return false;
			
		return true;
	}
	
	function onDelete()
	{
		$this->data["rank_id"] = 0;
	}
	
	function getTotalKills()
	{
		$query = $this->db->query("
		SELECT
			COUNT(*) as `count`
		FROM
			`player_killers` `killer`
		LEFT JOIN
			`killers` `killers`	
		ON
			`killer`.`kill_id` = `killers`.`id`
		WHERE
			`killers`.`final_hit` = '1' AND
			`killer`.`player_id` = {$this->getId()}
		");
		
		$fetch = $query->fetch();
		return $fetch->count;	
	}
	
	function getTotalBgKills()
	{
		$query = $this->db->query("
		SELECT
			COUNT(*) as `count`
		FROM
			`custom_pvp_kills`
		WHERE
			`is_frag` = '1' AND
			`player_id` = {$this->getId()} AND
			`type` = ".Battleground::PVP_TYPE_BATTLEGROUND."
		");
		
		$fetch = $query->fetch();
		return $fetch->count;		
	}
	
	function getTotalAssists()
	{
		$query = $this->db->query("
		SELECT
			COUNT(*) as `count`
		FROM
			`player_killers` `killer`
		WHERE
			`killer`.`player_id` = {$this->getId()}
		");
		
		$fetch = $query->fetch();
		return $fetch->count;	
	}
	
	function getTotalBgAssists()
	{
		$query = $this->db->query("
		SELECT
			COUNT(*) as `count`
		FROM
			`custom_pvp_kills`
		WHERE
			`player_id` = {$this->getId()} AND
			`type` = ".Battleground::PVP_TYPE_BATTLEGROUND."
		");
		
		$fetch = $query->fetch();
		return $fetch->count;	
	}
	
	function getTotalDeathsPlayers()
	{
		$query = $this->db->query("
		SELECT
			COUNT(*) as `count`
		FROM
			`player_deaths` `death`
		LEFT JOIN
			`killers`
		ON
			`killers`.`death_id` = `death`.`id`
		LEFT JOIN
			`player_killers` `pk`
		ON
			`pk`.`kill_id` = `killers`.`id`
		WHERE
			`death`.`player_id` = {$this->getId()} AND
      		`pk`.`player_id` != ''
		GROUP BY
			`death`.`id`
		");
		
		return $query->numRows();	
	}
	
	function getTotalDeathsEnv()
	{
		$query = $this->db->query("
		SELECT
			COUNT(*) as `count`
		FROM
			`player_deaths` `death`
		LEFT JOIN
			`killers`
		ON
			`killers`.`death_id` = `death`.`id`
		LEFT JOIN
			`environment_killers` `ek`
		ON
			`ek`.`kill_id` = `killers`.`id`
		WHERE
			`death`.`player_id` = {$this->getId()} AND
      		`ek`.`name` != ''
		GROUP BY
			`death`.`id`
		");

		return $query->numRows();		
	}
	
	function getTotalDeaths()
	{
		$query = $this->db->query("
		SELECT
			COUNT(*) as `count`
		FROM
			`player_deaths` `death`
		WHERE
			`death`.`player_id` = {$this->getId()}
		");
		
		$fetch = $query->fetch();
		return $fetch->count;
	}
	
	function getTotalBgDeaths()
	{
		$query = $this->db->query("
		SELECT
			COUNT(*) as `count`
		FROM
			`custom_pvp_deaths`
		WHERE
			`player_id` = {$this->getId()} AND
			`type` = ".Battleground::PVP_TYPE_BATTLEGROUND."
		");
		
		$fetch = $query->fetch();
		return $fetch->count;			
	}	
	
	function getBattlegroundsWon()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_BATTLEGROUND_WIN."");
		return $query->numRows();
	}
	
	function getBattlegroundsLose()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_BATTLEGROUND_LOST."");
		return $query->numRows();
	}
	
	function getBattlegroundsDraw()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_BATTLEGROUND_DRAW."");
		return $query->numRows();
	}
	
	function getDungeonAriadneTrollsAttemps()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_DUNGEON_ARIADNE_TROLLS_ATTEMPS."");
		return $query->numRows();
	}
	
	function getDungeonAriadneTrollsCompleted()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_DUNGEON_ARIADNE_TROLLS_COMPLETED."");
		return $query->numRows();
	}
	
	function getBattlegroundFlagsCaptured()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_BATTLEGROUND_FLAGS_CAPTURED."");
		return $query->numRows();
	}
	
	function getBattlegroundFlagsDroped()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_BATTLEGROUND_FLAGS_DROPED."");
		return $query->numRows();
	}
	
	function getBattlegroundFlagsReturned()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_BATTLEGROUND_FLAGS_RETURNED."");
		return $query->numRows();
	}
	
	function getBattlegroundFlagsKilled()
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_LOG." AND `history` = ".self::PH_LOG_BATTLEGROUND_FLAGS_KILLED."");
		return $query->numRows();
	}
	
	function hasAchievement($history)
	{
		$query = \Core\Main::$DB->query("SELECT `history` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_ACHIEVEMENT." AND `history` = {$history}");
		return $query->numRows() > 0;
	}
	
	function getAchievementInfo($history)
	{
		$query = \Core\Main::$DB->query("SELECT `date`, `params` FROM `player_history` WHERE `player_id` = ".$this->data["id"]." AND `type` = ".self::PH_TYPE_ACHIEVEMENT." AND `history` = {$history}");

		$info = array();
		
		if($query->numRows() == 0)
			$info["has"] = false;
		else	
		{
			$info = $query->fetchArray();
			$info["has"] = true;
		}
		
		return $info;
	}	
	
	function hasAchievBattlegroundRankVeteran()
	{
		return $this->hasAchievement(self::PH_ACHIEV_BATTLEGROUND_RANK_VETERAN);
	}
	
	function hasAchievBattlegroundRankLegend()
	{
		return $this->hasAchievement(self::PH_ACHIEV_BATTLEGROUND_RANK_LEGEND);
	}
	
	function hasAchievBattlegroundRankBrave()
	{
		return $this->hasAchievement(self::PH_ACHIEV_BATTLEGROUND_RANK_BRAVE);
	}
	
	function hasAchievBattlegroundInsaneKiller()
	{
		return $this->hasAchievement(self::PH_ACHIEV_BATTLEGROUND_ISANE_KILLER);
	}
	
	function hasAchievBattlegroundPerfect()
	{
		return $this->hasAchievement(self::PH_ACHIEV_BATTLEGROUND_PERFECT);
	}	
	
	function set($field, $value)
	{
		switch($field)
		{
			case "id"; 					$this->setId($value); 				break;	
			case "name"; 				$this->setName($value); 			break;	
			case "group_id"; 			$this->setGroup($value);			break;	
			case "account_id"; 			$this->setAccountId($value); 		break;	
			case "level"; 				$this->setLevel($value); 			break;	
			case "vocation"; 			$this->setVocation($value); 		break;	
			case "health"; 				$this->setHealth($value); 			break;	
			case "healthmax"; 			$this->setHealth($value); 			break;	
			case "experience";			$this->setExperience($value); 		break;	
			case "maglevel";			$this->setMagLevel($value); 		break;	
			case "mana";				$this->setMana($value); 			break;	
			case "manamax";				$this->setMana($value); 			break;	
			case "town_id";				$this->setTownId($value); 			break;	
			case "cap";					$this->setCap($value); 				break;	
			case "sex";					$this->setSex($value); 				break;	
			case "comment";				$this->setComment($value); 			break;	
			case "description";			$this->setDescription($value); 		break;	
			case "creation";			$this->setCreation($value); 		break;	
			case "created";				$this->setCreation($value); 		break;	
			case "hide";				$this->setHidden($value); 			break;	
			case "hidden";				$this->setHidden($value); 			break;	
		}
	}
	
	
	
	function setId($value){ $this->data['id'] = $value; }	
	function setName($value){ $this->data['name'] = $value; }
	function setWorldId($value){ $this->data['world_id'] = $value; }
	function setGroup($value){ $this->data['group_id'] = $value; }	
	function setAccountId($value){ $this->data['account_id'] = $value; }	
	function setLevel($value){ $this->data['level'] = $value; }	
	function setVocation($value){ $this->data['vocation'] = $value; }
	
	function setHealth($value)
	{		
		$this->data['health'] = $value;
		$this->data['healthmax'] = $value;
	}
	
	function setExperience($value){	$this->data['experience'] = $value; }
	function setLookType($value){ $this->data['looktype'] = $value; }		
	function setMagLevel($value){ $this->data['maglevel'] = $value;	}
	
	function setMana($value)
	{
		$this->data['mana'] = $value;
		$this->data['manamax'] = $value;
	}
	
	function setTownId($value){	$this->data['town_id'] = $value; }	
	function setConditions($value){	$this->data['conditions'] = $value;	}	
	function setCap($value){ $this->data['cap'] = $value; }	
	function setSex($value){ $this->data['sex'] = $value; }	
	function setGuildRankId($rank_id){ $this->_loadGuild = true; $this->_guild_rank_id = $rank_id; }	
	function setGuildNick($nick){ $this->_loadGuild = true; $this->_guild_nick = \Core\Strings::SQLInjection($nick); }	
	function setGuildJoinIn($join_in){ $this->_loadGuild = true; $this->_guild_join_in = $join_in; }		
	function setDescription($value){ $this->data['description'] = \Core\Strings::SQLInjection($value); }	
	function setComment($value){ $this->site_data['comment'] = $value; }	
	function setCreation($value){ $this->site_data['creation'] = $value; }	
	function setStamina($value){ $this->data['stamina'] = $value; }	
	function setSkull($value){	$this->data['skull'] = $value; }	
	function setSkullTime($value){	$this->data['skulltime'] = $value; }	
	function setHidden($value){	$this->site_data['visible'] = $value; }	
	
	function setDeleted($bool){ 
		$this->data['deleted'] = $bool; 
		if($bool)
			$this->onDelete();
	}
	
	function setLossExperience($loss){
	    $this->data['loss_experience'] = $loss;
	}
	
	function get($field)
	{
		switch($field)
		{
			case "id":					return $this->getId();					break;
			case "name":				return $this->getName();				break;
			case "group":				return $this->getGroup();				break;
			case "account_id":			return $this->getAccountId();			break;
			case "level":				return $this->getLevel();				break;
			case "vocation":			return $this->getVocation();			break;
			case "experience":			return $this->getExperience();			break;
			case "town_id":				return $this->getTownId();				break;
			case "sex":					return $this->getSex();					break;
			case "online":				return $this->getOnline();				break;
			case "description":			return $this->getDescription();			break;
			case "comment":				return $this->getComment();				break;
			case "created":				return $this->getCreation();			break;
			case "hide":				return $this->getHidden();				break;
		}
		
		return true;
	}		
	
	
	
	function getId(){ return $this->data['id']; }
	function getName(){ return $this->data['name'];	}
	function getWorldId(){ return $this->data['world_id'];	}
	function getGroup(){ return $this->data['group_id']; }
	function getAccess(){
		if(!$this->_group)
		{
			$group_id = $this->getGroup();
			$this->_group = \Framework\Group::LoadById($group_id);
		}
		
		return $this->_group->access;		
	}
	function getAccountId(){ return $this->data['account_id']; }
	function getLevel(){ return $this->data['level']; }
	function getMagicLevel(){ return $this->data['maglevel']; }	
	
	function getVocation()
	{
		$vocation = $this->data['vocation'];
		
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS && $this->data['promotion'] >= 1)
            $vocation = \Core\Tools::transformToPromotion($this->data['promotion'], $vocation);
			
		return $vocation;
	}
	
	function getOnlineTime($hourAgo = 24)
	{
	    if($hourAgo != 0)
		    $query = \Core\Main::$DB->query("SELECT SUM(`online_ticks`) as `total` FROM `player_activities` WHERE `player_id` = ".$this->data["id"]." AND login >= UNIX_TIMESTAMP() - (60 * 60 * {$hourAgo}) GROUP BY `player_id`");
	    else
	        $query = \Core\Main::$DB->query("SELECT SUM(`online_ticks`) as `total` FROM `player_activities` WHERE `player_id` = ".$this->data["id"]." GROUP BY `player_id`");
	    
		if($query->numRows() == 0)
			return 0;
			
		return $query->fetch()->total;
	}
	
	function getExperience(){ return $this->data['experience']; }
	function getMagLevel(){	return $this->data['maglevel'];	}
	function getTownId(){ return $this->data['town_id']; }
	function getSex(){ return $this->data['sex']; }
	function GetGuildRank(){ return $this->_guild_rank; }
	function GetGuildRankId(){ return $this->_guild_rank_id; }
	function GetGuildName(){ return $this->_guild_name;	}
	function GetGuildId(){ return $this->_guild_id; }
	function GetGuildLevel(){ 
		if($this->getGroup() == \t_Group::Administrator) 
			return \Framework\Guilds::RANK_LEADER; 
		
		if(!$this->_loadGuild)
			$this->LoadGuild();
		
		return $this->_guild_level; 
	}	
	function getGuildNick(){ return stripslashes($this->_guild_nick); }	
	function getGuildJoinIn(){ return $this->_guild_join_in; }		
	function getOnline(){ return $this->data['online']; }	
	function getDescription(){ return stripslashes($this->data['description']); }	
	function getComment(){ return $this->site_data['comment']; }
	function getCreation(){	return $this->site_data['creation']; }	
	function getHidden(){ return $this->site_data['visible']; }	
	function getLastLogin(){ return $this->data['lastlogin']; }		
	function getPosX(){ return $this->data['posx']; }
	function getPosY(){ return $this->data['posy']; }
	function getPosZ(){	return $this->data['posz'];	}	
	function getIpAddress(){ return \Core\Tools::ip_long2string($this->data['lastip']); }	
	function getStamina(){ return $this->data['stamina']; }	
	function getSkull(){ return $this->data['skull']; }	
	function getSkullTime(){ return $this->data['skulltime']; }	
	function getBattlegroundRating() { return $this->data['battleground_rating']; }
	function isPvpEnabled() { return (bool)$this->data['pvpEnabled']; }
	function isDeleted(){ return (bool)$this->data['deleted']; }
	
	/**
	 * Retorna uma string HTML do tipo img.
	 * @param unknown_type $var Pode ser um objeto Player ou o skull type
	 */
	static function getSkullImg($var){
	     
	    if($var instanceof self){     
	        $skull_type = $var->getSkull();
	    }
	    elseif(is_numeric($var)){
	        $skull_type = $var;
	    }
	    
		$skull = null;
		switch($skull_type){
		    
		    case \t_Skulls::White:
		        $skull = "white_skull.gif";
		        break;
		        
		    case \t_Skulls::Red:
		        $skull = "red_skull.gif";
		        break;
		        
		    case \t_Skulls::Black:
		        $skull = "black_skull.gif";
		        break;		
		}
		
		$skull_img = "";
		
		if($skull)
		    $skull_img = "<img src='files/misc/{$skull}' />";
	    
	    return $skull_img;	    
	}
}
?>
