<?
use \Core\Configs as g_Configs;
list($module, $topic) = explode(".", $_GET['ref']);

if(!g_Configs::Get(g_Configs::eConf()->ENABLE_MANUTENTION))
{
	Core\Strings::filterInputs(true);
	
	$needLogin = false;
	$needPremium = false;
	$patch = array();

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

				case "logout":
					$needLogin = true;
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
				case "monsterlist":
					$patch['file'] = $topic;
				break;						
				
				case "monster":
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
						if(g_Configs::Get(g_Configs::eConf()->ENABLE_STAMINA_REFILER))
						{						
							$needPremium = true;
							$needLogin = true;		
							$patch['file'] = $topic;	
						}
				break;
								
				case "removeSkull":
					if(g_Configs::Get(g_Configs::eConf()->ENABLE_REMOVE_SKULLS))
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
					//$needPremium = true;
					$patch['file'] = $topic;
				break;			

				case "ranks":
					//$needPremium = true;
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
					//$needPremium = true;
					$patch['file'] = $topic;
				break;				

				case "passleadership":
					//$needPremium = true;
					$patch['file'] = $topic;
				break;				
				
				case "disband":
					//$needPremium = true;
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
					$needMinGroup = t_Group::CommunityManager;			
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
					$needMinGroup = t_Group::CommunityManager;			
				break;			

				case "gold_check":
					$patch['file'] = $topic;
					$needMinGroup = t_Group::CommunityManager;			
				break;	
				
				case "bg_matches":
					$patch['file'] = $topic;
					$needMinGroup = t_Group::CommunityManager;			
				break;	

				case "addprize":
					$patch['file'] = $topic;
					$needMinGroup = t_Group::Administrator;			
				break;				
						
				case "emailcampaign":
					$patch['file'] = $topic;
					$needMinGroup = t_Group::Administrator;			
				break;			

				case "depot_merger":
					$patch['file'] = $topic;
					$needMinGroup = t_Group::Administrator;
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
			
			if(g_Configs::Get(g_Configs::eConf()->ENABLE_ITEM_SHOP) && !g_Configs::Get(g_Configs::eConf()->DISABLE_ALL_PREMDAYS_FEATURES))
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
		$_groupId = t_Group::Player;
		
		$checkAccount = \Framework\Account::loadLogged();
		
		if($checkAccount)
		{			
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
				
			if($patch['dir'] == "errors")
			{
				if(!$checkAccount || ($checkAccount && $checkAccount->getAccess() < t_Access::Administrator))
				{
					include("modules/".$patch['dir']."/".$patch['file'].".php");		
					\Core\Main::$FoundController = true;
				}
			}
			else
			{
				include("modules/".$patch['dir']."/".$patch['file'].".php");
				\Core\Main::$FoundController = true;				
			}
		}
	}	
	else	
		include("modules/news/last.php");
}
else
{
		$patch['urlnavigation'] = "/manutenção";
	
		$module .= "
		<div style='margin-top: 16px;' id='new-title-bar'>
			<h3 id='new-title'>
				".g_Configs::Get(g_Configs::eConf()->MANUTENTION_TITLE)."
			</h3>
			<div id='infos-line'>
			</div>	
		</div>
		<div id='new-summary'>".g_Configs::Get(g_Configs::eConf()->MANUTENTION_BODY)."</div>";
}

?>
