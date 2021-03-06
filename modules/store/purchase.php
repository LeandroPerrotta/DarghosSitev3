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
		
		$this->_itemlist_table = array();
		//$this->_itemlist_table[\Framework\ItemShop::CATEGORY_EQUIPMENTS] = new \Framework\HTML\Table();
		//$this->_itemlist_table[\Framework\ItemShop::CATEGORY_WEAPONS] = new \Framework\HTML\Table();
		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_ADDONS] = new \Framework\HTML\Table();
		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_VALUABLE] = new \Framework\HTML\Table();
		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_SERVICES] = new \Framework\HTML\Table();

		//$this->_itemlist_table[\Framework\ItemShop::CATEGORY_EQUIPMENTS]->AddField("Lista de Equipamentos");
		//$this->_itemlist_table[\Framework\ItemShop::CATEGORY_EQUIPMENTS]->AddRow();

		//$this->_itemlist_table[\Framework\ItemShop::CATEGORY_WEAPONS]->AddField("Lista de Armas e Escudos");
		//$this->_itemlist_table[\Framework\ItemShop::CATEGORY_WEAPONS]->AddRow();

		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_ADDONS]->AddField("Lista de Addons e Outfits");
		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_ADDONS]->AddRow();
		
		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_VALUABLE]->AddField("Lista de Valiosos e Diversos");
		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_VALUABLE]->AddRow();
		
		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_SERVICES]->AddField("Lista de Servi??os");
		$this->_itemlist_table[\Framework\ItemShop::CATEGORY_SERVICES]->AddRow();
		
		foreach($this->_itemlist_table as $table){
		    $table->AddField("", "10%", null, 2);
		    $table->AddField("<b>Nome</b>", "20%");
		    $table->AddField("<b>Descri????o</b>", "50%");
		    $table->AddField("<b>Pre??o</b>", "15%");
		    $table->AddRow();		    
		}
	
		if($this->_itemlist)
		{
			foreach($this->_itemlist as $item)
			{
				$item instanceof \Framework\ItemShop;
				
				$category = $item->getCategory();
				$params = $item->getParams();
				
				$table = $this->_itemlist_table[$category];
				
				$this->_selected_item->SetValue($item->getId());
				$table->AddField($this->_selected_item->Draw());
				
				$type = $item->getType();
	
			    if($type == \Framework\ItemShop::TYPE_CALLBACK){
			        
			        $table->AddField("<img src='{$params[\Framework\ItemShop::PARAM_IMAGE_URL]}'/>");
			        
			        $table->AddField("1x " . $item->getName());
			    }
			    elseif($type == \Framework\ItemShop::TYPE_ITEM){
			        
                    if($params[\Framework\ItemShop::PARAM_ITEM_STACKABLE])
			            $table->AddField("<img src='files/items/{$params[\Framework\ItemShop::PARAM_ITEM_ID]}_{$params[\Framework\ItemShop::PARAM_ITEM_COUNT]}.gif'/>");
			        else
			            $table->AddField("<img src='files/items/{$params[\Framework\ItemShop::PARAM_ITEM_ID]}.gif'/>");
			        	
			        $table->AddField($params[\Framework\ItemShop::PARAM_ITEM_COUNT]."x " . $item->getName());	
			    }
			
				
				$table->AddField($item->getDescription());
				$table->AddField($item->getPriceStr());

				$table->AddRow();
			}
			
			foreach($this->_itemlist_table as $table){
			    if($table->GetRowsCount() <= 2){
    			    $table->AddField("Nenhum item para esta categoria.", null, null, 5);
    			    $table->AddRow();
			    }
			}			
		}		
		else
		{		
		    foreach($this->_itemlist_table as $table){
    			$table->AddField("A nossa loja n??o possui nenhum item disponivel no momento.", null, null, 4);
    			$table->AddRow();	
		    }		    
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
		
		if(!\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_ITEM_SHOP, $player->getWorldId()))
		{
			$this->_message = "O nosso item shop n??o est?? habilitado para este mundo por enquanto.";
			return false;			
		}
		
		$item = new \Framework\ItemShop();
		if(!$item->load($selected_item))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;			
		}
		
		$item_prop = $item->getParams();
		
		if($item->getPrice() > $this->loggedAcc->getBalance())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->ITEMSHOP_COST, "R$ " . number_format($item->getPrice() / 100, 2));
			return false;
		}
		
		$received = 0;
		
		if($item->getType() == \Framework\ItemShop::TYPE_CALLBACK){
		
		    $params = $item->getParams();
		
		    $func_params = explode(",", $params[\Framework\ItemShop::PARAM_PARAMETERS]);
		    array_unshift($func_params, $this->loggedAcc, $player);
		
		    $callback = '\Framework\ItemShop::' . $params[\Framework\ItemShop::PARAM_FUNCTION];
		    if(method_exists('\Framework\ItemShop', $params[\Framework\ItemShop::PARAM_FUNCTION])){
		        $ret = call_user_func_array($callback, $func_params);
		        
		        if(!$ret["success"]){
		            $this->_message = $ret["msg"];
		            return false;
		        }
		        
		        if($item->getCategory() == \Framework\ItemShop::CATEGORY_SERVICES)
		          $received = 1;
		    }
		    else
		        echo "\Framework\ItemShop {$params[\Framework\ItemShop::PARAM_FUNCTION]}";
		}		
		
		$this->loggedAcc->addBalance(-$item->getPrice());
		$this->loggedAcc->save();
		
		//$item->doPlayerGiveThing($player->getId());
		$item->logItemPurchase($player->getId(), $received);
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->ITEMSHOP_PURCHASE_SUCCESS, $item_prop[\Framework\ItemShop::PARAM_ITEM_COUNT], $item->getName(), number_format($item->getPrice() / 100, 2));
		return true;		
	}
	
	function Draw()
	{
		global $module;		
		
		if($this->isAdmin)
		  \Core\Main::includeJavaScriptSource("views/store_purchase.js");
				
		$module .= "		
		<div style='margin-top: 5px; margin-bottom: 5px; display: block; width: 100%; text-align: right;'><a href='?ref=store.history'>Ver Historico</a></div>
		<form action='{$_SERVER['REQUEST_URI']}' method='post'>
			<fieldset>
	
				<p>Bem vindo a Loja do ".getConf(confEnum()->WEBSITE_NAME)."!</p>				
				<p>O saldo atual de sua conta ?? de: <big><b>R$ ".number_format($this->loggedAcc->getBalance() / 100, 2)."</b></big></p>				
				
				<p>
					<label>Personagem</label>
					{$this->_character->Draw()}
					
				</p>
				
                <div id='horizontalSelector'>
        			<span name='left_corner'></span>
        			<ul>
        			     <!--
        				<li name='equipments'><span>Equipamentos</span></li>
        				<li name='weapons'><span>Armas e Escudos</span></li>
        				-->
        				<li name='addons'><span>Addons</span></li>
        				<li name='valueable'><span>Valiosos e Diversos</span></li>
        				<li name='services' checked='checked'><span>Servi??os</span></li>
        			</ul>
        			<span name='right_corner'></span>
        		</div>
			    
			    <div title='addons' class='viewable' style='display: none; margin: 0px; padding: 0px;'>
			         {$this->_itemlist_table[\Framework\ItemShop::CATEGORY_ADDONS]->Draw()}
			    </div>
			    
			    <div title='valueable' class='viewable' style='display: none; margin: 0px; padding: 0px;'>
			         {$this->_itemlist_table[\Framework\ItemShop::CATEGORY_VALUABLE]->Draw()}
			    </div>
			    
			    <div title='services' class='viewable' style='margin: 0px; padding: 0px;'>
			         {$this->_itemlist_table[\Framework\ItemShop::CATEGORY_SERVICES]->Draw()}
			    </div>
				
				<p id='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' /> ".(($this->isAdmin) ? "<a class='buttonstd' href='?ref=store.add'>Novo Item</a> <a class='buttonstd' id='edit_item' href='#'>Editar Item</a> <a class='buttonstd' id='delete_item' href='#'>Excluir Item</a>" : null)."
				</p>
			</fieldset>
		</form>";					
	}
}

$view = new View();
?>