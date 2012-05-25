<?php
namespace Core;
class Menus
{	
	private static $leftMenu = array(
		array(
			"title" => "Navegação",
			"name" => "navigation",
			"items" => array(
				array("name" => "Ultimas Notícias", "url" => "?ref=news.last")
				,array("name" => "Sobre o Darghos", "url" => "?ref=general.about")
				,array("name" => "Como jogar?", "url" => "?ref=general.howplay")
				,array("name" => "Perguntas Frequentes", "url" => "?ref=general.faq")
				,array("name" => "Fansites", "url" => "?ref=general.fansites")	
			)
		)
		,array(
			"title" => "Contas",
			"name" => "accounts",
			"conditions" => Menu::CONDITION_CAN_NOT_LOGGED,
			"items" => array(
				array("name" => "Criar Conta", "style" => "font-weight: bold",  "url" => "?ref=account.register")
				,array("name" => "Login", "url" => "?ref=account.login")
				,array("name" => "Recuperar Conta", "url" => "?ref=account.recovery")
				,array("name" => "Leilão de Items", "style" => "font-weight: bold", "url" => "?ref=auctions.index")
				,array("name" => "Conta Premium", "url" => "?ref=account.premium")			
			)		
		)
		,array(
			"title" => "Minha Conta",
			"name" => "myaccount",
			"conditions" => Menu::CONDITION_MUST_LOGGED,
			"items" => array(
				array("name" => "Principal", "url" => "?ref=account.main")
				,array("name" => "Logout", "url" => "?ref=account.logout")		
			)		
		)
		,array(
			"title" => "Conta Premium",
			"name" => "premium",
			"conditions" => Menu::CONDITION_MUST_LOGGED,
			"items" => array(
				array("name" => "Vantagens", "url" => "?ref=account.premium")
				,array("name" => "Comprar!", "style" => "font-weight: bold", "url" => "?ref=contribute.order")		
				,array("name" => "Leilão de Items", "style" => "font-weight: bold", "url" => "?ref=auctions.index")
				,array("name" => "Item Shop", "url" => "?ref=itemshop.purchase")		
				,array("name" => "Minhas Compras", "url" => "?ref=contribute.myorders")
			)		
		)
		,array(
			"title" => "Admin Panel",
			"name" => "adminpanel",
			"conditions" => Menu::CONDITION_MUST_LOGGED,
			"visibility_style" => \e_MenuVisibilityStyle::DropDown,
			"min_group" => \t_Group::GameMaster,
			"items" => array(
				array("name" => "Notícia Rapida", "url" => "?ref=adv.fastnews")
				,array("name" => "Novo Tópico", "url" => "?ref=forum.newtopic", "min_group" => \t_Group::CommunityManager)		
				,array("name" => "Partidas BG", "url" => "?ref=adv.bg_matches", "min_group" => \t_Group::CommunityManager)		
				//,array("name" => "Campanha de E-mail", "url" => "?ref=adv.emailcampaign" => \t_Group::Administrator)		
			)		
		)
		,array(
			"title" => "Darghopédia",
			"name" => "darghopedia",
			"visibility_style" => \e_MenuVisibilityStyle::DropDown,
			"items" => array(
				array("name" => "O Mapa", "url" => "?ref=darghopedia.world")
				,array("name" => "Darghos Wikia", "url" => "http://pt-br.darghos.wikia.com/wiki/Wiki_Darghos")
				,array("name" => "Criaturas", "url" => "?ref=darghopedia.monsterlist")
				,array("name" => "Quests e Dungeons", "url" => "?ref=darghopedia.quests")
				,array("name" => "Agressivos e Pacificos", "url" => "?ref=darghopedia.change_pvp")
				,array("name" => "Battlegrounds", "url" => "http://pt-br.darghos.wikia.com/wiki/Battlegrounds")
				,array("name" => "PvP Arenas", "url" => "?ref=darghopedia.pvp_arenas")
				,array("name" => "Eventos Semanais", "url" => "?ref=darghopedia.week_events")			
			)		
		)		
		,array(
			"title" => "Comunidade",
			"name" => "community",
			"visibility_style" => \e_MenuVisibilityStyle::DropDown,
			"items" => array(
				array("name" => "Buscar Personagem", "url" => "?ref=character.view")
				,array("name" => "Highscores", "url" => "?ref=community.highscores")
				,array("name" => "Guildas", "url" => "?ref=community.guilds")
				,array("name" => "Casas", "url" => "?ref=community.houses")
				,array("name" => "Mortes Recentes", "url" => "?ref=community.lastdeaths")			
				,array("name" => "Enquetes", "url" => "?ref=community.polls")			
				,array("name" => "Quem está online?", "url" => "?ref=status.whoisonline")			
			)		
		)		
		,array(
			"title" => "Facebook",
			"name" => "facebook",
			"onDraw" => "drawFacebook"
		)		
	);
	
