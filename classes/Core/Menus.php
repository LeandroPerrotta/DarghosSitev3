<?php
namespace Core;
class Menus
{	
	private static $leftMenu;
	private static $rightMenu;
	
	static function drawMedia(&$xml)
	{
		$ul = $xml->addChild("ul");
		$ul->addAttribute("class", "always_viewable");	

		$li = $ul->addChild("li");
		
		$li->addChild("mediatag");
		
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
		$p->addChild("em", tr("".getConf(confEnum()->WEBSITE_NAME)." está "));	
		
		if(count($status) == 0)
		{
			$span = $p->addChild("span", tr("offline"));
			$span->addAttribute("style", "color: #ec0404; font-weight: bold;");
		}
		else
		{			
			$span = $p->addChild("span", tr("online"));
			if($allOnline)
				$span->addAttribute("style", "color: #00ff00; font-weight: bold;");
			else
				$span->addAttribute("style", "color: #e1dc48; font-weight: bold;");

			$p = $div->addChild("p");
			$p->addChild("em");	
			
			if(Configs::Get(Configs::eConf()->STATUS_IGNORE_AFK))
			{
				$p = $div->addChild("p");
				$em = $p->addChild("em");
				$a = $em->addChild("a", tr("Jogadores:"));
				$a->addAttribute("href", "?ref=status.whoisonline");
				$span = $p->addChild("span", " " . $fetch->players + $fetch->afk);		

				$p = $div->addChild("p");
				$p->addChild("em", tr("Jogando: "));
				$span = $p->addChild("span", $fetch->players);		
						
				$p = $div->addChild("p");
				$p->addChild("em", tr("Treinando: "));
				$span = $p->addChild("span", $fetch->afk);				

			}
			else
			{
				if(Configs::Get(Configs::eConf()->ENABLE_PLAYERS_ONLINE))
				{
					$p = $div->addChild("p");
					$em = $p->addChild("em");
					$a = $em->addChild("a", tr("Jogadores:"));
					$a->addAttribute("href", "?ref=status.whoisonline");
					$span = $p->addChild("span", " " . array_sum($status));
				}
			}
				
			if(Configs::Get(Configs::eConf()->STATUS_SHOW_PING))
			{
				$p = $div->addChild("p");
				$p->addChild("em", tr("Ping: "));
				$span = $p->addChild("span", tr("aguarde..."));			
				$span->addAttribute("class", "ping");
			}
		}		
		
		return true;
	}
	
