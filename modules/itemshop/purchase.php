<?php
class View
{
	//html fields 
	private $_itemlist_table, $_order_by, $_selected_item, $_character;
	
	//variables
	private $_message, $_itemlist;	
	
	//custom variables
	private $loggedAcc, $user, $isAdmin = false;	
	
	function View()
	{				
		if($_SESSION['login'])
		{
			$this->loggedAcc = new \Framework\Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
			
			if($this->loggedAcc->getGroup() == t_Group::Administrator)
			{
				$this->isAdmin = true;
			}
		}
		else
		{
			\Core\Main::requireLogin();
			return false;
		}		
		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;
		}
		
		if($_POST)
		{
			if(!$this->Post())
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			}
			else
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
				return true;
			}
		}		
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{				
		
		$this->_selected_item = new \Framework\HTML\Input();
		$this->_selected_item->SetName("selected_item");
		$this->_selected_item->IsRadio();
		
		$this->_character = new \Framework\HTML\SelectBox();
		$this->_character->SetName("character");
		$this->_character->SetSize(150);
		
		$this->_character->AddOption("");
		$this->_character->SelectedIndex(0);
		
		foreach($this->loggedAcc->getCharacterList() as $k => $name)
		{
			$this->_character->AddOption($name);
			
			if($_GET["name"] && $_GET["name"] == $name)
			{
				$this->_character->SelectedIndex($k);
			}
		}
			
		/*
		$this->_order_by = new \Framework\HTML\SelectBox();		
		$this->_order_by->SetName("order_by");
		$this->_order_by->AddOption("Mais vendidos",\Framework\ItemShop::LIST_ORDER_RELEVANCE);
		$this->_order_by->AddOption("Alfabeticamente",\Framework\ItemShop::LIST_ORDER_NAME);
		$this->_order_by->AddOption("Mais novos",\Framework\ItemShop::LIST_ORDER_NEWER);
		$this->_order_by->AddOption("Mais antigos",\Framework\ItemShop::LIST_ORDER_OLDER);
		$this->_order_by->AddOption("Mais caros",\Framework\ItemShop::LIST_ORDER_PRICE_DESC);
		$this->_order_by->AddOption("Mais baratos",\Framework\ItemShop::LIST_ORDER_PRICE_ASC);
		*/
		
		$this->_itemlist = \Framework\ItemShop::getItemShopList();
		
		$this->_itemlist_table = new \Framework\HTML\Table();

		$this->_itemlist_table->AddField("Lista de Items");
		$this->_itemlist_table->AddRow();	
		
		$this->_itemlist_table->AddField("", "10%", null, 2);
		$this->_itemlist_table->AddField("<b>Nome</b>", "20%");
		$this->_itemlist_table->AddField("<b>Descrição</b>", "50%");
		$this->_itemlist_table->AddField("<b>Premdays</b>", "15%");
		$this->_itemlist_table->AddRow();		
		
		if($this->_itemlist)
		{
			foreach($this->_itemlist as $item)
			{
				$item instanceof \Framework\ItemShop;
				
				$params = $item->getParams();
				
				$this->_selected_item->SetValue($item->getId());
				$this->_itemlist_table->AddField($this->_selected_item->Draw());
				
				if($item->getType() == \Framework\ItemShop::TYPE_ITEM)
				{
					if($params[\Framework\ItemShop::PARAM_ITEM_STACKABLE])
						$this->_itemlist_table->AddField("<img src='files/items/{$params[\Framework\ItemShop::PARAM_ITEM_ID]}_{$params[\Framework\ItemShop::PARAM_ITEM_COUNT]}.gif'/>");
					else
						$this->_itemlist_table->AddField("<img src='files/items/{$params[\Framework\ItemShop::PARAM_ITEM_ID]}.gif'/>");
				}				
				
				$this->_itemlist_table->AddField($params[\Framework\ItemShop::PARAM_ITEM_COUNT]."x " . $item->getName());
				$this->_itemlist_table->AddField($item->getDescription());
				$this->_itemlist_table->AddField($item->getPrice());

				$this->_itemlist_table->AddRow();
			}
		}		
		else
		{		
			$this->_itemlist_table->AddField("O nosso shop não possui nenhum item disponivel no momento.", null, null, 4);
			$this->_itemlist_table->AddRow();	
		}
			
		return true;
	}
	
	function Post()
	{
		$tmp_char = $this->_character->GetPost();
		$selected_item = $this->_selected_item->GetPost();
		
		if(!$tmp_char || !$selected_item)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
			return false;
		}
		
		$player = new \Framework\Player();
				
		if(!$player->loadByName($tmp_char) || $player->getAccountId() != $this->loggedAcc->getId())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;
		}
		
		if($player->getOnline())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NEED_OFFLINE);
			return false;			
		}
		
		if($player->getWorldId() == t_Worlds::RealMap)
		{
			$this->_message = "O nosso item shop não está habilitado para este mundo por enquanto.";
			return false;			
		}
		
		$item = new \Framework\ItemShop();
		if(!$item->load($selected_item))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;			
		}
		
		$item_prop = $item->getParams();
		
		if($item->getPrice() > $this->loggedAcc->getPremDays())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->ITEMSHOP_COST, $item_prop[\Framework\ItemShop::PARAM_ITEM_COUNT]);
			return false;
		}
		
		$this->loggedAcc->updatePremDays($item->getPrice(), false);
		$this->loggedAcc->save();
		
		//$item->doPlayerGiveThing($player->getId());
		$item->logItemPurchase($player->getId());
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->ITEMSHOP_PURCHASE_SUCCESS, $item_prop[\Framework\ItemShop::PARAM_ITEM_COUNT], $item->getName(), $item->getPrice());
		return true;		
	}
	
	function Draw()
	{
		global $module;		
				
		$module .= "		
		<div style='margin-top: 5px; margin-bottom: 5px; display: block; width: 100%; text-align: right;'><a href='?ref=itemshop.history'>Ver Historico</a></div>
		<form action='{$_SERVER['REQUEST_URI']}' method='post'>
			<fieldset>
	
				<p>Bem vindo ao item shop do Darghos, aqui você pode trocar dias de sua conta premium por itens especiais dentro do jogo.</p>
				<p>O sistema funciona de maneira automatica e no instante em que você efetuar o log-in no jogo <b>irá receber o(s) itens obtidos</b>.</p>					
				
				<p>
					<label>Personagem</label>
					{$this->_character->Draw()}
					
				</p>";
				
			/*		
				<p>
					<label>Ordenar por</label>
					{$this->_order_by->Draw()}
					
				</p>
			*/

		$module .= "
				{$this->_itemlist_table->Draw()}
				
				<p id='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' /> ".(($this->isAdmin) ? "<a class='buttonstd' href='?ref=itemshop.add'>Novo Item</a>" : null)."
				</p>
			</fieldset>
		</form>";					
	}
}

$view = new View();
?>