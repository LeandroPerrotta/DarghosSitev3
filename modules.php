<?
list($module, $topic) = explode(".", $_GET['ref']);

if($core->filterInputs())
{
	$needLogin = false;

	switch($module)
	{
		case "account":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "register":
					$patch['file'] = $topic;
				break;
				
				case "login":
					$patch['file'] = $topic;
				break;	
				
				case "main":
					$needLogin = true;
					$patch['file'] = $topic;
				break;				

				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;					
			}
			
		break;

		case "news":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "last":
					$patch['file'] = $topic;
				break;	

				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;					
			}
			
		break;
		
		default:
			$patch['dir'] = "errors";
			$patch['file'] = "notfound";
		break;	
	}

	$module = null;
	
	if($_GET)
	{
		if(($needLogin and $_SESSION['login']) or (!$needLogin))
		{
			if($patch['dir'] != "errors")
				$patch['urlnavigation'] = "/ ".$patch['dir']." / <a href='?ref=".$patch['dir'].".".$patch['file']."'>".$patch['file']."</a>";
				
			include("modules/".$patch['dir']."/".$patch['file'].".php");
		}	
		else
			include("modules/errors/needlogin.php");	
	}	
	else	
		include("modules/news/last.php");
}	
else
	include("modules/errors/sqlinjection.php");	

?>