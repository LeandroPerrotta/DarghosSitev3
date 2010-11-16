<?
class Character
{
	private $db, $data = array(), $skills = array(), $guild = array() /* deprecated? */;
	
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

	function Character()
	{
		$this->db = Core::$DB;
	}

	function save()
	{		
		$i = 0;
		
		if(isset($this->data['id']))
		{
			foreach($this->data as $field => $value)
			{
				$i++;
				
				if($i == count($this->data))
				{
					$update .= "".$field." = '".$value."'";
				}
				else
				{
					$update .= "".$field." = '".$value."', ";
				}			
			}
			
			$this->db->query("UPDATE players SET $update WHERE id = '".$this->data['id']."'");

			if($this->_loadGuild)
			{
				//first, erease all guild member information from player
				Core::$DB->query("DELETE FROM `guild_members` WHERE `player_id` = '{$this->data['id']}'");			
				
				//player have rank id, saving guild member information
				if($this->_guild_rank_id)
				{				
					//add a new guild information
					Core::$DB->query("
						INSERT INTO `guild_members`
							(`player_id`, `rank_id`, `nick`, `join_in`) 
							values
							('{$this->data["id"]}', '{$this->_guild_rank_id}', '{$this->_guild_nick}', '{$this->_guild_join_in}')
					");
				}
			}
		}
		//create new character!!
		else
		{
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
		}
	}	
	
	function load($player_id)
	{
		$query = $this->db->query("SELECT id, name, group_id, account_id, level, vocation, maglevel, health, healthmax, experience, lookbody, lookfeet, lookhead, looklegs, looktype, lookaddons, maglevel, mana, manamax, manaspent, soul, town_id, posx, posy, posz, conditions, cap, sex, lastlogin, lastip, save, skull_type, lastlogout, balance, stamina, direction, loss_experience, loss_mana, loss_skills, loss_items, description, created, hidden, online, skull_time FROM players WHERE id = '".$player_id."'");		
		
		if($query->numRows() != 1)
		{
			return false;
		}	
			
		$fetch = $query->fetch();
		
		$this->data['id'] = $fetch->id;				
		$this->data['name'] = $fetch->name;
		$this->data['group_id'] = $fetch->group_id;
		$this->data['account_id'] = $fetch->account_id;
		$this->data['level'] = $fetch->level;
		$this->data['vocation'] = $fetch->vocation;
		$this->data['health'] = $fetch->health;
		$this->data['healthmax'] = $fetch->healthmax;
		$this->data['experience'] = $fetch->experience;
		$this->data['lookbody'] = $fetch->lookfeet;
		$this->data['lookfeet'] = $fetch->lookfeet;
		$this->data['lookhead'] = $fetch->lookhead;
		$this->data['looklegs'] = $fetch->looklegs;
		$this->data['looktype'] = $fetch->looktype;
		$this->data['lookaddons'] = $fetch->lookaddons;
		$this->data['maglevel'] = $fetch->maglevel;
		$this->data['mana'] = $fetch->mana;
		$this->data['manamax'] = $fetch->manamax;
		$this->data['manaspent'] = $fetch->manaspent;
		$this->data['soul'] = $fetch->soul;
		$this->data['town_id'] = $fetch->town_id;
		$this->data['posx'] = $fetch->posx;
		$this->data['posy'] = $fetch->posy;
		$this->data['posz'] = $fetch->posz;
		$this->data['conditions'] = $fetch->conditions;
		$this->data['cap'] = $fetch->cap;
		$this->data['sex'] = $fetch->sex;
		$this->data['lastlogin'] = $fetch->lastlogin;
		$this->data['lastip'] = $fetch->lastip;
		$this->data['save'] = $fetch->save;
		$this->data['skull_type'] = $fetch->skull_type;
		$this->data['lastlogout'] = $fetch->lastlogout;
		$this->data['balance'] = $fetch->balance;
		$this->data['stamina'] = $fetch->stamina;
		$this->data['direction'] = $fetch->direction;
		$this->data['loss_experience'] = $fetch->loss_experience;
		$this->data['loss_mana'] = $fetch->loss_mana;
		$this->data['loss_skills'] = $fetch->loss_skills;
		$this->data['loss_items'] = $fetch->loss_items;
		$this->data['description'] = addslashes($fetch->description);
		$this->data['created'] = $fetch->created;
		$this->data['hidden'] = $fetch->hidden;
		$this->data['online'] = $fetch->online;
		$this->data['skull_time'] = $fetch->skull_time;
		
		return true;			
	}
	
	function LoadGuild()
	{
		$this->_loadGuild = true;
		
		//loading guild infos of player
		$query = Core::$DB->query("SELECT `rank_id`, `nick`, `join_in` FROM `guild_members` WHERE `player_id` = '{$this->data["id"]}'");
		
		if($query->numRows() == 1)
		{			
			$fetch = $query->fetch();
			
			$this->_guild_nick = $fetch->nick;
			$this->_guild_join_in = $fetch->join_in;
			
			//loading guild rank of member
			$rank = new Guild_Rank();
			$rank->load($fetch->rank_id);
			
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
		
	function loadLastDeaths()
	{
		$query = $this->db->query("SELECT id, player_id, date, level FROM player_deaths WHERE player_id = '".$this->data['id']."' AND date + ".(60 * 60 * 24 * SHOW_DEATHS_DAYS_AGO)." > ".time()." ORDER BY date DESC");	
		
		if($query->numRows() != 0)
		{	
			$deathlist = array();
			while($death_fetch = $query->fetch())
			{		
				$_killer = NULL;
				$_altkiller = NULL;
				
				$killers_query = $this->db->query("SELECT id, death_id, lasthit FROM killers WHERE death_id = '{$death_fetch->id}'");
				while($killers_fetch = $killers_query->fetch())
				{
					$player_killers_query = $this->db->query("SELECT kill_id, player_id WHERE kill_id = '{$killers_fetch->id}' ORDER BY kill_id LIMIT 1");
					if($player_killers_query->numRows() != 0)
					{
						if($killers_fetch->lasthit == 1)
							$_killer = $player_killers_query->fetch()->player_id;
						else
							$_altkiller = $player_killers_query->fetch()->player_id;
					}
					
					$env_killers_query = $this->db->query("SELECT kill_id, name FROM environment_killers WHERE kill_id = '{$killers_fetch->id}' ORDER BY kill_id LIMIT 1");
					if($env_killers_query->numRows() != 0)
					{
						if($killers_fetch->lasthit == 1)
							$_killer = $env_killers_query->fetch()->name;
						else
							$_altkiller = $env_killers_query->fetch()->name;
					}
					
				}
				
				$deathlist[] = array
				(
					"time" => $death_fetch->date,
					"level" => $death_fetch->level,
					"killed_by" => $_killer,
					"altkilled_by" => $_altkiller
				);	
			}
			
			return $deathlist;
		}	
		else
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
	
	function getSkill($skillid)
	{
		return $this->skills[$skillid];
	}
	
	function loadByName($player_name)
	{
		$query = $this->db->query("SELECT id FROM players WHERE name = '".$player_name."'");
		
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
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."playerdeletion WHERE player_id = '{$this->data['id']}'");
		
		if($query->numRows() != 0)
		{
			return $query->fetch()->time;
		}	
		else
			return false;
	}
	
	function cancelDeletion()
	{
		$this->db->query("DELETE FROM ".DB_WEBSITE_PREFIX."playerdeletion WHERE player_id = '{$this->data['id']}'");
	}
	
	function addToDeletion()
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."playerdeletion (player_id, time) values('{$this->data['id']}','".(time() + (60 * 60 * 24 * DAYS_TO_DELETE_CHARACTER))."')");
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
		$query = $this->db->query("SELECT value, time FROM ".DB_WEBSITE_PREFIX."changelog WHERE type = 'name' and player_id = '{$this->data["id"]}' ORDER BY time DESC");
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
			case "comment";				$this->setDescription($value); 		break;	
			case "description";			$this->setDescription($value); 		break;	
			case "creation";			$this->setCreation($value); 		break;	
			case "created";				$this->setCreation($value); 		break;	
			case "hide";				$this->setHidden($value); 			break;	
			case "hidden";				$this->setHidden($value); 			break;	
		}
	}
	
	
	
