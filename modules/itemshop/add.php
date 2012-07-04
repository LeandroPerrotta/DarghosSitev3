<?php
class View
{
	//html fields 
	private $_item_type,
			$_item_name,
			$_item_id,
			$_item_count,
			$_item_action_id,
			$_item_is_stackable,
			$_item_description,
			$_item_price,
			$_item_require_days
			;
	
	//variables
	private $_message, $_itemlist;	
	
	//custom variables
	private $loggedAcc;	
	
	function View()
	{		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;
		}
		
		$this->_item_type = new \Framework\HTML\SelectBox();
		$this->_item_type->SetName("item_type");	
		$this->_item_type->AddOption("Item", \Framework\ItemShop::TYPE_ITEM);
		
		$this->_item_name = new \Framework\HTML\Input();
		$this->_item_name->SetName("item_name");
		
		$this->_item_id = new \Framework\HTML\Input();
		$this->_item_id->SetName("item_id");	
		$this->_item_id->SetSize(10);	
			
		$this->_item_count = new \Framework\HTML\Input();
		$this->_item_count->SetName("item_count");	
		$this->_item_count->SetSize(10);		
		
		$this->_item_action_id = new \Framework\HTML\Input();
		$this->_item_action_id->SetName("item_action_id");	
		$this->_item_action_id->SetSize(10);		
		
		$this->_item_is_stackable = new \Framework\HTML\Input();
		$this->_item_is_stackable->IsCheackeable();
		$this->_item_is_stackable->SetValue("true");
		$this->_item_is_stackable->SetName("item_is_stackable");		
		
		$this->_item_description = new \Framework\HTML\Input();
		$this->_item_description->IsTextArea(7, 50);
		$this->_item_description->SetName("item_description");	

		$this->_item_price = new \Framework\HTML\Input();
		$this->_item_price->SetName("item_price");		
		$this->_item_price->SetSize(10);			
		
		$this->_item_require_days = new \Framework\HTML\Input();
		$this->_item_require_days->SetName("item_require_days");		
		$this->_item_require_days->SetSize(10);			
		
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
		if($_SESSION['login'])
		{
			$this->loggedAcc = new \Framework\Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
			
			if($this->loggedAcc->getGroup() == t_Group::Administrator)
			{
				return true;
			}
		}	
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
		return false;
	}
	
	function Post()
	{
		$name = $this->_item_name->GetPost();
		$description = $this->_item_description->GetPost();
		$price = $this->_item_price->GetPost();
		$require_days = $this->_item_require_days->GetPost();
		$type = $this->_item_type->GetPost();
		
		if($type == \Framework\ItemShop::TYPE_ITEM)
		{
			
			$item_id = $this->_item_id->GetPost();
			$item_count = $this->_item_count->GetPost();
			$item_action_id = $this->_item_action_id->getPost();
			$item_is_stack = $this->_item_is_stackable->GetPost();
		
			if(!$name 
				|| !$item_id
				|| !$item_count
				|| !$description
				|| !$price
			)
			{
				$this->_message = "Preencha todos campos corretamente.";
				return false;						
			}
			
			if(!is_numeric($item_id) 
				|| !is_numeric($item_count)
				|| !is_numeric($price)
				|| ($require_days && !is_numeric($require_days))
				|| ($item_action_id && !is_numeric($item_action_id))
			)
			{
				$this->_message = "Os campos de id do item, quantidade, action id e preço e requer dias precisam ser numericos.";
				return false;						
			}			
			
			$item = new \Framework\ItemShop();
			$item->setName($name);
			$item->setType($type);
			$item->setDescription($description);
			$item->setPrice($price);
			
			if($require_days())
				$item->setRequireDays($require_days);
			
			$params = array();
			$params[\Framework\ItemShop::PARAM_ITEM_ID] = $item_id;
			$params[\Framework\ItemShop::PARAM_ITEM_COUNT] = $item_count;
			$params[\Framework\ItemShop::PARAM_ITEM_STACKABLE] = ($item_is_stack == "true") ? 1 : 0;
			
			if($item_action_id)
				$params[\Framework\ItemShop::PARAM_ITEM_ACTION_ID] = $item_action_id;
			
			$item->setParams($params);
			$item->setAddedIn(time());
			$item->setEnabled(true);
			$item->save();
			
			$this->_message = "O item foi adicionado com sucesso.";
			return false;				
		}
	}
	
	function Draw()
	{
		global $module;		
				
		$module .= "
		<script type='text/javascript'>				
		$(document).ready(function() {
		

		});
		</script>
		
		<form action='{$_SERVER['REQUEST_URI']}' method='post'>
		<fieldset>
	
				<p>
					<label for='item_type'>Tipo</label><br />
					{$this->_item_type->Draw()}
				</p>					
				
				<p>
					<label for='item_name'>Nome</label><br />
					{$this->_item_name->Draw()}
				</p>	
				
				<p>
					<label for='item_id'>Item ID</label><br />
					{$this->_item_id->Draw()}
				</p>			

				<p>
					<label for='item_count'>Quantidade</label><br />
					{$this->_item_count->Draw()}
				</p>		

				<p>
					<label for='item_count'>Action ID (necessario para alguns itens)</label><br />
					{$this->_item_action_id->Draw()}
				</p>				
				
				<p>
					{$this->_item_is_stackable->Draw()} É agrupavel?<br />
				</p>			

				<p>
					<label for='item_description'>Descrição</label><br />
					{$this->_item_description->Draw()}
				</p>
							
				<p>
					<label for='item_price'>Preço (premdays)</label><br />
					{$this->_item_price->Draw()}
				</p>
							
				<p>
					<label for='item_price'>Requer (dias minimos na conta para comprar)</label><br />
					{$this->_item_require_days->Draw()}
				</p>
				
				<p id='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' />
				</p>
			</fieldset>
		</form>";					
	}
}

$view = new View();
?>