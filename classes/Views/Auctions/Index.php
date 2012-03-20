<?php
namespace Views\Auctions;
use Framework\Auctions\Item;

use Framework\Player;

class Index extends \Core\Views
{
	function __construct($data)
	{
		parent::__construct($data);
		
		$logged = \Framework\Account::loadLogged();		
		/*
		 * Leilões iniciados
		 */
		$table_begun = new \Framework\HTML\Table();
		
		$table_begun->AddField("Leilões de itens em andamento", null, null, 5, true);
		$table_begun->AddRow();
		
		if(count($this->data["auctions_begun"]) > 0)
		{
			$table_begun->AddField("", 10);
			$table_begun->AddField("<strong>Item</strong>", 30);
			$table_begun->AddField("<strong>Lançe atual</strong>", 20);
			$table_begun->AddField("<strong>Acaba em</strong>", 20);
			$table_begun->AddField("", 10);
			$table_begun->AddRow();			
			
			foreach($this->data["auctions_begun"] as $k => $auction)
			{
				$auction instanceof \Framework\Auctions\Auction;
				
				$bid = $auction->GetCurrentBid();
				$bid instanceof \Framework\Auctions\Bid;
				
				$bid_str = "Nenhum";
				
				if($bid)
				{
					$player = new Player();
					$player->load($bid->player_id);
					
					$bid_str = "<center>";
					
					if($logged)
					{
						$bid_str .= "{$bid->bid} dias por<br>";
					}
					
					$bid_str .= "<a href='?ref=character.view&name={$player->getName()}'>{$player->getName()}</a></center>";
				}
				
				$items = $auction->GetItems();
				
				$key = rand(0, count($items) - 1);		
				
				$item = $items[$key];
				$item instanceof Item;
				
				$table_begun->AddField("<center><img id='item_{$item->itemtype}' class='requestItemInfo' src='files/items/{$item->itemtype}.gif'/></center>");
				
				$timeleft_str = "";
				
				$diff = $auction->end - time();
				if($diff <= 60 * 60)
					$timeleft_str = "menos de 1 hora";
				elseif($diff <= 60 * 60 * 24)
					$timeleft_str = "menos de <strong>".ceil($diff / (60 * 60))."</strong> horas";
				else
					$timeleft_str = "menos de <strong>".ceil($diff / (60 * 60 * 24))."</strong> dias";
				
				$table_begun->AddField("<center><h3>{$auction->title}</h3>{$auction->description}</center>");
				$table_begun->AddField($bid_str);
				$table_begun->AddField("<center>" . \Core\Main::formatDate($auction->end) . " ou<br>" .$timeleft_str . "</center>");		
				$table_begun->AddField("<center><a href='?ref=auctions.detail&id={$auction->id}'>Detalhes</a></center>");		
				$table_begun->AddRow();
			}
		}
		else
		{
			$table_begun->AddField("O nosso leilão não possui nenhum item disponivel no momento.", null, null, 5);
			$table_begun->AddRow();
		}
		
		/*
		 * Leilões a iniciar
		*/	
		$table_starting = new \Framework\HTML\Table();
		
		$table_starting->AddField("Leilões a iniciar", null, null, 5, true);
		$table_starting->AddRow();
		
		if(count($this->data["auctions_starting"]) > 0)
		{
			$table_starting->AddField("", 10);
			$table_starting->AddField("<strong>Item</strong>", 30);
			$table_starting->AddField("<strong>Lançe inicial</strong>", 20);
			$table_starting->AddField("<strong>Inicia em</strong>", 20);
			$table_starting->AddField("", 10);
			$table_starting->AddRow();
				
			foreach($this->data["auctions_starting"] as $k => $auction)
			{
				$auction instanceof \Framework\Auctions\Auction;
		
				$items = $auction->GetItems();
		
				$key = rand(0, count(items) - 1);
		
				$item = $items[$key];
				$item instanceof Item;
		
				$table_starting->AddField("<center><img id='item_{$item->itemtype}' class='requestItemInfo' src='files/items/{$item->itemtype}.gif'/></center>");
		
				$timeleft_str = "";
		
				$diff = $auction->begin - time();
				if($diff <= 60 * 60)
					$timeleft_str = "menos de 1 hora";
				elseif($diff <= 60 * 60 * 24)
				$timeleft_str = "menos de <strong>".ceil($diff / (60 * 60))."</strong> horas";
				else
					$timeleft_str = "menos de <strong>".ceil($diff / (60 * 60 * 24))."</strong> dias";
		
				$table_starting->AddField("<center><h3>{$auction->title}</h3>{$auction->description}</center>");
				$table_starting->AddField("<center>{$auction->min_bid}</center>");
				$table_starting->AddField("<center>" . \Core\Main::formatDate($auction->begin) . " ou<br>" .$timeleft_str . "</center>");
				$table_starting->AddField("<center><a href='?ref=auctions.detail&id={$auction->id}'>Detalhes</a></center>");
				$table_starting->AddRow();
			}
		}
		else
		{
			$table_starting->AddField("O nosso leilão não possui nenhum item disponivel no momento.", null, null, 5);
			$table_starting->AddRow();
		}
		
		/*
		 * Leilões finalizados
		*/
		$table_ended = new \Framework\HTML\Table();
		
		$table_ended->AddField("Leilões de items já encerrados", null, null, 5, true);
		$table_ended->AddRow();
		
		if(count($this->data["auctions_ended"]) > 0)
		{
			$table_ended->AddField("", 10);
			$table_ended->AddField("<strong>Item</strong>", 30);
			$table_ended->AddField("<strong>Vencedor</strong>", 20);
			$table_ended->AddField("<strong>Acabou em</strong>", 20);
			$table_ended->AddField("", 10);
			$table_ended->AddRow();
		
			foreach($this->data["auctions_ended"] as $k => $auction)
			{
				$auction instanceof \Framework\Auctions\Auction;
		
				$bid = $auction->GetCurrentBid();
				$bid instanceof \Framework\Auctions\Bid;
		
				$bid_str = "<center>n/a</center>";
		
				if($bid)
				{
					$player = new Player();
					$player->load($bid->player_id);
		
					$bid_str = "<center>";
			
					if($logged)
					{
						$bid_str .= "{$bid->bid} dias por<br>";
					}
		
					$bid_str .= "<a href='?ref=character.view&name={$player->getName()}'>{$player->getName()}</a></center>";
				}
		
				$items = $auction->GetItems();
		
				$key = rand(0, count(items) - 1);
		
				$item = $items[$key];
				$item instanceof Item;
		
				$table_ended->AddField("<center><img id='item_{$item->itemtype}' class='requestItemInfo' src='files/items/{$item->itemtype}.gif'/></center>");
		
				$table_ended->AddField("<center><h3>{$auction->title}</h3>{$auction->description}</center>");
				$table_ended->AddField($bid_str);
				$table_ended->AddField("<center>" . \Core\Main::formatDate($auction->end) . "</center>");
				$table_ended->AddField("<center><a href='?ref=auctions.detail&id={$auction->id}'>Detalhes</a></center>");
				$table_ended->AddRow();
			}
		}
		else
		{
			$table_ended->AddField("O nosso leilão não possui nenhum item disponivel no momento.", null, null, 5);
			$table_ended->AddRow();
		}		
		
		$admin_buttons = "";
		
		if($logged && $logged->getAccess() == \t_Access::Administrator)
		{
			$admin_buttons .= "<a class='buttonstd' href='?ref=auctions.create'>Novo Leilão</a>";
		}
		
		global $module;
		$module = "
			<p>No sistema de leilões do Darghos você pode usar dias de premium de sua conta para adquirir items e outros beneficios extra. Se você é novo e não sabe como funciona o nosso leilão clique <a href='?ref=auctions.info'>aqui</a> e leia as instruções.</p>
			{$table_begun->Draw()}
			{$admin_buttons}
			{$table_starting->Draw()}
			{$table_ended->Draw()}
		";
	}
}