	private static $rightMenu = array(
		array(
			"title" => "Server Status",
			"name" => "serverstatus",
			"onDraw" => "drawStatus"
		)
		,array(
			"title" => "Test Server Publico",
			"name" => "testserverstatus",
			"onDraw" => "drawTestServerStatus"
		)
		,array(
				"title" => "Power Gammers",
				"color" => \e_menuColor::Red,
				"name" => "powergammers",
				"onDraw" => "drawPowerGammers"
		)			
		,array(
			"title" => "Top 5 Matadores",
			"color" => \e_menuColor::Red,
			"name" => "topkillers",
			"onDraw" => "drawTopKillers"
		)	
		,array(
			"title" => "Battleground Semana",
			"color" => \e_menuColor::Red,
			"name" => "bestbgplayers",
			"onDraw" => "drawBgBest"
		)	
	);
	
	static function drawFacebook(&$xml)
	{
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$li = $ul->addChild("li");
		
		$li->addChild("facebooktag");
		
		return true;
	}
	
	static function drawTestServerStatus(&$xml)
	{
		if(!Configs::Get(Configs::eConf()->STATUS_SHOW_TEST_SERVER))
			return false;
		
		$testIp = "testserver.darghos.com.br";
		$testPort = "7171";
		
		$server = new \OTS_ServerInfo($testIp, $testPort);
		$status = $server->info(\OTS_ServerStatus::REQUEST_MISC_SERVER_INFO | \OTS_ServerStatus::REQUEST_PLAYERS_INFO);	

		if(!$status)
			return false;
			
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$li = $ul->addChild("li");
		$div = $li->addChild("div");

		$p = $div->addChild("p");
		$p->addChild("em", "Status: ");		
		$span = $p->addChild("span", "online");	
		$span->addAttribute("style", "color: #00ff00; font-weight: bold;");		
		
		$p = $div->addChild("p");
		$p->addChild("em", "IP: ");
		$span = $p->addChild("span", $testIp);	

		$p = $div->addChild("p");
		$p->addChild("em", "Porta: ");
		$span = $p->addChild("span", $testPort);
		
		return true;
	}
	
	static function drawStatus(&$xml)
	{
		if(Configs::Get(Configs::eConf()->ENABLE_MANUTENTION))
			return false;
		
		$status = array();
		$allOnline = true;
		
		while(\t_Worlds::ItValid())
		{
			$world_id = \t_Worlds::It();

			$query = Main::$DB->query("SELECT `players`, `online`, `date` FROM `serverstatus` WHERE `server_id` = {$world_id} ORDER BY `date` DESC LIMIT 1");
			$fetch = $query->fetch();			
			
			if((bool)!$fetch->online || $fetch->date < time - 60 * 5)
			{
				if(Configs::Get(Configs::eConf()->ENABLE_MULTIWORLD))
					$allOnline = false;
				
				\t_Worlds::ItNext();
				continue;
			}
			
			$status[$world_id] = $fetch->players;
			\t_Worlds::ItNext();
		}
		

		
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$li = $ul->addChild("li");
		$div = $li->addChild("div");

		$p = $div->addChild("p");
		$p->addChild("em", "Status: ");	
		
		if(count($status) == 0)
		{
			$span = $p->addChild("span", "offline");
			$span->addAttribute("style", "color: #ec0404; font-weight: bold;");
		}
		else
		{			
			$span = $p->addChild("span", "online");
			if($allOnline)
				$span->addAttribute("style", "color: #00ff00; font-weight: bold;");
			else
				$span->addAttribute("style", "color: #e1dc48; font-weight: bold;");

			$p = $div->addChild("p");
			$p->addChild("em", "IP: ");
			$span = $p->addChild("span", Configs::Get(Configs::eConf()->STATUS_HOST));

			$p = $div->addChild("p");
			$p->addChild("em", "Porta: ");
			$span = $p->addChild("span", Configs::Get(Configs::eConf()->STATUS_PORT));
			
			
			if(Configs::Get(Configs::eConf()->STATUS_IGNORE_AFK))
			{
				$p = $div->addChild("p");
				$em = $p->addChild("em");
				$a = $em->addChild("a", "Players online:");
				$a->addAttribute("href", "?ref=status.whoisonline");
				$span = $p->addChild("span", " " . $fetch->players + $fetch->afk);		

				$p = $div->addChild("p");
				$p->addChild("em", "Playing: ");
				$span = $p->addChild("span", $fetch->players);		
						
				$p = $div->addChild("p");
				$p->addChild("em", "Training: ");
				$span = $p->addChild("span", $fetch->afk);				

			}
			else
			{
				$p = $div->addChild("p");
				$em = $p->addChild("em");
				$a = $em->addChild("a", "Players online:");
				$a->addAttribute("href", "?ref=status.whoisonline");
				$span = $p->addChild("span", " " . array_sum($status));
			}
				
			if(Configs::Get(Configs::eConf()->STATUS_SHOW_PING))
			{
				$p = $div->addChild("p");
				$p->addChild("em", "Ping: ");
				$span = $p->addChild("span", "aguarde...");			
				$span->addAttribute("class", "ping");
			}
		}		
		
		return true;
	}
	
