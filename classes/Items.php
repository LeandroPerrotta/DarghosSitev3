<?php
class Items
{
	static private $_instance;
	
	function __construct()
	{
		$query = Core::$DB->query("SELECT name FROM ".DB_WEBSITE_PREFIX."items");
		
		if($query->numRows() != 0)
		{
			return;
		}		
		
		$itemsXML = new DOMDocument();
		
		if(file_exists(DIR_DATA."items/items.xml"))
		{
			$itemsXML->load(DIR_DATA."items/items.xml");
			
			$nodeList = $itemsXML->getElementsByTagName("item");
			for($x = 0; $x < $nodeList->length; $x++)
			{				
				$id = $nodeList->item($x)->getAttribute("id");
				$name = addslashes($nodeList->item($x)->getAttribute("name"));
				if($id && $name)
				{
					Core::$DB->query("INSERT INTO ".DB_WEBSITE_PREFIX."items values ('{$id}', '{$name}')");
				}
			}
		}
		else
			die("Banco de dados necessario não localizado #5400.");	
	}	
	
	static function GetInstance()
	{
		if(self::$_instance)
		{
			return self::$_instance;
		}
		
		$class = __CLASS__;
		self::$_instance = new $class;
		return self::$_instance;
	}	

	function getNameById($itemid)
	{
		$query = Core::$DB->query("SELECT `name` FROM ".DB_WEBSITE_PREFIX."items WHERE `id` = '{$itemid}'");
		
		if($query->numRows() == 0)
			return false;
			
		$fetch = $query->fetch();
		
		return stripcslashes($fetch->name);
	}
}