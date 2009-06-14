<?
class Character
{
	private $db, $data = array(), $skills = array(), $guild = array();

	function __construct()
	{
		global $db;
		$this->db = $db;
	}

	
	
	function load($player_id, $fields = null)
	{
		$query = $this->db->query("SELECT id, name, group_id, account_id, level, vocation, maglevel, health, healthmax, experience, lookbody, lookfeet, lookhead, looklegs, looktype, lookaddons, maglevel, mana, manamax, manaspent, soul, town_id, posx, posy, posz, conditions, cap, sex, lastlogin, lastip, save, redskull, redskulltime, rank_id, guildnick, lastlogout, balance, stamina, direction, loss_experience, loss_mana, loss_skills, loss_items, description, guild_join_date, created, hidden, online FROM players WHERE id = '".$player_id."'");		
		
		if($query->numRows() != 0)
		{
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
			$this->data['redskull'] = $fetch->redskull;
			$this->data['redskulltime'] = $fetch->redskulltime;
			$this->data['rank_id'] = $fetch->rank_id;
			$this->data['guildnick'] = $fetch->guildnick;
			$this->data['lastlogout'] = $fetch->lastlogout;
			$this->data['balance'] = $fetch->balance;
			$this->data['stamina'] = $fetch->stamina;
			$this->data['direction'] = $fetch->direction;
			$this->data['loss_experience'] = $fetch->loss_experience;
			$this->data['loss_mana'] = $fetch->loss_mana;
			$this->data['loss_skills'] = $fetch->loss_skills;
			$this->data['loss_items'] = $fetch->loss_items;
			$this->data['description'] = $fetch->description;
			$this->data['guild_join_date'] = $fetch->guild_join_date;
			$this->data['created'] = $fetch->created;
			$this->data['hidden'] = $fetch->hidden;
			$this->data['online'] = $fetch->online;

			return true;	
		}
		else
		{
			return false;
		}			
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
			case "rank_id";				$this->setRankId($value); 			break;	
			case "guildnick";			$this->setGuildNick($value); 		break;	
			case "comment";				$this->setDescription($value); 		break;	
			case "description";			$this->setDescription($value); 		break;	
			case "guild_join_date";		$this->setGuildJoindate($value); 	break;	
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
	
	function setRankId($value)
	{
		$this->data['rank_id'] = $value;
	}
	
	function setGuildNick($value)
	{
		$this->data['guildnick'] = $value;
	}
	
	function setDescription($value)
	{
		$this->data['description'] = $value;
	}
	
	function setGuildJoinDate($value)
	{
		$this->data['guild_join_date'] = $value;
	}
	
	function setCreation($value)
	{
		$this->data['created'] = $value;
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
			case "rank_id":				return $this->getRankId();				break;
			case "guildnick":			return $this->getGuildNick();			break;
			case "online":				return $this->getOnline();				break;
			case "description":			return $this->getDescription();			break;
			case "comment":				return $this->getDescription();			break;
			case "guild_join_date":		return $this->getGuildJoinDate();		break;
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
	
	function getRankId()
	{
		return $this->data['rank_id'];
	}
	
	function getGuildNick()
	{
		return $this->data['guildnick'];
	}
	
	function getOnline()
	{
		return $this->data['online'];
	}
	
	function getDescription()
	{
		return $this->data['description'];
	}
	
	function getGuildJoinDate()
	{
		return $this->data['guild_join_date'];
	}
	
	function getCreation()
	{
		return $this->data['created'];
	}
	
	function getHidden()
	{
		return $this->data['hidden'];
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
	
	function loadByName($player_name, $fields = null)
	{
		$query = $this->db->query("SELECT id FROM players WHERE name = '".$player_name."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->id, $fields);
			return true;
		}
		else
			return false;
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
		}
		//criação de novos personagens!!
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
	
	function loadGuild()
	{		
		if($this->data["rank_id"] == 0)
			return false;
			
		global $core;
		
		$guild = $core->loadClass("Guilds");
			
		if($guild->loadByRank($this->data["rank_id"]))
		{	
			$guild->loadRanks();	
			$ranks = $guild->getRanks();
			
			$this->guild["name"] = $guild->get("name");
			$this->guild["rank_name"] = $ranks[$this->data["rank_id"]]["name"];
			$this->guild["rank_level"] = $ranks[$this->data["rank_id"]]["level"];
			
			return true;
		}
		else
			return false;
	}
	
	function getGuildInfo($value)
	{
		return $this->guild[$value];
	}
	
	function wasInvitedToGuild()
	{
		$query = $this->db->query("SELECT guild_id FROM guild_invites WHERE player_id = '{$this->data['id']}'");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			return $fetch->guild_id;
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
		$guild_id = $this->wasInvitedToGuild();
		
		if($guild_id)
		{
			global $core;
			
			$guild = $core->loadClass("Guilds");			
			if($guild->load($guild_id))
			{
				$guild->loadRanks();
				$lowerRank = $guild->getLowerRank();
				
				$this->set("rank_id", $lowerRank);
				$this->set("guild_join_date", time());
				
				$this->removeInvite();
			}
		}
	}
	
	function removeInvite()
	{
		$this->db->query("DELETE FROM guild_invites WHERE player_id = '{$this->data['id']}'");
	}
	
	function loadAccount($fields = null)
	{
		global $core;
		
		$account = $core->loadClass("Account");
		
		if($fields)
			$account->load($this->data["account_id"], $fields);
		else
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
}
?>