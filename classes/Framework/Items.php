<?php
namespace Framework;
use \Core\Configs as g_Configs;;
class Items
{
	static private $_instance;
	
	static function LoadById($item_id)
	{
		$patch = g_Configs::Get(g_Configs::eConf()->PATCH_SERVER) . g_Configs::Get(g_Configs::eConf()->FOLDER_DATA). "items/items.xml";
		$xml = new \SimpleXMLElement($patch, null, true);
		
		$result = $xml->xpath("//*[@id=\"".(int)$item_id."\"]");
		
		if(!$result)
			return false;
		
		$attr = $result[0]->attributes();
		
		$item = new Item();
		$item->SetId($attr["id"]);
		$item->SetName($attr["name"]);
		
		return $item;
	}
	
	function __construct()
	{
		$query = \Core\Main::$DB->query("SELECT name FROM ".\Core\Tools::getSiteTable("items")."");
		
		if($query->numRows() != 0)
		{
			return;
		}		
		
		$itemsXML = new \DOMDocument();
		
		$patch = g_Configs::Get(g_Configs::eConf()->FOLDER_DATA)."items/items.xml";
		
		if(file_exists($patch))
		{
			$itemsXML->load($patch);
			
			$nodeList = $itemsXML->getElementsByTagName("item");
			for($x = 0; $x < $nodeList->length; $x++)
			{				
				$id = $nodeList->item($x)->getAttribute("id");
				$name = addslashes($nodeList->item($x)->getAttribute("name"));
				if($id && $name)
				{
					\Core\Main::$DB->query("INSERT INTO ".\Core\Tools::getSiteTable("items")." values ('{$id}', '{$name}')");
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
		$query = \Core\Main::$DB->query("SELECT `name` FROM ".\Core\Tools::getSiteTable("items")." WHERE `id` = '{$itemid}'");
		
		if($query->numRows() == 0)
			return false;
			
		$fetch = $query->fetch();
		
		return stripcslashes($fetch->name);
	}
}