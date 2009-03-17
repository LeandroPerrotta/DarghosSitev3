<?
list($module, $topic) = explode(".", $_GET['ref']);

$noInjection = false;

if(!in_array($_GET['ref'], $_inputsWhiteList))
{
	if($strings->filterInputs(true))
	{
		$noInjection = true;
	}	
}
else
{
	$noInjection = true;
}

if($noInjection)
{
	$needLogin = false;
	$needPremium = false;

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

				case "changepassword":
					$needLogin = true;
					$patch['file'] = $topic;
				break;		

				case "logout":
					$needLogin = true;
					$patch['file'] = $topic;
				break;		

				case "premium":
					$patch['file'] = $topic;
				break;		

				case "changeemail":
					$needLogin = true;
					$patch['file'] = $topic;
				break;				

				case "cancelchangeemail":
					$needLogin = true;
					$patch['file'] = $topic;
				break;	

				case "changeinfos":
					$needLogin = true;
					$patch['file'] = $topic;
				break;					
				
				case "recovery":
					$patch['file'] = $topic;
				break;	

				case "advanced_recovery":
					$patch['file'] = $topic;
				break;					
				
				case "secretkey":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;

				case "itemshop_log":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;				
				
				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;					
			}
			
		break;
		
		case "character":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "create":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;
				
				case "view":
					$patch['file'] = $topic;
				break;			

				case "edit":
					$needLogin = true;		
					$patch['file'] = $topic;
				break;			

				case "delete":
					$needLogin = true;		
					$patch['file'] = $topic;
				break;		

				case "undelete":
					$needLogin = true;		
					$patch['file'] = $topic;
				break;		

				case "itemshop":
					$needLogin = true;		
					$patch['file'] = $topic;
				break;					

				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;					
			}
			
		break;		

		case "contribute":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "order":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;		

				case "confirm":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;				

				case "myorders":
					$needLogin = true;
					$patch['file'] = $topic;
				break;		

				case "accept":
					$needLogin = true;
					$patch['file'] = $topic;
				break;		

				/*case "import":
					$patch['file'] = $topic;
				break;*/						

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
		
		case "community":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "highscores":
					$patch['file'] = $topic;
				break;	
				
				case "lastdeaths":
					$patch['file'] = $topic;
				break;			

				case "houses":
					$patch['file'] = $topic;
				break;

				case "guilds":
					$patch['file'] = $topic;
				break;				
				
				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;					
			}
			
		break;	

		case "guilds":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "details":
					$patch['file'] = $topic;
				break;
				
				case "create":
					$needPremium = true;
					$patch['file'] = $topic;
				break;		

				case "edit":
					$needPremium = true;
					$patch['file'] = $topic;
				break;			

				case "ranks":
					$needPremium = true;
					$patch['file'] = $topic;
				break;		

				case "invite":
					$needPremium = true;
					$patch['file'] = $topic;
				break;	

				case "acceptInvite":
					$patch['file'] = $topic;
				break;		

				case "members":
					$needPremium = true;
					$patch['file'] = $topic;
				break;				

				case "passleadership":
					$needPremium = true;
					$patch['file'] = $topic;
				break;				
				
				case "disband":
					$needPremium = true;
					$patch['file'] = $topic;
				break;	

				case "leave":
					$patch['file'] = $topic;
				break;				
				
				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;
			}
			
		break;		
		
		case "status":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "whoisonline":
					$patch['file'] = $topic;
				break;

				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;
			}
			
		break;			
		
		case "general":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "howplay":
					$patch['file'] = $topic;
				break;	

				case "emailmarketing":
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
		$_isPremium = false;
		
		if($_SESSION['login'])
		{
			$accIsPremium = $core->loadClass("Account");	
			$accIsPremium->load($_SESSION["login"][0], "premdays");
			
			if($accIsPremium->get("premdays") != 0)
			{
				$_isPremium = true;
			}
		}
		
		
		if(($needLogin and !$_SESSION['login']) or ($needPremium and !$_SESSION['login']))
		{
			include("modules/errors/needlogin.php");
		}
		elseif($needPremium and !$_isPremium)
		{
			include("modules/errors/needpremium.php");
		}
		else
		{
			if($patch['dir'] != "errors")
				$patch['urlnavigation'] = "/ ".$patch['dir']." / <a href='?ref=".$patch['dir'].".".$patch['file']."'>".$patch['file']."</a>";
				
			include("modules/".$patch['dir']."/".$patch['file'].".php");			
		}
	}	
	else	
		include("modules/news/last.php");
}	
else
{
	$module = null;
	include("modules/errors/sqlinjection.php");	
}
	

?>