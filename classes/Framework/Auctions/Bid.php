<?php
namespace Framework\Auctions;
class Bid extends \Core\DBTable
{
	public
		$id
		,$auction_id
		,$player_id
		,$bid
		,$date
	;
	
	static function Load($id)
	{		
		$obj = parent::Load(\Core\Tools::getSiteTable("auction_bids"), $id);
		$obj instanceof Bid;
		return $obj;
	}
	
	function __construct(){
		parent::__construct(\Core\Tools::getSiteTable("auction_bids"));
	}
	
	function Save()
	{
		if(!$this->id)
			$this->id = parent::Insert();
		else
			parent::Update();
	}
}