<?php
namespace Core;
class MenuItem
{
	private $menu_id, $item, $url, $conditions, $min_group, $style, $icon;
	
	function __construct($item, $url, $conditions, $min_group = 0, $style = "", $icon = "")
	{
		$this->item = $item;
		$this->url = $url;
		$this->conditions = $conditions;
		$this->min_group = $min_group;
		$this->style = $style;
		$this->icon = $icon;
	}
	
	function getMenuId() { return $this->menu_id; }
	function getItem() { return $this->item; }
	function getUrl() { return $this->url; }
	
	function setMenuId($menu_id) { $this->menu_id = $menu_id; } 
	function setItem($item) { $this->item = $item; } 
	function setUrl($url) { $this->url = $url; } 
	
	function __addToElement(&$xml)
	{
		if(Tools::hasFlag($this->conditions, Menu::CONDITION_MUST_LOGGED) && !Main::isLogged())
			return "";		
		elseif(Tools::hasFlag($this->conditions, Menu::CONDITION_CAN_NOT_LOGGED) && Main::isLogged())
			return "";			
		elseif(Tools::hasFlag($this->conditions, Menu::CONDITION_SHOWING_PLAYERS_ONLINE) && !Configs::Get(Configs::eConf()->ENABLE_PLAYERS_ONLINE) && (!Main::isLogged() || \Framework\Account::loadLogged()->getAccess() < \t_Group::GameMaster))
			return "";
		
		$li = $xml->addChild("li");
		

		
		$a = $li->addChild("a");
		
		$span = $a->addChild("span", $this->getItem());
		$span->addAttribute("style", $this->style);
		
		if($this->icon){
		    $icon = $a->addChild("span");
		    $icon->addAttribute("class", $this->icon);
		}		
		
		$a->addAttribute("href", $this->getUrl());
	}
	
	
}