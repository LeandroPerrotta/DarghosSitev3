<?php
class View
{
	//html fields 
	private $_itemlist_table, $_selected_item, $_character;
	
	//variables
	private $_message, $_itemlist;	
	
	//custom variables
	private $loggedAcc, $user, $isAdmin = false;	
	
	function View()
	{				
		if($_SESSION['login'])
		{
			$this->loggedAcc = new Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
			
			if($this->loggedAcc->getGroup() == GROUP_ADMINISTRATOR)
			{
				$this->isAdmin = true;
			}
		}
		else
		{
			return false;
		}		
		
		if(!$this->Prepare())
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;
		}
		
		if($_POST)
		{
			if(!$this->Post())
			{
				Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			}
			else
			{
				Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $this->_message);
				return true;
			}
		}		
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{				
		
		$this->_selected_item = new HTML_Input();
		$this->_selected_item->SetName("selected_item");
		$this->_selected_item->IsRadio();
		
		$this->_character = new HTML_SelectBox();
		$this->_character->SetName("character");
		
		foreach($this->loggedAcc->getCharacterList() as $k => $name)
		{
			$this->_character->AddOption($name);
			
			if($_GET["name"] && $_GET["name"] == $name)
			{
				$this->_character->SelectedIndex($k);
			}
		}
		
		$this->_itemlist = ItemShop::getItemShopList();
		
		$this->_itemlist_table = new HTML_Table();

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
				$item instanceof ItemShop;
				
				$params = $item->getParams();
				
				$this->_selected_item->SetValue($item->getId());
				$this->_itemlist_table->AddField($this->_selected_item->Draw());
				
				if($item->getType() == ItemShop::TYPE_ITEM)
				{
					if($params[ItemShop::PARAM_ITEM_STACKABLE])
						$this->_itemlist_table->AddField("<img src='files/items/{$params[ItemShop::PARAM_ITEM_ID]}_{$params[ItemShop::PARAM_ITEM_COUNT]}.gif'/>");
					else
						$this->_itemlist_table->AddField("<img src='files/items/{$params[ItemShop::PARAM_ITEM_ID]}.gif'/>");
				}				
				
				$this->_itemlist_table->AddField($params[ItemShop::PARAM_ITEM_COUNT]."x " . $item->getName());
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
			$this->_message = Lang::Message(LMSG_FILL_FORM);
			return false;
		}
		
		$character = new Character();
				
		if(!$character->loadByName($tmp_char) || $character->getAccountId() != $this->loggedAcc->getId())
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;
		}
		
		if($character->getOnline())
		{
			$this->_message = Lang::Message(LMSG_CHARACTER_NEED_OFFLINE);
			return false;			
		}
		
		$item = new ItemShop();
		if(!$item->load($selected_item))
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;			
		}
		
		$item_prop = $item->getParams();
		
		if($item->getPrice() > $this->loggedAcc->getPremDays())
		{
			$this->_message = Lang::Message(LMSG_ITEMSHOP_COST, $item_prop[ItemShop::PARAM_ITEM_COUNT]);
			return false;
		}
		
		$this->loggedAcc->updatePremDays($item->getPrice(), false);
		$this->loggedAcc->save();
		
		$item->doPlayerGiveThing($character->getId());
		
		$this->_message = Lang::Message(LMSG_ITEMSHOP_PURCHASE_SUCCESS, $item_prop[ItemShop::PARAM_ITEM_COUNT], $item->getName(), $item->getPrice());
		return true;		
	}
	
	function Draw()
	{
		global $module;		
				
		$module .= "		
		<form action='{$_SERVER['REQUEST_URI']}' method='post'>
			<fieldset>
	
				<p>Bem vindo ao item shop do Darghos, aqui você pode trocar dias de sua conta premium por itens especiais dentro do jogo.</p>
				<p>O sistema funciona de maneira automatica e no instante em que você finalizar a sua troca o <b>seu item já estará disponivel dentro do jogo em um depot localizado no ultimo andar do depot da cidade de Aracura</b>, caso você não saiba chegar até lá, basta ir em qualquer barco do jogo e dizer ao NPC as palavras: \"hi\", \"aracura\" e \"yes\".</p>					
				
				<p>
					<label> Personagem<br>
						{$this->_character->Draw()}
					</label>
				</p>
				
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