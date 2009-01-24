<?
list($module, $topic) = explode(".", $_GET['ref']);

switch($module)
{
	case "account":
	
		$patch['dir'] = $module;
	
		switch($topic)
		{
			case "register":
				$patch['file'] = $topic;
				$patch['urlnavigation'] = "/ ".$patch['dir']." / <a href='?ref=".$patch['dir'].".".$topic."'>".$topic."</a>";
			break;
		}
		
	break;

	default:
		$patch['dir'] = "news";
		$patch['file'] = "last";
	break;	
}

$module = "modules/".$patch['dir']."/".$patch['file'].".php";

?>