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

	if($_GET)
	{
		if(($needLogin and $_SESSION['login']) or (!$needLogin))
		{
			if($patch['dir'] != "errors")
				$patch['urlnavigation'] = "/ ".$patch['dir']." / <a href='?ref=".$patch['dir'].".".$patch['file']."'>".$patch['file']."</a>";
				
			$module = "modules/".$patch['dir']."/".$patch['file'].".php";
		}	
		else
			$module = "modules/errors/needlogin.php";	
	}	
	else	
		$module = "modules/news/last.php";
}	
else
	$module = "modules/errors/sqlinjection.php";	

?>