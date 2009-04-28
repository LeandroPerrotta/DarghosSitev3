<?php
class PagesDB
{
	private $db, $data = array();

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}
	
	function load($id)
	{
		$query = $this->db->query("SELECT id, content FROM ".DB_WEBSITE_PREFIX."pages WHERE id = '{$id}'");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$this->data["content"] = $fetch->content;
			$this->data["id"] = $fetch->id;
			
			return true;
		}
		
		return false;
	}
	
	function printContent()
	{
		global $module;
		$module .= $this->data["content"];
	}
	
	function setContent($content)
	{
		$this->data["content"] = $content;
	}
	
	function getContent()
	{
		return $this->data["content"];
	}
	
	function save($id)
	{
		$i = 0;
		
		//update
		if(isset($this->data["id"]))
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
			
			$this->db->query("UPDATE ".DB_WEBSITE_PREFIX."pages SET $update WHERE id = '".$this->data['id']."'");
		}
		//new
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

			$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."pages (`id`, $insert_fields) values('{$id}', $insert_values)");	
			$this->load($this->db->lastInsertId());		
		}
	}
}	
?>