	static function drawEvents(\SimpleXMLElement &$xml){
	    
	    $today = \Framework\Events::getToday();
	    
	    $ul = $xml->addChild("ul");
	    $ul->addAttribute("class", "always_viewable");
	    
	    $li = $ul->addChild("li"); 
	    
	    $div = $li->addChild("div");
	    $div->addAttribute("class", "events");
	    $div->addChild("h3", "Hoje");
	    
	    if($today->numRows() > 0){

	    }
	    else{
	        $div->addChild("p", "Nenhum evento programado :(");
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
	
	static function drawTopBar()
	{
	    /*
	     * 	            <!--
					<div id=\"games-box\" style='visibility: hidden;'>
						<form action=\"?ref=account.login\" method=\"post\" >
							<fieldset>
								<label>Conheça também...</label>
								<select name=\"game_options\" id=\"game_options\">
									<option selected=\"selected\"></option>
									<option value=\"http://ultraxsoft.com/ot\">UltraX (Open Tibia)</option>
									<option value=\"http://www.darghos.com.br\">Darghos (MMORPG)</option>
								</select>
							</fieldset>
						</form>
					</div>
		            -->
	     * */
	    
		$xml = new \SimpleXMLElement("
				<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
				<root>
                    <div id=\"language-box\">
						<form action=\"?ref=misc.language\" method=\"post\" >
							<fieldset>
								<label style='margin-top: 0px; line-height: 16px;'>" . tr("Seu idioma") . "</label>
								<select name=\"language\" id=\"language\" style='width: 125px;' onchange='this.form.submit()'>
									<option selected=\"selected\"></option>					
									<option value=\"en\">". tr("Inglês") ."</option>
	                                <option value=\"pt-br\">". tr("Português") ."</option>
								</select>
							</fieldset>
						</form>
					</div>		        
					<div id=\"ultraxsoft-box\">
					</div>				
				</root>");
		
		$div = $xml->addChild("div");
		$div->addAttribute("id", "login");		
		
		if(!Main::isLogged())
		{			
			$form = $div->addChild("form");
			$form->addAttribute("action", "?ref=account.login");
			$form->addAttribute("method", "post");
			
			$fieldset = $form->addChild("fieldset");
			$label = $fieldset->addChild("label", tr("Acesse sua conta... Ou crie uma "));
			$a = $label->addChild("a", tr("nova conta!"));
			$a->addAttribute("href", "?ref=account.register");
			
			$account_name = $fieldset->addChild("input");
			$account_name->addAttribute("name", "login_name");
			$account_name->addAttribute("size", "15");
			$account_name->addAttribute("type", "password");
			
			$account_password = $fieldset->addChild("input");
			$account_password->addAttribute("name", "login_password");
			$account_password->addAttribute("size", "15");
			$account_password->addAttribute("type", "password");
			
			$login = $fieldset->addChild("input");
			$login->addAttribute("class", "button");
			$login->addAttribute("value", tr("Entrar"));
			$login->addAttribute("type", "submit");
		}
		else
		{
			$div->addChild("p", tr("Você está conectado!"));
			$p = $div->addChild("p");
			$a = $p->addChild("a", tr("Sua Conta"));
			$a->addAttribute("href", "?ref=account.main");	

			$p = $div->addChild("p");
			$a = $p->addChild("a", tr("Desconectar"));
			$a->addAttribute("href", "?ref=account.logout");			
		}
		
		return $xml->asXML();
	}
	
	static function drawTopMenu()
	{
		/*
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
		*/
	    return "";
	}
	
	static function drawUtils(&$xml){
	    $ul = $xml->addChild("ul");
	    $ul->addAttribute("class", "always_viewable");
	    
	    $li = $ul->addChild("li");
	    
	    $li->addChild("utils"); 
	    
	    return true;
	}
	
	static function drawLeftMenu()
	{		
		$string = "";
		
		self::$leftMenu = array(
            array(
    			"title" => "Navegação",
    			"name" => "navigation",
    			"items" => array(
    				array("name" => 'Ultimas Notícias', "url" => "?ref=news.last")
    				,array("name" => "Serverinfo", "url" => "?ref=general.about")
    				//,array("name" => "Downloads", "url" => "?ref=general.downloads")
    				//,array("name" => "Darghos Tunnel", "url" => "?ref=tunnel.about")
    				,array("name" => "Perguntas Frequentes", "url" => "?ref=general.faq")
    				,array("name" => "Suporte", "url" => "?ref=general.support")			        
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
    				//,array("name" => "Leilão de Items", "style" => "font-weight: bold", "url" => "?ref=auctions.index")
    				//,array("name" => "Conta Premium", "url" => "?ref=account.premium")			
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
    			"title" => "Loja ".getConf(confEnum()->WEBSITE_NAME)."",
    			"name" => "premium",
    			"conditions" => Menu::CONDITION_MUST_LOGGED,
    			"items" => array(
    				array("name" => "Premium Account", "url" => "?ref=account.premium", "icon" => "icon-star", "style" => "color: #FFF500; font-weight: bold;")
    				,array("name" => "+Saldo", "style" => "font-weight: bold", "url" => "?ref=balance.purchase")		
    				//,array("name" => "Leilão de Items", "style" => "font-weight: bold", "url" => "?ref=auctions.index")
    				,array("name" => "Loja ".getConf(confEnum()->WEBSITE_NAME)."", "url" => "?ref=store.purchase")		
    				,array("name" => "Leilão ".getConf(confEnum()->WEBSITE_NAME)."", "url" => "?ref=auctions.index")		
    				,array("name" => "Historico", "url" => "?ref=balance.history")
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
    				,array("name" => "Liberar Saldo", "url" => "?ref=adv.add_balance", "min_group" => \t_Group::Administrator)		
    				,array("name" => "Estatisticas", "url" => "?ref=adv.statistics", "min_group" => \t_Group::Administrator)		
    				,array("name" => "Idiomas", "url" => "?ref=adv.translations", "min_group" => \t_Group::Administrator)		
    				//,array("name" => "Partidas BG", "url" => "?ref=adv.bg_matches", "min_group" => \t_Group::CommunityManager)		
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
			        ,array("name" => "Darghos Suporte", "url" => "http://suporte-darghos.webnode.com/")
    				,array("name" => "Criaturas", "url" => "?ref=darghopedia.monsterlist")
			        ,array("name" => "Battleground", "url" => "?ref=general.battleground", "icon" => "icon-exclamation")
    				//,array("name" => "Quests e Dungeons", "url" => "?ref=darghopedia.quests")
    				//,array("name" => "Agressivos e Pacificos", "url" => "?ref=darghopedia.change_pvp")
    				//,array("name" => "PvP Arenas", "url" => "?ref=darghopedia.pvp_arenas")
    				//,array("name" => "Eventos Semanais", "url" => "?ref=darghopedia.week_events")			
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
    				,array("name" => "Quem está online?", "url" => "?ref=status.whoisonline", "conditions" => Menu::CONDITION_SHOWING_PLAYERS_ONLINE)	
    			)		
    		)	
		);
		
		foreach(self::$leftMenu as $node)
		{
			
			$menu = new Menu($node);
			$string .= $menu->__toXML();
		}
		
		return $string;
	}
	
	static function drawRightMenu()
	{		
		$string = "";
		
		self::$rightMenu = array(
            array(
                "title" => "Redes Sociais",
                "name" => "social-media",
                "onDraw" => "drawMedia"
            ),
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
            /*,array(
                    "title" => "Eventos",
                    "desc" => "Confira todos eventos que estão rolando no Darghos.",
                    "name" => "events",
                    "onDraw" => "drawEvents"
            )*/
	        ,array(
                "title" => "Utéis",
                "desc" => "Recursos úteis aos jogadores.",
                "color" => \e_menuColor::Red,
                "name" => "utils",
                "onDraw" => "drawUtils"
	        )		        
    		,array(
    			"title" => "Power Gammers",
    			"desc" => "Jogadores que mais obtiveram expêriencia. Atualizado diariamente as 10:00.",
    			"color" => \e_menuColor::Red,
    			"name" => "powergammers",
    			"onDraw" => "drawPowerGammers"
    		)
    		,array(
    				"title" => "Battlegrounds Semana",
    				"desc" => "Jogadores com melhor desempenho em Battlegrounds na ultima semana (vitorias / derrotas). Atualizado toda terça feira as 10:00.",
    				"color" => \e_menuColor::Red,
    				"name" => "bestbgplayers",
    				"onDraw" => "drawBgBest"
    		)					
    		,array(
    			"title" => "Top 5 Matadores",
    			"desc" => "Jogadores que mais mataram outros jogadores. Atualizado diariamente as 16:00.",
    			"color" => \e_menuColor::Red,
    			"name" => "topkillers",
    			"onDraw" => "drawTopKillers"
    		)		
    	);
		
		foreach(self::$rightMenu as $node)
		{
			$menu = new Menu($node);
			$string .= $menu->__toXML();
		}
        
        $mediatag = '
        <div>
            <ul class="social-media">
              <li class="facebook"><a href="https://facebook.com/DarghosOT" title="Darghos no Facebook"></a></li>
            </ul>
        </div>';
        
        $string = str_replace("<mediatag></mediatag>", $mediatag, $string);
        
        $utilstag = '
        <div>
            <ul class="utils">
                <li>
                    <a href="?ref=general.client" class="not-menu">
                        <img src="newlay/images/icon_client.png" width="60px"/>
                        <span>Cliente Darghos</span>
                    </a>
                </li>
                <li>
                    <a href="?ref=general.teamspeak" class="not-menu">
                        <img src="newlay/images/icon_ts.png" width="60px"/>
                        <span>TeamSpeak 3</span>
                    </a>
                </li>
            </ul>
        </div>';
        
        $string = str_replace("<utils></utils>", $utilstag, $string);        
		
		return $string;
	}	
}
