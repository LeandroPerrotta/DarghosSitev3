<?
class Character
{
	private $db, $data = array(), $skills = array();

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}

	function load($player_id, $fields = null)
	{
		if($fields)
			$query = $this->db->query("SELECT id, $fields FROM players WHERE id = '".$player_id."'");
		else
			$query = $this->db->query("SELECT id FROM players WHERE id = '".$player_id."'");		
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			$this->data['id'] = $fetch->id;	
					
			if($fields)	
			{	
				$e = explode(", ", $fields);
				foreach($e as $field)
				{
					$this->data[$field] = $fetch->$field;
				}
			}

			return true;	
		}
		else
		{
			return false;
		}			
	}
	
	function loadLastDeaths()
	{
		$query = $this->db->query("SELECT time, level, killed_by, is_player FROM player_deaths WHERE player_id = '".$this->data['id']."' AND time + ".(60 * 60 * 24 * SHOW_DEATHS_DAYS_AGO)." > ".time()." ORDER BY time DESC");	
		
		if($query->numRows() != 0)
		{	
			$deathlist = array();
			while($fetch = $query->fetch())
			{	
				$deathlist[] = array
				(
					"time" => $fetch->time,
					"level" => $fetch->level,
					"killed_by" => $fetch->killed_by,
					"is_player" => $fetch->is_player
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
		$query = $this->db->query("SELECT id FROM players WHERE name = '".$this->data['name']."'");
		
		$i = 0;
		
		if($query->numRows() != 0)
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
		//criaчуo de novos personagens!!
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

			$this->db->query("INSERT INTO players ($insert_fields) values($insert_values)");						
		}
	}
	
	function set($field, $value)
	{
		$this->data[$field] = $value;
	}
	
	function get($field)
	{
		return $this->data[$field];
	}	

	function addItem($slot, $slot_pid, $itemid, $count) 
	{	
		$this->db->query("INSERT INTO `player_items` VALUES ('".$this->data['id']."', '".$slot_pid."', '".$slot."', '".$itemid."', '".$count."', '', '', '0')");
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
}
?>