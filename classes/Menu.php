<?php
class Menu
{	
	const CONDITION_MUST_LOGGED = 1;
	const CONDITION_CAN_NOT_LOGGED = 2;
	
	static function getPositionName($position)
	{			
		switch($position)
		{
			case e_MenuPosition::Left:
				return "esquerda";
				
			case e_MenuPosition::Right:
				return "direita";
		}			
	}
	
	static function getColorName($color)
	{
		switch($color)
		{
			case e_MenuColor::Green:
				return "verde";
				
			case e_MenuColor::Red:
				return "vermelho";
		}
	}
	
	static function getColorClass($color)
	{
		switch($color)
		{
			case e_MenuColor::Green:
				return "";
				
			case e_MenuColor::Red:
				return "red";
		}		
	}
	
	static function getVisibilityStyleName($visibilityStyle)
	{
		switch($visibilityStyle)
		{
			case e_MenuVisibilityStyle::Normal:
				return "sempre visivel";
				
			case e_MenuVisibilityStyle::DropDown:
				return "recolhivel";
		}		
	}
	
	static function getTypeName($type)
	{
		switch($type)
		{
			case e_MenuType::Items:
				return "items";
				
			case e_MenuType::CallFunction:
				return "chamar função";
		}		
	}	
	
	static function getLastInOrder($position)
	{
		$query = Core::$DB->query("
		SELECT MIN(`order`) as `lastInOrder` FROM `wb_menus` WHERE `position` = {$position}
		");
		
		$fetch = $query->fetch();
		return ($fetch->lastInOrder != 0) ? $fetch->lastInOrder - 1 : 100;
	}
	
	static function reAllocateBellowOff($order, $position)
	{
		Core::$DB->query("
		UPDATE `wb_menus` SET `order` = `order` + 1 WHERE `order` < {$order} AND `position` = {$position}
		");		
	}
	
	private 
		$id
		, $title
		, $position
		, $color
		, $visibility_style
		, $type
		, $hide
		, $name
		, $conditions
		, $min_group
		, $order = 0
		;
		
	private $functionCallback, $items;
	
	function Menu($data = NULL)
	{		
		if($data)
			return $this->load($data);
	}
	
	function load($data)
	{
		if(is_numeric($data))
			return $this->loadByQuery($data);
		elseif(is_array($data))
			return $this->loadByNode($data);
	}
	
	function loadByQuery($id)
	{
		$query = Core::$DB->query("
		SELECT 
			`id`, `title`, `position`, `color`, `visibility_style`, `type`, `hide`, `name`, `conditions`, `min_group`, `order`
		FROM 
			".Tools::getSiteTable("menus")."
		WHERE
			`id` = {$id}
		");
		
		if($query->numRows() != 1)
			return false;
			
		$fetch = $query->fetch();
					
		$this->id = $id;
		$this->title = $fetch->title;
		$this->position = $fetch->position;
		$this->color = $fetch->color;
		$this->visibility_style = $fetch->visibility_style;
		$this->type = $fetch->type;
		$this->hide = $fetch->hide;
		$this->name = $fetch->name;
		$this->conditions = $fetch->conditions;
		$this->min_group = $fetch->min_group;
		$this->order = $fetch->order;
		
		return true;		
	}
	
	function loadByNode($node)
	{					
		$this->title = $node["title"];
		$this->color = $node["color"] | e_MenuColor::Green;
		$this->visibility_style = $node["visibility_style"] | e_MenuVisibilityStyle::Normal;
		$this->hide = $node["hide"] | false;
		$this->name = $node["name"];
		$this->conditions = $node["conditions"] | 0;
		$this->min_group = $node["min_group"] | 0;
		
		if($node["onDraw"] && !empty($node["onDraw"]))
		{
			$this->type = e_MenuType::CallFunction;
			$this->functionCallback = $node["onDraw"];		
		}
		else
		{
			$this->type = e_MenuType::Items;
			$this->items = $node["items"];
		}
		
		return true;		
	}	
	
	function save()
	{
		if($this->id)
		{
			$this->update();
			return;
		}
			
		$this->insert();	
	}
	
	function insert()
	{
		Core::$DB->query("
		INSERT INTO
			`".Tools::getSiteTable("menus")."` (
				`title`
				, `name`
				, `position`
				, `color`
				, `visibility_style`
				, `type`
				, `hide`
				, `conditions`
				, `min_group`
				, `order`
			) VALUES (
				'{$this->title}'
				, '{$this->name}'
				, '{$this->position}'
				, '{$this->color}'
				, '{$this->visibility_style}'
				, '{$this->type}'
				, '{$this->hide}'
				, '{$this->conditions}'
				, '{$this->min_group}'
				, ".(self::getLastInOrder($this->position))."
			)
		");
		
		$this->id = Core::$DB->lastInsertId();
	}
	
	function update()
	{
		Core::$DB->query("
		UPDATE
			`".Tools::getSiteTable("menus")."`
		SET
			`title` = '{$this->title}'
			, `position` = '{$this->position}'
			, `color` = '{$this->color}'
			, `visibility_style` = '{$this->visibility_style}'
			, `type` = '{$this->type}'
			, `hide` = '{$this->hide}'
			, `name` = '{$this->name}'
			, `conditions` = '{$this->conditions}'
			, `min_group` = '{$this->min_group}'
			, `order` = '{$this->order}'
		WHERE
			`id` = {$this->id}
		");
	}
	
	function delete()
	{
		Core::$DB->query("
		DELETE FROM
			`".Tools::getSiteTable("menus")."`
		WHERE
			`id` = {$this->id}			
		");
	}
	
	function getId() { return $this->id; }
	function getTitle() { return $this->title; }
	function getPosition() { return $this->position; }
	function getColor() { return $this->color; }
	function getVisibilityStyle() { return $this->visibility_style; }
	function getType() { return $this->type; }
	function isHide() { return $this->hide; }
	function getName() { return $this->name; }
	function getConditions() { return $this->conditions; }
	function getMinGroup() { return $this->min_group; }
	function getOrder() { return $this->order; }
	
	function setId($id) { $this->id = $id; } 
	function setTitle($title) { $this->title = $title; } 
	function setPosition($position) { $this->position = $position; } 
	function setColor($color) { $this->color = $color; } 
	function setVisibilityStyle($style) { $this->visibility_style = $style; } 
	function setType($type) { $this->type = $type; } 
	function setHide($bool) { $this->hide = $bool; } 
	function setName($name) { $this->name = $name; }
	function setConditions($conditions) { $this->conditions = $conditions; } 
	function setMinGroup($minGroup) { $this->min_group = $minGroup; } 
	function setOrder($order) { $this->order = $order; } 
	
	function __toXML()
	{
		if(Tools::hasFlag($this->conditions, self::CONDITION_MUST_LOGGED) && !Core::isLogged())
			return "";
		elseif(Tools::hasFlag($this->conditions, self::CONDITION_CAN_NOT_LOGGED) && Core::isLogged())
			return "";
		elseif(($this->min_group > 0 && !Core::isLogged()) || (Account::loadLogged() && Account::loadLogged()->getGroup() < $this->min_group))
			return "";
		
		$xml = new SimpleXMLElement("
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<root>		
</root>");
		
		$visibility = "always_viewable";
		$toogleButton = null;

		if($this->visibility_style == e_MenuVisibilityStyle::DropDown)
		{
			$visibility = "null";
			$toogleButton = "tooglePlus";			
			
			if($_COOKIE["menudropdown_{$this->name}"])
			{
				$visibility = "viewable";
				$toogleButton = "toogleMinus";		
			}				
		}			
		
		//corpo
		$li = $xml->addChild("li");
		
		//titulo
		$div = $li->addChild("div");
		$div->addAttribute("name", $this->name);
		
		if($this->color != e_MenuColor::Green)
			$div->addAttribute("class", $this->getColorClass($this->color));
		
		$div->addChild("strong", $this->getTitle());
		
		if($toogleButton)
		{
			$span = $div->addChild("span");
			$span->addAttribute("class", $toogleButton);
		}
		
		//conteudo
		if($this->type == e_MenuType::Items)
		{
			$ul = $li->addChild("ul");
			$ul->addAttribute("class", $visibility);
			
			foreach($this->items as $item)
			{
				$menuItem = new MenuItem($item["name"], $item["url"], $item["conditions"] | 0, $item["min_group"] | 0, $item["style"] | "");
				$menuItem->__addToElement(&$ul);
			}
		}
		elseif($this->type == e_MenuType::CallFunction)
		{
			if(method_exists("Menus", $this->functionCallback) && !call_user_func(array("Menus", $this->functionCallback), &$li))
				return "";
		}
		
		return $li->asXML();
	}
	
	
}