	function setId($value)
	{
		$this->data['id'] = $value;
	}
	
	function setName($value)
	{
		$this->data['name'] = $value;
	}
	
	function setGroup($value)
	{
		$this->data['group_id'] = $value;
	}
	
	function setAccountId($value)
	{
		$this->data['account_id'] = $value;
	}
	
	function setLevel($value)
	{
		$this->data['level'] = $value;
	}
	
	function setVocation($value)
	{
		$this->data['vocation'] = $value;
	}
	
	function setHealth($value)
	{
		$this->data['health'] = $value;
		$this->data['healthmax'] = $value;
	}
	
	function setExperience($value)
	{
		$this->data['experience'] = $value;
	}

	function setLookType($value)
	{
		$this->data['looktype'] = $value;
	}	
	
	function setMagLevel($value)
	{
		$this->data['maglevel'] = $value;
	}
	
	function setMana($value)
	{
		$this->data['mana'] = $value;
		$this->data['manamax'] = $value;
	}
	
	function setTownId($value)
	{
		$this->data['town_id'] = $value;
	}
	
	function setConditions($value)
	{
		$this->data['conditions'] = $value;
	}	
	
	function setCap($value)
	{
		$this->data['cap'] = $value;
	}
	
	function setSex($value)
	{
		$this->data['sex'] = $value;
	}
	
