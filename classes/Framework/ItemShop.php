<?php
namespace Framework;
class ItemShop
{
	const TYPE_ITEM = 0;
	const TYPE_CALLBACK = 1;
	
	//for type "item"
	const PARAM_ITEM_ID = "item_id";
	const PARAM_ITEM_STACKABLE = "item_stackable";
	const PARAM_ITEM_COUNT = "item_count";
	const PARAM_ITEM_ACTION_ID = "item_action_id";
	
	//for type "callback"
	const PARAM_FUNCTION = "function";
	const PARAM_PARAMETERS = "parameters";
	const PARAM_IMAGE_URL = "image";
	
	const LIST_ORDER_NAME = 0;
	const LIST_ORDER_RELEVANCE = 1;
	const LIST_ORDER_NEWER = 2;
	const LIST_ORDER_OLDER = 3;
	const LIST_ORDER_PRICE_DESC = 4;
	const LIST_ORDER_PRICE_ASC = 5;
	
	private $id, $name, $description, $params, $price, $added_in, $enabled, $type;

	static function getItemShopList($orderBy = LIST_ORDER_RELEVANCE)
	{
		if($orderBy == self::LIST_ORDER_NAME)
		{
			$query = \Core\Main::$DB->query("
				SELECT 
					`id`, `name`, `description`, `params`, `price`, `added_in`, `enabled`, `type` 
				FROM 
					`".\Core\Tools::getSiteTable("itemshop")."` 
				WHERE 
					`enabled` = 1
				ORDER BY
					`name` ASC
			");
		}
		elseif($orderBy == self::LIST_ORDER_RELEVANCE)
		{
			$query = \Core\Main::$DB->query("
				SELECT 
					`shop`.`id`, `shop`.`name`, `shop`.`description`, `shop`.`params`, `shop`.`price`, `shop`.`added_in`, `shop`.`enabled`, `shop`.`type` 
				FROM 
					`".\Core\Tools::getSiteTable("itemshop")."` `shop`
				LEFT JOIN
					`".\Core\Tools::getSiteTable("itemshop_log")."` `log`
				ON
					`shop`.`id` = `log`.`shop_id`
				WHERE 
					`log`.`date` >= UNIX_TIMESTAMP() - (60 * 60 * 24 * 90)
					AND `shop`.`enabled` = 1
				GROUP BY
					`shop`.`id`
				ORDER BY
					COUNT(*) DESC
			");			
		}
		elseif($orderBy == self::LIST_ORDER_NEWER)
		{
			$query = \Core\Main::$DB->query("
				SELECT 
					`id`, `name`, `description`, `params`, `price`, `added_in`, `enabled`, `type` 
				FROM 
					`".\Core\Tools::getSiteTable("itemshop")."` 
				WHERE 
					`enabled` = 1
				ORDER BY
					`added_in` DESC
			");			
		}
		elseif($orderBy == self::LIST_ORDER_OLDER)
		{
			$query = \Core\Main::$DB->query("
				SELECT 
					`id`, `name`, `description`, `params`, `price`, `added_in`, `enabled`, `type` 
				FROM 
					`".\Core\Tools::getSiteTable("itemshop")."` 
				WHERE 
					`enabled` = 1
				ORDER BY
					`added_in` ASC
			");			
		}
		elseif($orderBy == self::LIST_ORDER_PRICE_DESC)
		{
			$query = \Core\Main::$DB->query("
				SELECT 
					`id`, `name`, `description`, `params`, `price`, `added_in`, `enabled`, `type` 
				FROM 
					`".\Core\Tools::getSiteTable("itemshop")."` 
				WHERE 
					`enabled` = 1
				ORDER BY
					`price` DESC
			");			
		}
		elseif($orderBy == self::LIST_ORDER_PRICE_ASC)
		{
			$query = \Core\Main::$DB->query("
				SELECT 
					`id`, `name`, `description`, `params`, `price`, `added_in`, `enabled`, `type` 
				FROM 
					`".\Core\Tools::getSiteTable("itemshop")."` 
				WHERE 
					`enabled` = 1
				ORDER BY
					`price` ASC
			");			
		}
		
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
			$query = \Core\Main::$DB->query("
				SELECT 
					`id`, `name`, `description`, `params`, `price`, `added_in`, `enabled`, `type` 
				FROM 
					`".\Core\Tools::getSiteTable("itemshop")."` 
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
			\Core\Main::$DB->query("
				UPDATE 
					`".\Core\Tools::getSiteTable("itemshop")."` 
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
			\Core\Main::$DB->query("
				INSERT INTO
					`".\Core\Tools::getSiteTable("itemshop")."`
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
			
			$this->id = \Core\Main::$DB->lastInsertId();
		}
	}
	
	function logItemPurchase($player_id)
	{
		\Core\Main::$DB->query("
			INSERT INTO 
				`".\Core\Tools::getSiteTable("itemshop_log")."`
				(`shop_id`, `date`, `player_id`, `received`) 
			VALUES 
				(
					'{$this->id}', '".time()."', '{$player_id}', '0'
				)");	

		return \Core\Main::$DB->lastInsertId();
	}
	
	function doPlayerGiveThing($player_id)
	{
		if($this->type == self::TYPE_ITEM)
		{
			$item_prop = $this->getParams();
			$stack = $item_prop[self::PARAM_ITEM_STACKABLE] ? $item_prop[self::PARAM_ITEM_STACKABLE] : false;
			$action_id = $item_prop[self::PARAM_ITEM_ACTION_ID] ? $item_prop[self::PARAM_ITEM_ACTION_ID] : null;
			$this->doPlayerGiveItem($player_id, $item_prop[self::PARAM_ITEM_ID], $item_prop[self::PARAM_ITEM_COUNT], $stack, $action_id);
		}
	}
	
	function doPlayerGiveItem($player_id, $item_id, $item_count, $item_stackable = false, $item_action_id = null)
	{
		$DEPOT_ID = 11;
		
		$items = array(
			"presentbox" => 1990,
			"depotitem" => 2589,
			"depotlocker" => 2594
		);
		
		$query = \Core\Main::$DB->query("SELECT `sid` FROM `player_depotitems` WHERE `player_id` = '{$player_id}' ORDER BY `sid` DESC LIMIT 1");	
		$sid = $query->fetch()->sid;
		
		$query = \Core\Main::$DB->query("SELECT `sid` FROM `player_depotitems` WHERE `player_id` = '{$player_id}' AND `pid` = '{$DEPOT_ID}'");
	
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
				$this->doPlayerAddDepotItem($player_id, $sid, $present_sid, $item_id, 1, true, $item_action_id);
				
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
	
	function doPlayerAddDepotItem($player_id, $sid, $pid, $itemtype, $count, $log = false, $action_id = null)
	{
		$attr = "";
		
		$attrdata = new \OTS_Buffer();
		
		$ATTRIBUTE_MAP = 128;		
		$VALUE_TYPE_INTEGER = 2;		
		
		$attrdata->putChar($ATTRIBUTE_MAP);
		
		$attr_count = 0;
		
		if($log)
			$attr_count++;
			
		if($action_id)
			$attr_count++;
		
		$attrdata->putShort($attr_count); //attributes count	
			
		if($log)
		{
			$log_id = $this->logItemPurchase($player_id);
			
			$attrdata->putString("itemShopLogId"); //attribute name
			$attrdata->putChar($VALUE_TYPE_INTEGER); //attribute value data type
			$attrdata->putLong($log_id); //attribute value
		}
		
		if($action_id)
		{
			$attrdata->putString("aid"); //attribute name
			$attrdata->putChar($VALUE_TYPE_INTEGER); //attribute value data type
			$attrdata->putLong($action_id); //attribute value
		}
		
		if($attr_count > 0)
			$attr = $attrdata->getBuffer();
		
		\Core\Main::$DB->query("
			INSERT INTO 
				`player_depotitems` 
				(`player_id`, `sid`, `pid`, `itemtype`, `count`, `attributes`) 
			VALUES 
				(
					'{$player_id}', '{$sid}', '{$pid}', '{$itemtype}', '{$count}', '{$attr}'
				)");
	}	
	
	function getId() { return $this->id; }
	function getName() { return $this->name; }
	function getDescription() { return $this->description; }
	function getParams() { return json_decode($this->params, true); }
	function getPrice() { return $this->price; }
	function getPriceStr() { return "R$ " . number_format($this->price / 100, 2); }
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
	
	/*
	 * Callback functions for non ingame items
	 * */
	
	static function onPurchaseExpBonus(Account $account){
	    $lastExpBonus = $account->getLastExpBonus();
	    if($lastExpBonus != 0 && $lastExpBonus + (60 * 60 * 24 * 10) >= time()){
	        return array("success" => false, "msg" => tr("Este serviço so pode ser adquirido uma vez a cada 10 dias."));
	    }
	    
	    $account->addExpDays();
	    $account->save();
	    
	    return array("success" => true);
	}
}
?>