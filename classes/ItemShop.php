<?php
class ItemShop
{
	const TYPE_ITEM = 0;
	
	const PARAM_ITEM_ID = "item_id";
	const PARAM_ITEM_STACKABLE = "item_stackable";
	const PARAM_ITEM_COUNT = "item_count";
	
	private $id, $name, $description, $params, $price, $added_in, $enabled, $type;

	static function getItemShopList()
	{
		$query = Core::$DB->query("
			SELECT 
				`id`, `name`, `description`, `params`, `price`, `added_in`, `enabled`, `type` 
			FROM 
				`".Tools::getSiteTable("itemshop")."` 
			WHERE 
				`enabled` = 1
		");
		
		if($query->numRows() == 0)
			return false;
			
		$items = array();	
			
		for($i = 0; $i < $query->numRows(); ++$i)
		{
			$fetch = $query->fetch();
			$item = new ItemShop();
			$item->load($fetch->id, $fetch);
			
			$items[] = $item;						
		}
		
		return $items;
	}
	
	
	function __construct()
	{

	}
	
	function load($id, $fetch = null)
	{
		if(!$fetch)
		{
			$query = Core::$DB->query("
				SELECT 
					`id`, `name`, `description`, `params`, `price`, `added_in`, `enabled`, `type` 
				FROM 
					`".Tools::getSiteTable("itemshop")."` 
				WHERE id = {$id}
			");
			
			if($query->numRows() == 0)
				return false;
				
			$fetch = $query->fetch();
		}
		
		$this->id = $fetch->id;
		$this->name = $fetch->name;
		$this->description = $fetch->description;
		$this->params = $fetch->params;
		$this->price = $fetch->price;
		$this->added_in = $fetch->added_in;
		$this->type = $fetch->type;
		
		return true;
	}
	
	function save()
	{
		if($this->id)
		{
			Core::$DB->query("
				UPDATE 
					`".Tools::getSiteTable("itemshop")."` 
				SET
					`name` = '{$this->name}',
					`description` = '{$this->description}',
					`params` = '{$this->params}',
					`price` = '{$this->price}',
					`type` = '{$this->type}'
				WHERE
					`id` = '{$this->id}'
					
			");
		}
		else
		{
			Core::$DB->query("
				INSERT INTO
					`".Tools::getSiteTable("itemshop")."`
					(`name`, `description`, `params`, `price`, `added_in`, `enabled`, `type`)
				VALUES
					(
						'{$this->name}',
						'{$this->description}',
						'{$this->params}',
						'{$this->price}',
						'{$this->added_in}',
						'{$this->enabled}',
						'{$this->type}'
					)
			");
			
			$this->id = Core::$DB->lastInsertId();
		}
	}
	
	function logItemPurchase($player_id)
	{
		Core::$DB->query("
			INSERT INTO 
				`".Tools::getSiteTable("itemshop_log")."`
				(`shop_id`, `date`, `player_id`) 
			VALUES 
				(
					'{$this->id}', '".time()."', '{$player_id}'
				)");		
	}
	
	function doPlayerGiveThing($player_id)
	{
		if($this->type == self::TYPE_ITEM)
		{
			$item_prop = $this->getParams();
			$stack = $item_prop[self::PARAM_ITEM_STACKABLE] ? $item_prop[self::PARAM_ITEM_STACKABLE] : false;
			$this->doPlayerGiveItem($player_id, $item_prop[self::PARAM_ITEM_ID], $item_prop[self::PARAM_ITEM_COUNT], $stack);
		}
		
		$this->logItemPurchase($player_id);
	}
	
	function doPlayerGiveItem($player_id, $item_id, $item_count, $item_stackable = false)
	{
		$DEPOT_ID = 11;
		
		$items = array(
			"presentbox" => 1990,
			"depotitem" => 2589,
			"depotlocker" => 2594
		);
		
		$query = Core::$DB->query("SELECT `sid` FROM `player_depotitems` WHERE `player_id` = '{$player_id}' ORDER BY `sid` DESC LIMIT 1");	
		$sid = $query->fetch()->sid;
		
		$query = Core::$DB->query("SELECT `sid` FROM `player_depotitems` WHERE `player_id` = '{$player_id}' AND `pid` = '{$DEPOT_ID}'");
	
		//player jÃ¡ tem o itemshop depot criado
		if($query->numRows() > 0)
		{
			$depot_sid = $query->fetch()->sid;
			
			$sid++;
			$present_sid = $sid;
			$this->doPlayerAddDepotItem($player_id, $sid, $depot_sid, $items["presentbox"], 1);
		}
		//ainda nao...
		else
		{
			$sid++;
			$depot_sid = $sid;
			$this->doPlayerAddDepotItem($player_id, $sid, $DEPOT_ID, $items["depotitem"], 1);
			
			$sid++;
			$this->doPlayerAddDepotItem($player_id, $sid, $depot_sid, $items["depotlocker"], 1);
			
			$sid++;
			$present_sid = $sid;
			$this->doPlayerAddDepotItem($player_id, $sid, $depot_sid, $items["presentbox"], 1);			
		}	

		if(!$item_stackable)
		{
			do{
				$sid++;
				$this->doPlayerAddDepotItem($player_id, $sid, $present_sid, $item_id, 1);
				
				$item_count--;
			}while($item_count > 0);		
		}
		else
		{
			do{
				
				$c = 100;
				
				if($item_count >= $c)
				{
					$item_count -= $c;
				}
				else
				{
					$c = $item_count;
					$item_count = 0;
				}
				
				$sid++;
				$this->doPlayerAddDepotItem($player_id, $sid, $present_sid, $item_id, min(array(100, $c)));
			}while($item_count > 0);
		}		
	}
	
	function doPlayerAddDepotItem($player_id, $sid, $pid, $itemtype, $count)
	{
		Core::$DB->query("
			INSERT INTO 
				`player_depotitems` 
				(`player_id`, `sid`, `pid`, `itemtype`, `count`) 
			VALUES 
				(
					'{$player_id}', '{$sid}', '{$pid}', '{$itemtype}', '{$count}'
				)");
	}	
	
	function getId() { return $this->id; }
	function getName() { return $this->name; }
	function getDescription() { return $this->description; }
	function getParams() { return json_decode($this->params, true); }
	function getPrice() { return $this->price; }
	function getAddedIn() { return $this->added_in; }
	function getType() { return $this->type; }
	function isEnabled() { return $this->enabled; }
	
	function setId($int) { $this->id = $int; }
	function setName($str) { $this->name = $str; }
	function setDescription($str) { $this->description = $str; }
	function setParams($array) { $this->params = json_encode($array); }
	function setPrice($int) { $this->price = $int; }
	function setAddedIn($int) { $this->added_in = $int; }
	function setType($int) { $this->type = $int; }
	function setEnabled($bool) { $this->enabled = $bool; }
}
?>