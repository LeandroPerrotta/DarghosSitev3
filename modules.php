<?
list($module, $topic) = explode(".", $_GET['ref']);

/*$noInjection = false;

if(!in_array($_GET['ref'], $_inputsWhiteList))
{
	if(Strings::filterInputs(true))
	{
		$noInjection = true;
	}	
}
else
{
	$noInjection = true;
}*/

/*if($noInjection)
{*/
	Strings::filterInputs(true);

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

				case "setname":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;					

				case "premiumtest":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;					

				case "importElerian":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;		

				case "prize":
					$needLogin = true;	
					$patch['file'] = $topic;
				break;			

				case "changename":
					$needLogin = true;
					$patch['file'] = $topic;
				break;			

				case "validateEmail":
					$needLogin = true;
					$patch['file'] = $topic;
				break;					
				
				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;					
			}
			
		break;
		
		case "darghopedia":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "world":
					$patch['file'] = $topic;
				break;				
				
				case "reborn":
					$patch['file'] = $topic;
				break;
				
				case "quests":
					$patch['file'] = $topic;
				break;						
				
				case "monsterlist":
					$patch['file'] = $topic;
				break;						
				
				case "monster":
					$patch['file'] = $topic;
				break;		

				case "pvp_arenas":
					$patch['file'] = $topic;
				break;			
						
				case "week_events":
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
				
				case "reborn":
					$needPremium = true;
					$needLogin = true;		
					$patch['file'] = $topic;	
				break;				
				

				case "stamina":
						if(ENABLE_BUY_STAMINA)
						{						
							$needPremium = true;
							$needLogin = true;		
							$patch['file'] = $topic;	
						}
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

				case "polls":
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

				case "invitereply":
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
					
				case "declarewar":
					$needPremium = true;
					$patch['file'] = $topic;
				break;		
				
				case "replywar":
					$needPremium = true;
					$patch['file'] = $topic;
				break;
				
				case "wardetail":
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

		case "tickets":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "send":
					$needLogin = true;
					$patch['file'] = $topic;
				break;

				case "tickets":
					$needLogin = true;
					$patch['file'] = $topic;
				break;
				
				case "view":
					$needLogin = true;
					$patch['file'] = $topic;
				break;
				
				case "close":
					$needLogin = true;
					$patch['file'] = $topic;
				break;		

				case "open":
					$needLogin = true;
					$patch['file'] = $topic;
				break;
				
				case "super_view":
					$needLogin = true;
					$patch['file'] = $topic;
				break;		
				
				case "super_list":
					$needLogin = true;
					$patch['file'] = $topic;
				break;		
				
				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;
			}
			
		break;
		
		case "forum":
		
			$patch['dir'] = $module;
		
			switch($topic)
			{
				case "topic":
					$patch['file'] = $topic;
				break;
				
				case "register":
					$needLogin = true;
					$patch['file'] = $topic;
				break;		
				
				case "newtopic":
					$patch['file'] = $topic;
					$needMinGroup = GROUP_COMMUNITYMANAGER;			
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
				
				case "about":
					$patch['file'] = $topic;
				break;	

				case "emailmarketing":
					$patch['file'] = $topic;
				break;					

				case "fansites":
					$patch['file'] = $topic;
				break;					
				
				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;					
			}
			
		break;
		
		case "adv":
		
			$patch['dir'] = $module;
			$needLogin = true;
			
			switch($topic)
			{
				case "fastnews":
					$patch['file'] = $topic;
					$needMinGroup = GROUP_COMMUNITYMANAGER;			
				break;			

				case "tutortest":
					$patch['file'] = $topic;
					$needMinGroup = GROUP_COMMUNITYMANAGER;			
				break;	

				case "addprize":
					$patch['file'] = $topic;
					$needMinGroup = GROUP_ADMINISTRATOR;			
				break;				
						
				case "emailcampaign":
					$patch['file'] = $topic;
					$needMinGroup = GROUP_ADMINISTRATOR;			
				break;						
				
				default:
					$patch['dir'] = "errors";
					$patch['file'] = "notfound";
				break;					
			}
			
		break;	

	
		case "itemshop":
		{
			$patch['dir'] = $module;		
			
			if(SHOW_SHOPFEATURES == 1)
			{
				if(file_exists("modules/{$module}/{$topic}.php"))
				{
					$patch['file'] = $topic;
					break;
				}
			}
			
			$patch['dir'] = "errors";
			$patch['file'] = "notfound";
			break;
		}
		
		
		default:
			$patch['dir'] = "errors";
			$patch['file'] = "notfound";
		break;	
	}

	$module = null;
	
	if($_GET)
	{	
		$_isPremium = false;
		$_groupId = GROUP_PLAYERS;
		
		if($_SESSION['login'])
		{
			include_once('classes/account.php');
			$checkAccount = new Account;	
			$checkAccount->load($_SESSION["login"][0]);
			
			if($checkAccount->get("premdays") != 0)
			{
				$_isPremium = true;
			}
			
			$_groupId = $checkAccount->getGroup();
		}
		
		if(($needLogin and !$_SESSION['login']) or ($needPremium and !$_SESSION['login']))
		{
			include("modules/errors/needlogin.php");
		}
		elseif($needPremium and !$_isPremium)
		{
			include("modules/errors/needpremium.php");
		}
		elseif($_groupId < $needMinGroup)
		{
			include("modules/errors/notfound.php");
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
/*}	
else
{
	$module = null;
	include("modules/errors/sqlinjection.php");	
}*/

?>
