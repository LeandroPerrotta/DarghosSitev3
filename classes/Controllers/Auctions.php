<?php
namespace Controllers;

use Framework\Auctions\Auction;

use Framework\Auctions as AuctionsModel;
use Views\Auctions as AuctionsView;

class Auctions
{
	function Detail()
	{
		$logged = \Framework\Account::loadLogged();
		
		if(!$logged)
		{
			\Core\Main::requireLogin();
			return false;
		}
		
		$id = (int)$_GET["id"];
		
		$data = array();
		$data["auction"] = AuctionsModel\Auction::Load($id);		
		
		$view = new AuctionsView\Detail($data);
		return true;
	}
	
	function Index()
	{
		$data = array();
		$data["auctions_begun"] = AuctionsModel\Auction::ListBegun();
		$data["auctions_ended"] = AuctionsModel\Auction::ListEnded();
		$data["auctions_starting"] = AuctionsModel\Auction::ListStarting();
		
		$view = new AuctionsView\Index($data);
		return true;
	}
	
	function Delete()
	{
		$logged = \Framework\Account::loadLogged();
		
		if(!$logged || $logged->getAccess() < \t_Access::Administrator)
			return false;

		\Core\Main::$isAjax = true;
		
		$auction = new AuctionsModel\Auction();
		$auction->id = (int)$_POST["id"];
		$auction->Delete();
		
		$ret["error"] = false;
		$ret["msg"] = "O lançe foi removido com sucesso!";
		return $ret;	
	}

	function Additem()
	{
		$data = array();
		$data["show_success"] = false;
	
		$logged = \Framework\Account::loadLogged();
	
		if(!$logged || $logged->getAccess() < \t_Access::Administrator || !$_GET["id"])
			return false;
			
		if($_POST)
		{
			$item = new AuctionsModel\Item();
			$item->auction_id = (int)$_GET["id"];
			$item->itemtype = (int)$_POST["itemtype"];
			$item->count = (int)$_POST["item_count"];
			
			$attr = array();
			
			if((int)$_POST["item_actionid"])
				$attr["action_id"] = $_POST["item_actionid"];
			
			if(count($attr) > 0)
			{
				$item->attributes = json_encode($attr);
			}
			
			$item->Insert();
			$data["show_success"] = true;
		}
	
		$view = new AuctionsView\Additem($data);
		return true;
	}	
	
	function Create()
	{
		$data = array();
		$data["created"] = false;
		
		$logged = \Framework\Account::loadLogged();
		
		if(!$logged || $logged->getAccess() < \t_Access::Administrator)
			return false;
			
		if($_POST)
		{
			list($month, $day, $year) = explode("/", $_POST["auction_begin"]);
			$begin = mktime(10, 0, 0, $month, $day, $year);
			
			list($month, $day, $year) = explode("/", $_POST["auction_end"]);
			$end = mktime(10, 0, 0, $month, $day, $year);
			
			$auction = new AuctionsModel\Auction();
			$auction->title = $_POST["auction_title"];
			$auction->description = $_POST["auction_description"];
			$auction->min_bid = (int)$_POST["auction_min_bid"];
			$auction->begin = $begin;
			$auction->end = $end;
			
			$auction->Insert();			
			
			$data["created"] = true;
		}
		
		$view = new AuctionsView\Create($data);
		return true;
	}
	
	function Makebid()
	{
		\Core\Main::$isAjax = true;
		
		$ret = array();
		
		$logged = \Framework\Account::loadLogged();
		if(!$logged)
		{
			$ret["error"] = true;
			return $ret;
		}
		
		$auction_id = (int)$_POST["bid_auction"];
		$bid_value = (int)$_POST["bid_value"];
		$bid_player = $_POST["bid_player"];
		
		$auction = AuctionsModel\Auction::Load($auction_id);
		if(!$auction)
		{
			$ret["error"] = true;
			$ret["msg"] = "Error#1";
			return $ret;			
		}
		
		if(!in_array($bid_player, $logged->getCharacterList()))
		{
			$ret["error"] = true;
			$ret["msg"] = "Error#2";
			return $ret;			
		}
		
		$player = new \Framework\Player();
		if(!$player->loadByName($bid_player))
		{
			$ret["error"] = true;
			$ret["msg"] = "Error#3";
			return $ret;			
		}
		
		$oldbid = $auction->GetCurrentBid();
		$oldbid instanceof AuctionsModel\Bid;
		
		if($oldbid && $oldbid->bid >= $bid_value)
		{
			$ret["error"] = true;
			$ret["msg"] = "Error#4";
			return $ret;
		}
		
		if($auction->GetStatus() != AuctionsModel\Auction::STATUS_BEGIN)
		{
			$ret["error"] = true;
			$ret["msg"] = "Error#5";
			return $ret;			
		}
		
		if($logged->getPremDays() <= $bid_value)
		{
			$ret["error"] = true;
			$ret["msg"] = "Você possui {$logged->getPremDays()} dias de conta premium, insulficientes para o lançe de {$bid_value}. Por favor, diminua seu lançe ou adquira mais dias de conta premium.";
			return $ret;
		}
		
		$newbid = new AuctionsModel\Bid();
		$newbid->auction_id = $auction->id;
		$newbid->bid = $bid_value;
		$newbid->date = time();
		$newbid->player_id = $player->getId();
		$newbid->Save();
		
		$auction->current_bid = $newbid->id;
		$auction->Update();
		
		$newacc = $player->loadAccount();
		$newacc->updatePremDays($newbid->bid, false);
		$newacc->save();
		
		if($oldbid)
		{
			$oldplayer = new \Framework\Player();
			$oldplayer->load($oldbid->player_id);
			$oldacc = $oldplayer->loadAccount();
			$oldacc->updatePremDays($oldbid->bid, true);
			$oldacc->save();
		}
		
		$ret["error"] = false;
		$ret["msg"] = "Seu lançe foi efetuado com sucesso! Você vencerá o leilão se ninguem cobrir o seu lançe nos proximos dias!";
		
		return $ret;
	}
}