<?php
namespace Framework\Auctions;
use \Core\Configs;
use \Core\Consts;
class Auction extends \Core\DBTable
{
	const
		STATUS_BEGUN = 0
		,STATUS_BEGIN = 1
		,STATUS_ENDED = 2
		;
	
	public
		$id
		,$title
		,$description
		,$min_bid = 0
		,$current_bid = 0
		,$begin
		,$end
		,$visible = 0
	;
		
	private
		$m_items
		,$m_bid
	;
	
	static function Load($id, $canNonVisible = false)
	{
		$obj = parent::Load(\Core\Tools::getSiteTable("auctions"), $id);
		$obj instanceof Auction;
		
		if(!$canNonVisible && !$obj->visible)
			return false;
		
		return $obj;
	}
	
	static function ListBegun($canNonVisible = false)
	{
		$query = \Core\Main::$DB->query("
		SELECT 
			`id`, `title`, `description`, `min_bid`, `current_bid`, `begin`, `end` 
		FROM 
			`".\Core\Tools::getSiteTable("auctions")."` 
		WHERE 
			`begin` < UNIX_TIMESTAMP()
			AND `end` > UNIX_TIMESTAMP()
			".((!$canNonVisible) ? "AND `visible` = 1" : null)."
		ORDER BY
			`end` ASC					
		");
		
		if($query->numRows() == 0)
			return NULL;
		
		$array = array();
		$query->fetchAsArrayObject($array, __CLASS__);
		
		return $array;
	}
	
	static function ListEnded($canNonVisible = false)
	{
		$query = \Core\Main::$DB->query("
				SELECT
					`id`, `title`, `description`, `min_bid`, `current_bid`, `begin`, `end`
				FROM
					`".\Core\Tools::getSiteTable("auctions")."`
				WHERE
					`end` < UNIX_TIMESTAMP()
					".((!$canNonVisible) ? "AND `visible` = 1" : null)."
				ORDER BY
					`end` DESC			
				LIMIT 10
				");
	
		if($query->numRows() == 0)
			return NULL;
	
		$array = array();
		$query->fetchAsArrayObject($array, __CLASS__);
	
		return $array;
	}
	
	static function ListStarting($canNonVisible = false)
	{
		$query = \Core\Main::$DB->query("
				SELECT
					`id`, `title`, `description`, `min_bid`, `current_bid`, `begin`, `end`
				FROM
					`".\Core\Tools::getSiteTable("auctions")."`
				WHERE
					`begin` > UNIX_TIMESTAMP()
					".((!$canNonVisible) ? "AND `visible` = 1" : null)."
				ORDER BY
					`begin` DESC
				");
	
		if($query->numRows() == 0)
			return NULL;
	
		$array = array();
		$query->fetchAsArrayObject($array, __CLASS__);
	
		return $array;
	}	
	
	function __construct(){
		parent::__construct(\Core\Tools::getSiteTable("auctions"));
	}
	
	function LoadCurrentBid()
	{
		if(!$this->m_bid)
			$this->m_bid = Bid::Load($this->current_bid);
	}
	
	function LoadItems()
	{
		if(!$this->m_items)
		{
			$this->m_items = array();
			Item::LoadItems($this->m_items, $this->id);
		}
	}
	
	function GetCurrentBid(){
		$this->LoadCurrentBid();
		return $this->m_bid;
	}
	function GetItems(){
		$this->LoadItems();
		return $this->m_items;
	}
	
	function GetStatus(){
		if($this->end < time())
			return self::STATUS_ENDED;
		
		if($this->begin < time())
			return self::STATUS_BEGIN;
		
		return self::STATUS_BEGUN;
	}
}