	static function drawPowerGammers(&$xml)
	{
		$today = new CustomDate();
		$end_day = null;
		
		if($today->getHour() > 10) $end_day = $today->getDay();
		else $end_day = $today->getDay() - 1;	

		$start_day = $end_day - 1;
		
		$make_stamp = new CustomDate(); $make_stamp->_hour = 10; $make_stamp->_month = $today->getMonth(); $make_stamp->_day = $start_day; $make_stamp->_year = $today->getYear();
		$start_stamp = $make_stamp->makeDate();
		
		$make_stamp->_day = $end_day;
		$end_stamp = $make_stamp->makeDate();
		
		$query = Main::$DB->query("SELECT `p`.`name`, `p`.`level`, SUM(`a`.`experience_logout`) - SUM(`a`.`experience`) as `change_exp` FROM `player_activities` `a` LEFT JOIN `players` `p` ON `p`.`id` = `a`.`player_id` WHERE `a`.`login` >= {$start_stamp} AND `a`.`login` < {$end_stamp} GROUP BY `a`.`player_id` ORDER BY `change_exp` DESC LIMIT 5");
		
		if($query->numRows() == 0)
			return false;
		
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");
		
		$pos = 1;
		while($fetch = $query->fetch())
		{
			$size = (strlen($fetch->name) > 15) ? "8px" : "9px";
				
			$li = $ul->addChild("li");
			$a = $li->addChild("a", "{$pos}. {$fetch->name} ({$fetch->level}, +{$fetch->change_exp})");
			$a->addAttribute("href", "?ref=character.view&name={$fetch->name}");
			$a->addAttribute("style", "font-size: {$size}");
				
			$pos++;
		}
		
		return true;		
	}
	
	static function drawTopKillers(&$xml)
	{
		if(Configs::Get(Configs::eConf()->ENABLE_MANUTENTION))
			return true;		
		
		$today = new CustomDate();	
		$end_day = null;
		
		if($today->getHour() > 15) $end_day = $today->getDay();
		else $end_day = $today->getDay() - 1;
		
		$start_day = $end_day - 1;
										
		$make_stamp = new CustomDate(); $make_stamp->_hour = 15; $make_stamp->_month = $today->getMonth(); $make_stamp->_day = $start_day; $make_stamp->_year = $today->getYear();
		
		$start_stamp = $make_stamp->makeDate();
		
		$make_stamp->_day = $end_day;
		
		$end_stamp = $make_stamp->makeDate();						
		$query = \Framework\Deaths::getTopFraggers($start_stamp, $end_stamp);	

		if($query->numRows() == 0)
			return false;
			
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$pos = 1;
		while($fetch = $query->fetch())
		{
			$size = (strlen($fetch->name) > 15) ? "8px" : "9px";
			
			$li = $ul->addChild("li");
			$a = $li->addChild("a", "{$pos}. {$fetch->name} ($fetch->c)");
			$a->addAttribute("href", "?ref=character.view&name={$fetch->name}");
			$a->addAttribute("style", "font-size: {$size}");
			
			$pos++;
		}		

		return true;
	}
	
