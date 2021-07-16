<?php 
namespace Framework;
use \Core\Configs;
class Group
{
	public
		$id
		,$name
		,$flags = 0
		,$customFlags = 0
		,$access = 0
		;
	
	static function &LoadById($group_id)
	{
		$patch = Configs::Get(Configs::eConf()->PATCH_SERVER) . Configs::Get(Configs::eConf()->FOLDER_DATA). "XML/groups.xml";
		$xml = new \SimpleXMLElement($patch, null, true);
	
		$result = $xml->xpath("//*[@id=\"".(int)$group_id."\"]");
	
		if(!$result)
			return false;
	
		$attr = $result[0]->attributes();
	
		$group = new self();
		$group->id = $group_id;
		$group->name = $attr["name"];
		
		if($attr["flags"])
			$group->flags = $attr["flags"];
		
		if($attr["customFlags"])
			$group->customFlags = $attr["customFlags"];
		
		if($attr["access"])
			$group->access = $attr["access"];
	
		return $group;
	}
}
?>