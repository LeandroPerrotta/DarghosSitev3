<?php
namespace Views\Auctions;
use Framework\Auctions as AuctionsModel;
use Framework\Player;
use Framework\UI;
class Detail extends \Core\Views
{
	function __construct($data)
	{
		parent::__construct($data);
		
		$logged = \Framework\Account::loadLogged();
		
		$auction = $this->data["auction"];
		$auction instanceof AuctionsModel\Auction;
		
		$infos = new \Framework\HTML\Table();
		$infos->AddField("Informações", null, null, 1, true);
		$infos->AddRow();
		
		$bid_auction = new UI\Input($this);
		$bid_auction->SetName("bid_auction");
		$bid_auction->SetId("bid_auction");
		$bid_auction->SetValue($auction->id);
		$bid_auction->IsHidden();		
		
		$end_str = "
		<p>
			<strong>Este leilão foi finalizado em</strong>
			<br>".\Core\Main::formatDate($auction->end)."
		</p>		
		";
		
		if($auction->GetStatus() != AuctionsModel\Auction::STATUS_ENDED)
		{		
			$diff = $auction->end - time();
			
			$timeleft_str = "";
			
			if($auction->GetStatus() == AuctionsModel\Auction::STATUS_BEGIN)
			{
				$timeleft_str = " <span style='color: #00ff00'>(<small>";
				
				if($diff <= 60 * 60)
					$timeleft_str.= "menos de 1 hora";
				elseif($diff < 60 * 60 * 24)
					$timeleft_str .= "menos de <strong>".ceil($diff / (60 * 60))."</strong> horas";
				else
					$timeleft_str .= "menos de <strong>".ceil($diff / (60 * 60 * 24))."</strong> dias";
				
				$timeleft_str .= "</small>)</span>";
			}
			
			$end_str = "
			<p>
				<strong>Este leilão será finalizado em</strong>
				<br>".\Core\Main::formatDate($auction->end)."
				{$timeleft_str}
			</p>
			";		
		}	
		
		$begin_str = "
		<strong>Este leilão foi iniciado em</strong>
		<br>".\Core\Main::formatDate($auction->begin)."		
		";
		
		if($auction->GetStatus() == AuctionsModel\Auction::STATUS_BEGUN)
		{
			$diff = $auction->begin - time();
				
			$timeleft_str = " <span style='color: #00ff00'>(<small>";
		
			if($diff <= 60 * 60)
				$timeleft_str .= "menos de 1 hora";
			elseif($diff < 60 * 60 * 24)
			$timeleft_str .= "menos de <strong>".ceil($diff / (60 * 60))."</strong> horas";
			else
				$timeleft_str .= "menos de <strong>".ceil($diff / (60 * 60 * 24))."</strong> dias";		
			
			$timeleft_str .= "</small>)</span>";
			
			$begin_str = "
			<strong>Este leilão será iniciado em</strong>
			<br>".\Core\Main::formatDate($auction->begin)."
			{$timeleft_str}
			";			
		}
	
		$infos->AddField("
			<h3>{$auction->title}</h3>
			{$auction->description}
		");
		$infos->AddField("
			<p>
				{$begin_str}
			</p>
			<p>
				{$end_str}
			</p>
				", 40);	
		$infos->AddRow();
		
		$items = $auction->GetItems();
		
		$itemsTable = new \Framework\HTML\Table();
		
		$itemsTable->AddField("Fazem parte deste leilão os seguintes itens:", null, null, 2);
		$itemsTable->AddRow();		
		
		foreach($items as $k => $item)
		{
			$item instanceof AuctionsModel\Item;
			
			$_item = \Framework\Items::LoadById($item->itemtype);
			
			$itemsTable->AddField("<img id='item_{$item->itemtype}' class='requestItemInfo' src='files/items/{$item->itemtype}.gif'/>", null, "text-align: right; vertical-align: bottom; width: 32px; height: 32px;");
			
			$string = "<span id='item_{$item->itemtype}' class='requestItemInfo'>{$item->count}x {$_item->GetName()}</span>";
			
			if($logged && $logged->getAccess() == \t_Access::Administrator)
				$string .= " <a onclick='onDeleteItem({$auction->id}, {$k})' href='#'>[deletar]</a>";
			
			$itemsTable->AddField($string);
			$itemsTable->AddRow();			
		}
		
		$infos->AddField($itemsTable->Draw() . "<a class='buttonstd' href='?ref=auctions.additem&id={$auction->id}'>Adicionar</a>", null, null, 2);
		$infos->AddRow();		
		
		$bid = $auction->GetCurrentBid();
		$bid instanceof AuctionsModel\Bid;
				
		
		if($auction->GetStatus() == AuctionsModel\Auction::STATUS_BEGIN)
			$bid_str = "<p>Ninguem até agora efetuou um lançe neste leilão. Está interessado? O lançe inicial é de <strong>{$auction->min_bid}</strong>.<p>";
		elseif($auction->GetStatus() == AuctionsModel\Auction::STATUS_BEGUN)
		$bid_str = "<p>Este Leilão irá iniciar em breve. Está interessado? Já garanta seus dias de conta premium! O lançe inicial será de <strong>{$auction->min_bid}</strong> dias.<p>";
		else
			$bid_str = "<p>Este leilão terminou sem nenhum interessado.</p>";
		
		if($bid)
		{
			$player = new Player();
			$player->load($bid->player_id);
			
			if($auction->end > time())
				$bid_str = "
					<p>
						<h3>Maior lançe até o momento:</h3> 
						<br>► <strong>{$bid->bid}</strong> dias de conta premium dado pelo jogador <a href='?ref=character.view&name={$player->getName()}'>{$player->getName()}</a>.
						<br><em><small>Efetuado em ".\Core\Main::formatDate($bid->date).".</small></em>
					</p>";
			else
				$bid_str = "
				<p>
				<h3>Lançe vencedor do leilão (terminado):</h3>
				<br>► <strong>{$bid->bid}</strong> dias de conta premium dado pelo jogador <a href='?ref=character.view&name={$player->getName()}'>{$player->getName()}</a>.
				<br><em><small>Efetuado em ".\Core\Main::formatDate($bid->date).".</small></em>
				</p>";				
		}

		$infos->AddField($bid_str, null, null, 2);
		$infos->AddRow();
		
		if($auction->GetStatus() == AuctionsModel\Auction::STATUS_BEGIN)
		{
			$makebid = new \Framework\HTML\Table();
			
			$currentBid = ($bid) ? $bid->bid : $auction->min_bid - 1;
			
			$makebid->AddDataRow("Fazer um lançe <span class='tooglePlus'></span>");
			$makebid->IsDropDownHeader();
	
			if($logged->getPremDays() > 0)
			{
				$bid_player_label = new UI\Label($this, "Personagem: ");
				
				$bid_player = new UI\Select($bid_player_label);
				$bid_player->SetName("bid_player");
				$bid_player->SetId("bid_player");
				$bid_player->AddOption("", null, null, true);
				
				foreach($logged->getCharacterList() as $name)
				{
					$bid_player->AddOption($name);
				}
				
				$bid_player_label->SetFor($bid_player->GetId());
				
				$bid_value_label = new UI\Label($this, "Digite o seu lançe:");
				
				$bid_value = new UI\Input($this);
				$bid_value->SetName("bid_value");
				$bid_value->SetId("bid_value");
				$bid_value->IsOnlyNumeric($currentBid + 1);
				$bid_value->SetValue($currentBid + 1);
				
				$bid_value_label->SetFor($bid_value->GetId());
				
				$button = new UI\Input($this);
				$button->IsButton();
				$button->SetValue("Enviar");
				$button->SetId("submit");
				
				$string = "
					
				<div style='text-align: center;'>
					
				<p>
				{$this->saveHTML($bid_player_label)}
				</p>
					
				<p>
				{$this->saveHTML($bid_value_label)}
				<p><small>Obs: O seu lançe deve ser maior que {$currentBid}.</small></p>
				{$this->saveHTML($bid_value)}
				</p>
				
				<p>
				{$this->saveHTML($button)}
				</p>
				
				</div>";				
			}
			else
			{
				$string = "
					
				<div style='text-align: center;'>
					
					<p>
						Você ainda não possui uma <a href='?ref=balance.purchase'>Conta Premium</a>. Adquira agora mesmo a sua e você poderá disputar itens em nosso leilão além de adquirir uma série de exclusividades no jogo!
					</p>
					
				</div>";	
			}	
			
			
			$makebid->AddField($string);
			$makebid->AddRow();		
		}
		
		
		global $module;
		
		$module = $this->saveHTML($bid_auction);
		$module .= $infos->Draw();
		
		if($logged && $logged->getAccess() == \t_Access::Administrator)
			$module .= "
			<a class='buttonstd' id='delete'>Remover</a> <a class='buttonstd' href='?ref=auctions.edit&id={$auction->id}'>Editar</a>
			";			
		
		if($auction->GetStatus() == AuctionsModel\Auction::STATUS_BEGIN)
			$module .= "
			<fieldset>
				{$makebid->Draw()}				
			</fieldset>			
			";
	}
}