	static function drawTopBgRating(&$xml)
	{			
		if(Configs::Get(Configs::eConf()->ENABLE_MANUTENTION))
			return true;		
		
		$result = \Framework\Player::listByBestRating();

		if($result->numRows() == 0)
			return false;
			
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$pos = 1;
		while($fetch = $result->fetch())
		{
			$size = (strlen($fetch->name) > 15) ? "8px" : "9px";
			
			$li = $ul->addChild("li");
			$a = $li->addChild("a", "{$pos}. {$fetch->name} ({$fetch->battleground_rating})");
			$a->addAttribute("href", "?ref=character.view&name={$fetch->name}");
			$a->addAttribute("style", "font-size: {$size}");			
			
			$pos++;
		}		
		
		return true;
	}
	
	static function drawBgBest(&$xml)
	{
		if(Configs::Get(Configs::eConf()->ENABLE_MANUTENTION))
			return true;
	
		$array = json_decode(Main::readTempFile("bgbest.json"));
	
		if(count($array) < 5)
			return false;
			
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");
	
		$pos = 1;
		foreach($array as $info)
		{
			$player = new \Framework\Player();
			$player->load($info->player_id).
			$size = (strlen($player->getName()) > 15) ? "8px" : "9px";
				
			$li = $ul->addChild("li");
			$a = $li->addChild("a", "{$pos}. {$player->getName()} ({$info->wins} / {$info->losses})");
			$a->addAttribute("href", "?ref=character.view&name={$player->getName()}");
			$a->addAttribute("style", "font-size: {$size}");
				
			$pos++;
			
			if($pos > 10)
				break;
		}
	
		return true;
	}	
	
	static function listLeftMenu()
	{
		$query = Main::$DB->query("
		SELECT 
			`id`
		FROM 
			".Tools::getSiteTable("menus")."
		WHERE
			`position` = ".\e_MenuPosition::Left."
			AND `hide`= 0
		ORDER BY
			`order`
			DESC
		");
		
		return new ResultIterator($query);		
	}
	
	static function drawTopMenu()
	{
		$xml = new \SimpleXMLElement("
				<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
				<root>
				</root>");

		$ul = $xml->addChild("ul");
		
		$li = $ul->addChild("li");
		$li->addAttribute("id", "lastnews");
		$link = $li->addChild("a");
		$link->addAttribute("href", "?ref=news.last");
		
		if(!Main::isLogged())
		{
			$li = $ul->addChild("li");
			$li->addAttribute("id", "login");
			$link = $li->addChild("a");
			$link->addAttribute("href", "?ref=account.login");			

			$li = $ul->addChild("li");
			$li->addAttribute("id", "register");
			$link = $li->addChild("a");
			$link->addAttribute("href", "?ref=account.register");				
		}
		else
		{
			$li = $ul->addChild("li");
			$li->addAttribute("id", "logout");
			$link = $li->addChild("a");
			$link->addAttribute("href", "?ref=account.logout");

			$li = $ul->addChild("li");
			$li->addAttribute("id", "account");
			$link = $li->addChild("a");
			$link->addAttribute("href", "?ref=account.main");			
		}
		
		$li = $ul->addChild("li");
		$li->addAttribute("id", "highscores");
		$link = $li->addChild("a");
		$link->addAttribute("href", "?ref=community.highscores");		
		
		$li = $ul->addChild("li");
		$li->addAttribute("id", "auctions");
		$link = $li->addChild("a");
		$link->addAttribute("href", "?ref=auctions.index");

		return $xml->asXML();
	}
	
	static function drawLeftMenu()
	{		
		$string = "";
		
		foreach(self::$leftMenu as $node)
		{
			
			$menu = new Menu($node);
			$string .= $menu->__toXML();
		}
		
		$facebooktag = '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like-box href="http://www.facebook.com/pages/Darghos/205124342834613" width="180" height="345" style="margin-top: 0px; border: none;" colorscheme="dark" show_faces="true" border_color="" stream="false" header="false"></fb:like-box>';
		$string = str_replace("<facebooktag></facebooktag>", $facebooktag, $string);
		
		return $string;
	}
	
	static function drawRightMenu()
	{		
		$string = "";
		
		foreach(self::$rightMenu as $node)
		{
			$menu = new Menu($node);
			$string .= $menu->__toXML();
		}
		
		return $string;
	}	
}
