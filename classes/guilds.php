<?php
class Guilds
{
	private $db, $data = array(), $ranks = array(), $members = array(), $invites = array();

	function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	function loadByRank($rank_id)
	{
		$query = $this->db->query("SELECT guild_id FROM guild_ranks WHERE id = '{$rank_id}'");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$this->load($fetch->guild_id);
			
			return true;
		}
		else
			return false;
	}
	
	function save()
	{
		$i = 0;
	
		$query = $this->db->query("SELECT id FROM guilds WHERE id = '".$this->data['id']."'");
		
		//update
		if($query->numRows() == 1)
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
			
			$this->db->query("UPDATE guilds SET $update WHERE id = '".$this->data['id']."'");
		}
		//new
		elseif($query->numRows() == 0)
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

			$this->db->query("INSERT INTO guilds ($insert_fields) values($insert_values)");	
			$this->load($this->db->lastInsertId());		
		}		
	}
	
	function load($id)
	{
		$query = $this->db->query("SELECT id, name, ownerid, creationdata, motd, image, status, formationTime  FROM guilds WHERE id = '{$id}'");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$this->data["id"] = $fetch->id;
			$this->data["name"] = $fetch->name;
			$this->data["ownerid"] = $fetch->ownerid;
			$this->data["creationdata"] = $fetch->creationdata;
			$this->data["motd"] = addslashes($fetch->motd);
			$this->data["image"] = $fetch->image;
			$this->data["status"] = $fetch->status;
			$this->data["formationTime"] = $fetch->formationTime;
			
			return true;
		}
		else
			return false;
	}
	
	function loadRanks()
	{
		$ranks_query = $this->db->query("SELECT id, name, level FROM guild_ranks WHERE guild_id = '{$this->data['id']}' ORDER by level");
		
		if($ranks_query->numRows() != 0)
		{
			while($ranks_fetch = $ranks_query->fetch())
			{
				$this->ranks[$ranks_fetch->id] = array('name' => $ranks_fetch->name, 'level' => $ranks_fetch->level);
			}		
		}			
	}
	
	function loadMembersList()
	{
		if(count($this->ranks) != 0)
		{
			foreach($this->ranks as $rank_id => $value)
			{
				$members_query = $this->db->query("SELECT name, guildnick, guild_join_date FROM players WHERE rank_id = '{$rank_id}'");
				
				while($fetch = $members_query->fetch())
				{
					$this->members[$fetch->name] = array('nick' => $fetch->guildnick, 'rank' => $value['name'], 'level' => $value['level'], 'joinDate' => $fetch->guild_join_date);
				}
			}
		}		
	}
	
	function loadInvitesList()
	{
		$invites_query = $this->db->query("SELECT player_id, date FROM guild_invites WHERE guild_id = '{$this->data['id']}'");
		
		if($invites_query->numRows() != 0)
		{
			while($invites_fetch = $invites_query->fetch())
			{
				$name_query = $this->db->query("SELECT name FROM players WHERE id = '{$invites_fetch->player_id}'");
				$name_fetch = $name_query->fetch();
				
				$this->invites[$name_fetch->name] = $invites_fetch->date;
			}
		}		
	}
	
	function loadByName($name)
	{
		$query = $this->db->query("SELECT id FROM guilds WHERE name = '{$name}'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->id);
			
			return true;
		}
		else
			return false;		
	}
	
	function disband()
	{
		$this->db->query("DELETE FROM guild_ranks WHERE guild_id = '{$this->data["id"]}'");
		$this->db->query("DELETE FROM guild_invites WHERE guild_id = '{$this->data["id"]}'");
		$this->db->query("UPDATE players SET rank_id = '0', guildnick = '', guild_join_date = '0' WHERE id = '{$this->data["ownerid"]}'");
		$this->db->query("DELETE FROM guilds WHERE id = '{$this->data["id"]}'");
	}
	
	function getMembersList()
	{
		return $this->members;
	}
	
	function getRanks()
	{
		return $this->ranks;
	}	
	
	function getInvites()
	{
		return $this->invites;
	}		
	
	function get($field)
	{
		if($field == "motd")
		{
			return stripslashes($this->data["motd"]);
		}
		else	
			return $this->data[$field];
	}
	
	function set($field, $value)
	{
		global $strings;
		
		if($field == "motd")
			$value = $strings->SQLInjection($value);
		
		$this->data[$field] = $value;
	}
	
	function setRank($name, $level)
	{
		$query = $this->db->query("SELECT id FROM guild_ranks WHERE level = '{$level}' and guild_id = '{$this->data['id']}'");
		
		if($query->numRows() != 0)
			$this->db->query("UPDATE guild_ranks SET name = '{$name}' WHERE level = '{$level}' and guild_id = '{$this->data['id']}'");
		else
			$this->db->query("INSERT INTO guild_ranks (name, level, guild_id) values('{$name}', '{$level}', '{$this->data['id']}')");
	}
	
	function ereaseRank($level)
	{
		$query = $this->db->query("SELECT id FROM guild_ranks WHERE level = '{$level}' and guild_id = '{$this->data['id']}'");
		$fetch = $query->fetch();
		
		$query_players = $this->db->query("SELECT id FROM players WHERE rank_id = '{$fetch->id}'");
		
		if($query->numRows() != 0)
		{
			if($query_players->numRows() == 0)
			{
				$this->db->query("DELETE FROM guild_ranks WHERE level = '{$level}' and guild_id = '{$this->data['id']}'");
			}
			else
			{
				return 1;
			}	
		}	
		else
		{
			return 2;
		}
	}
	
	function getLowerRank()
	{
		$lastRanklvl = 0;
		$lastRank = 0;
		
		foreach($this->ranks as $rank_id => $rank_value)
		{
			if($rank_value['level'] > $lastRanklvl)
			{
				$lastRanklvl = $rank_value['level'];
				$lastRank = $rank_id;
			}
		}

		return $lastRank;
	}
}
?>