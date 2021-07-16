<?php
class ItemShop
{
	private $db, $data = array();

	function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	function load($id)
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."itemshop WHERE id = {$id}");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetchArray();
			
			foreach($fetch as $field => $value)
			{
				$this->data[$field] = $value;
			}
			
			return true;
		}
		else
			return false;
	}
	
	function save()
	{
		$i = 0;
	
		$query = $this->db->query("SELECT id FROM ".DB_WEBSITE_PREFIX."itemshop WHERE id = '".$this->data['id']."'");
		
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
			
			$this->db->query("UPDATE ".DB_WEBSITE_PREFIX."itemshop SET $update WHERE id = '".$this->data['id']."'");
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

			$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."itemshop ($insert_fields) values($insert_values)");			
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
}
?>