	function setGuildRankId($rank_id)
	{
		$this->_guild_rank_id = $rank_id;
	}
	
	function setGuildNick($nick)
	{
		$this->_guild_nick = Strings::SQLInjection($nick);
	}
	
	function setGuildJoinIn($join_in)
	{
		$this->_guild_join_in = $join_in;
	}	
	
	function setDescription($value)
	{
		$this->data['description'] = Strings::SQLInjection($value);
	}
	
	function setCreation($value)
	{
		$this->data['created'] = $value;
	}
	
	function setStamina($value)
	{
		$this->data['stamina'] = $value;
	}
	
	function setHidden($value)
	{
		$this->data['hidden'] = $value;
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
			case "comment":				return $this->getDescription();			break;
			case "created":				return $this->getCreation();			break;
			case "hide":				return $this->getHidden();				break;
		}
		
		return true;
	}		
	
	
	
	function getId()
	{
		return $this->data['id'];
	}
	
	function getName()
	{
		return $this->data['name'];
	}
	
	function getGroup()
	{
		return $this->data['group_id'];
	}
	
	function getAccountId()
	{
		return $this->data['account_id'];
	}
	
	function getLevel()
	{
		return $this->data['level'];
	}
	
	function getMagicLevel()
	{
		return $this->data['maglevel'];
	}	
	
	function getVocation()
	{
		return $this->data['vocation'];
	}
	
	function getExperience()
	{
		return $this->data['experience'];
	}
	
	function getMagLevel()
	{
		return $this->data['maglevel'];
	}
	
	function getTownId()
	{
		return $this->data['town_id'];
	}
	
	function getSex()
	{
		return $this->data['sex'];
	}
	
	function GetGuildRank()
	{
		return $this->_guild_rank;
	}
	
	function GetGuildRankId()
	{
		return $this->_guild_rank_id;
	}
	
	function GetGuildName()
	{
		return $this->_guild_name;
	}
	
	function GetGuildId()
	{
		return $this->_guild_id;
	}
	
	function GetGuildLevel()
	{
		return $this->_guild_level;
	}
	
	function getGuildNick()
	{
		return stripslashes($this->_guild_nick);
	}
	
	function getGuildJoinIn()
	{
		return $this->_guild_join_in;
	}	
	
	function getOnline()
	{
		return $this->data['online'];
	}
	
	function getDescription()
	{
		return stripslashes($this->data['description']);
	}
	
	function getCreation()
	{
		return $this->data['created'];
	}
	
	function getHidden()
	{
		return $this->data['hidden'];
	}
	
	function getLastLogin()
	{
		return $this->data['lastlogin'];
	}	
	
	function getPosX()
	{
		return $this->data['posx'];
	}
	
	function getPosY()
	{
		return $this->data['posy'];
	}

	function getPosZ()
	{
		return $this->data['posz'];
	}	

	function getStamina()
	{
		return $this->data['stamina'];
	}	
}
?>