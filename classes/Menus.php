<?php
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
				,array("name" => "Conta Premium", "url" => "?ref=accunt.premium")			
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
				,array("name" => "Item Shop", "style" => "font-weight: bold", "url" => "?ref=itemshop.purchase")		
				,array("name" => "Minhas Compras", "url" => "?ref=contribute.myorders")		
			)		
		)
		,array(
			"title" => "Admin Panel",
			"name" => "adminpanel",
			"conditions" => Menu::CONDITION_MUST_LOGGED,
			"visibility_style" => e_MenuVisibilityStyle::DropDown,
			"min_group" => GROUP_GAMEMASTER,
			"items" => array(
				array("name" => "Notícia Rapida", "url" => "?ref=adv.fastnews")
				,array("name" => "Novo Tópico", "url" => "?ref=forum.newtopic", "min_group" => GROUP_COMMUNITYMANAGER)		
				//,array("name" => "Campanha de E-mail", "url" => "?ref=adv.emailcampaign" => GROUP_ADMINISTRATOR)		
			)		
		)
		,array(
			"title" => "Darghopédia",
			"name" => "darghopedia",
			"visibility_style" => e_MenuVisibilityStyle::DropDown,
			"items" => array(
				array("name" => "O Mapa", "url" => "?ref=darghopedia.world")
				,array("name" => "Criaturas", "url" => "?ref=darghopedia.monsterlist")
				,array("name" => "Quests e Dungeons", "url" => "?ref=darghopedia.quests")
				,array("name" => "PvP Arenas", "url" => "?ref=darghopedia.pvp_arenas")
				,array("name" => "Eventos Semanais", "url" => "?ref=darghopedia.week_events")			
			)		
		)		
		,array(
			"title" => "Comunidade",
			"name" => "community",
			"visibility_style" => e_MenuVisibilityStyle::DropDown,
			"items" => array(
				array("name" => "Buscar Personagem", "url" => "?ref=character.view")
				,array("name" => "Highscores", "url" => "?ref=community.highscores")
				,array("name" => "Guildas", "url" => "?ref=community.guilds")
				,array("name" => "Casas", "url" => "?ref=community.houses")
				,array("name" => "Mortes Recentes", "url" => "?ref=community.lastdeaths")			
				,array("name" => "Enquetes", "url" => "?ref=community.polls")			
				,array("name" => "Quem está online?", "url" => "?ref=community.whoisonline")			
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
			"title" => "Top 5 Matadores",
			"color" => e_menuColor::Red,
			"name" => "topkillers",
			"onDraw" => "drawTopKillers"
		)	
		,array(
			"title" => "Top Battleground Rating",
			"color" => e_menuColor::Red,
			"name" => "topbgrating",
			"onDraw" => "drawTopBgRating"
		)	
	);
	
	static function drawFacebook(SimpleXMLElement &$xml)
	{
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$li = $ul->addChild("li");
		
		$li->addChild("facebooktag");
		
		return true;
	}
	
	static function drawStatus(SimpleXMLElement &$xml)
	{
		$query = Core::$DB->query("SELECT `players`, `online`, `uptime`, `afk`, `date` FROM `serverstatus` ORDER BY `date` DESC LIMIT 1");
		$fetch = $query->fetch();		
		
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$li = $ul->addChild("li");
		$div = $li->addChild("div");

		$p = $div->addChild("p");
		$p->addChild("em", "Status: ");	
		
		if($fetch->online == 0 || $fetch->date < time - 60 * 5)
		{
			$span = $p->addChild("span", "offline");
			$span->addAttribute("style", "color: #ec0404; font-weight: bold;");
		}
		else
		{
			$seconds = $fetch->uptime % 60;
			$uptime = floor($fetch->uptime / 60);
	
			$minutes = $uptime % 60;
			$uptime = floor($uptime / 60);
	
			$hours = $uptime % 24;
			$uptime = floor($uptime / 24);
	
			$days = $uptime % 365;
			
			$uptime = ($days >= 1) ? "{$days}d {$hours}h {$minutes}m" : "{$hours}h {$minutes}m";
			
			$span = $p->addChild("span", "online");
			$span->addAttribute("style", "color: #00ff00; font-weight: bold;");			

			$p = $div->addChild("p");
			$p->addChild("em", "IP: ");
			$span = $p->addChild("span", STATUS_ADDRESS);

			$p = $div->addChild("p");
			$p->addChild("em", "Porta: ");
			$span = $p->addChild("span", STATUS_PORT);
			
			
			if(REMOVE_AFK_FROM_STATUS)
			{
				$p = $div->addChild("p");
				$p->addChild("em", "Players online: ");
				$span = $p->addChild("span", $fetch->players + $fetch->afk);		

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
				$p->addChild("em", "Players online: ");
				$span = $p->addChild("span", $fetch->players);
			}

			$p = $div->addChild("p");
			$p->addChild("em", "Uptime: ");
			$span = $p->addChild("span", $uptime);		
				
			$p = $div->addChild("p");
			$p->addChild("em", "Ping: ");
			$span = $p->addChild("span", "aguarde...");			
			$span->addAttribute("class", "ping");
		}		
		
		return true;
	}
	
	static function drawTopKillers(SimpleXMLElement &$xml)
	{
		$today = new CustomDate();	
		$end_day = null;
		
		if($today->getHour() > 15) $end_day = $today->getDay();
		else $end_day = $today->getDay() - 1;
		
		$start_day = $end_day - 1;
										
		$make_stamp = new CustomDate(); $make_stamp->_hour = 15; $make_stamp->_month = $today->getMonth(); $make_stamp->_day = $start_day; $make_stamp->_year = $today->getYear();
		
		$start_stamp = $make_stamp->makeDate();		
		
		$make_stamp->_day = $end_day;
		
		$end_stamp = $make_stamp->makeDate();						
		$query = Deaths::getTopFraggers($start_stamp, $end_stamp);	

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
	
	static function drawTopBgRating(SimpleXMLElement &$xml)
	{
		$today = new CustomDate();	
		$end_day = null;
		
		if($today->getHour() > 15) $end_day = $today->getDay();
		else $end_day = $today->getDay() - 1;
		
		$start_day = $end_day - 1;
										
		$make_stamp = new CustomDate(); $make_stamp->_hour = 15; $make_stamp->_month = $today->getMonth(); $make_stamp->_day = $start_day; $make_stamp->_year = $today->getYear();
		
		$start_stamp = $make_stamp->makeDate();		
		
		$make_stamp->_day = $end_day;
		
		$end_stamp = $make_stamp->makeDate();				
		$result = Battleground::buildRating(Battleground::listAll($start_stamp, $end_stamp));

		if(count($result) == 0)
			return false;
			
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$pos = 1;
		foreach($result as $key => $value)
		{
			$size = (strlen($value["name"]) > 15) ? "8px" : "9px";
			
			$li = $ul->addChild("li");
			$a = $li->addChild("a", "{$pos}. {$value["name"]} ({$value["rating"]})");
			$a->addAttribute("href", "?ref=character.view&name={$value["name"]}");
			$a->addAttribute("style", "font-size: {$size}");
			
			$pos++;
			
			if($pos > 5)
				break;
		}		
		
		return true;
	}
	
	static function listLeftMenu()
	{
		$query = Core::$DB->query("
		SELECT 
			`id`
		FROM 
			".Tools::getSiteTable("menus")."
		WHERE
			`position` = ".e_MenuPosition::Left."
			AND `hide`= 0
		ORDER BY
			`order`
			DESC
		");
		
		return new ResultIterator($query);		
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