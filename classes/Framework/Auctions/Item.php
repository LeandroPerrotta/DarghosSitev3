<?php
namespace Framework\Auctions;
class Item extends \Core\DBTable
{
	public
		$auction_id
		,$itemtype
		,$count
		,$attributes
		;
		
	static function LoadItems(&$items, $auction_id)
	{
		$query = \Core\Main::$DB->query("SELECT `auction_id`, `itemtype`, `count`, `attributes` FROM `".\Core\Tools::getSiteTable("auction_items")."` WHERE `auction_id` = {$auction_id}");
	
		if($query->numRows() == 0)
			return;
		
		$query->fetchAsArrayObject($items, __CLASS__);
	}	
	
	function __construct(){
		parent::__construct(\Core\Tools::getSiteTable("auction_items"));
	}
}