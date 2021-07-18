<?php 
namespace Framework;
use \Core\Configs;
class Group
{
	public
		$id
		,$name
		,$flags = 0
		,$customFlags = 0 //TFS only
		,$access = 0
		;
	
	static function &LoadById($group_id)
	{
	    if(Configs::Get(Configs::eConf()->USE_DISTRO) == \Core\Consts::SERVER_DISTRO_TFS){
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
		elseif(Configs::Get(Configs::eConf()->USE_DISTRO) == \Core\Consts::SERVER_DISTRO_OPENTIBIA){
		    
		    $query = \Core\Main::$DB->query("SELECT `name`, `flags`, `access` FROM `groups` WHERE `id` = {$group_id}");
		    
		    if($query->numRows() == 1){
		        
		        $fetch = $query->fetch();
		        
		        $group = new self();
		        
		        $group->id = $group_id;
		        $group->name = $fetch->name;
		        $group->flags = $fetch->flags;
		        $group->access = $fetch->access;
		        
		        return $group;
		    }
		    else
		        return false;
	    }
	    
	    return false;
	}
}
?>