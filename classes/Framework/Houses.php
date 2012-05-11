<?php
namespace Framework;
use \Core\Configs as g_Configs;
use \Core\Consts;
class Houses
{
	private $db, $data = array();
	
	static private $xml;

	function __construct($world_id)
	{
		$this->db = \Core\Main::$DB;
		
		if(!self::$xml)
		{
			$patch = g_Configs::Get(g_Configs::eConf()->PATCH_SERVER) . g_Configs::Get(g_Configs::eConf()->FOLDER_DATA, $world_id) . g_Configs::Get(g_Configs::eConf()->FILE_HOUSES, $world_id);
			self::$xml = \Core\Main::ParseXML($patch);
		}
	}

	function load($id, $world_id)
	{		
		$query = $this->db->query("SELECT `name`, `rent`, `size`, `town`, `owner`, `paid`, `warnings` FROM `houses` WHERE `id` = '{$id}' AND `world_id` = {$world_id} ");
		
		while($fetch = $query->fetch())
		{
			$this->data['id'] = $id;
			$this->data['name'] = $fetch->name;
			$this->data['rent'] = $fetch->rent;
			$this->data['size'] = $fetch->size;
			
			if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
				$this->data['townid'] = $fetch->town;		
			elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
				$this->data['townid'] = $fetch->townid;		
					
			$this->data['owner'] = $fetch->owner;
			$this->data['paid'] = $fetch->paid;
			$this->data['warnings'] = $fetch->warnings;
		}
	}
	
	function delete()
	{
		$this->db->ExecQuery("DELETE FROM `houses` WHERE `id` = '{$this->data['id']}'");
	}
	
	function isValid()
	{			
		foreach(self::$xml->house as $node)
		{
			$attr = $node->attributes();
			if($attr["houseid"] == $this->data['id'])
				return true;
		}		
		
		return false;
	}
	
	function get($field)
	{
		return $this->data[$field];
	}
	
	function getTown()
	{
		return $this->data["townid"];
	}
	
	static function deleteOldHouses($world_id)
	{
		$xml = new \DOMDocument();
		
		$patch = g_Configs::Get(g_Configs::eConf()->PATCH_SERVER) . g_Configs::Get(g_Configs::eConf()->FOLDER_DATA, $world_id) . g_Configs::Get(g_Configs::eConf()->FILE_HOUSES, $world_id);
		$xml->load($patch);		
		
		$exists = array();
		
		foreach($xml->getElementsByTagName("house") as $house)
		{
			$exists[] = $house->getAttribute("houseid");
		}		
		
		$query = \Core\Main::$DB->query("SELECT `id` FROM `houses`");
		$i = 0;
		
		while($fetch = $query->fetch())
		{
			if(!in_array($fetch->id, $exists))
			{
				\Core\Main::$DB->query("DELETE FROM `map_store` WHERE `house_id` = '{$fetch->id}'");
				\Core\Main::$DB->query("DELETE FROM `house_lists` WHERE `house_id` = '{$fetch->id}'");
				\Core\Main::$DB->query("DELETE FROM `houses` WHERE `id` = '{$fetch->id}'");
				
				$i++;
			}
		}
		
		echo "Casas apagadas: {$i}";
	}
}
?>