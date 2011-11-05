<?php
class MenuItem
{
	private $menu_id, $item, $url, $conditions, $min_group, $style;
	
	function MenuItem($item, $url, $conditions, $min_group = 0, $style = "")
	{
		$this->item = $item;
		$this->url = $url;
		$this->conditions = $conditions;
		$this->min_group = $min_group;
		$this->style = $style;
	}
	
	function getMenuId() { return $this->menu_id; }
	function getItem() { return $this->item; }
	function getUrl() { return $this->url; }
	
	function setMenuId($menu_id) { $this->menu_id = $menu_id; } 
	function setItem($item) { $this->item = $item; } 
	function setUrl($url) { $this->url = $url; } 
	
	function __addToElement(SimpleXMLElement &$xml)
	{
		if(Tools::hasFlag($this->conditions, Menu::CONDITION_MUST_LOGGED) && !Core::isLogged())
			return "";		
		elseif(Tools::hasFlag($this->conditions, Menu::CONDITION_CAN_NOT_LOGGED) && Core::isLogged())
			return "";			
		
		$li = $xml->addChild("li");
		$a = $li->addChild("a");
		$span = $a->addChild("span", $this->getItem());
		$span->addAttribute("style", $this->style);
		
		$a->addAttribute("href", $this->getUrl());
	}
	
	
}