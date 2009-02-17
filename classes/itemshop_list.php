<?php
class ItemShop_List
{
	private $db, $data = array();

	function __construct()
	{
		global $db_tenerian;
		$this->db = $db_tenerian;
	}
	
	function load($id)
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."itemshop_list WHERE id = {$id}");
		
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
	
	function get($field)
	{
		return $this->data[$field];
	}
}
?>