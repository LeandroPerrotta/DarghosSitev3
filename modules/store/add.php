<?php
class View
{
	//html fields 
	private $_item_type,
			$_item_category,
			$_item_name,
			$_item_id,
			$_item_count,
			$_item_action_id,
			$_item_is_stackable,
			$_item_func,
			$_item_func_param,
			$_item_img_url,
			$_item_description,
			$_item_price
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
		
		$item = NULL;
		
		if(isset($_GET["edit"])){
			$item = new \Framework\ItemShop();
			
    		if(!$item->load($_GET["edit"]))
    		{
    			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
    			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
    			return false;
    		}
		}
		
		$isitem = $item ? (($item->getType() == \Framework\ItemShop::TYPE_ITEM) ? true : false) : false;
		$iscallback = $item ? (($item->getType() == \Framework\ItemShop::TYPE_CALLBACK) ? true : false) : false;
		$name = $item ? $item->getName() : "";
		$itemid = $item ? $item->getParams()[\Framework\ItemShop::PARAM_ITEM_ID] : "";
		$itemcount = $item ? $item->getParams()[\Framework\ItemShop::PARAM_ITEM_COUNT] : "";
		$actionid = $item ? $item->getParams()[\Framework\ItemShop::PARAM_ITEM_ACTION_ID] : "";
		$stackable = $item ? $item->getParams()[\Framework\ItemShop::PARAM_ITEM_STACKABLE] : false;
		$description = $item ? $item->getDescription() : "";
		$price = $item ? $item->getPrice() : "";
		
		$isequipment = $item ? (($item->getCategory() == \Framework\ItemShop::CATEGORY_EQUIPMENTS) ? true : false) : false;
		$isweapon = $item ? (($item->getCategory() == \Framework\ItemShop::CATEGORY_WEAPONS) ? true : false) : false;
		$isaddons = $item ? (($item->getCategory() == \Framework\ItemShop::CATEGORY_ADDONS) ? true : false) : false;
		$isvaluable = $item ? (($item->getCategory() == \Framework\ItemShop::CATEGORY_VALUABLE) ? true : false) : false;
		$isservice = $item ? (($item->getCategory() == \Framework\ItemShop::CATEGORY_SERVICES) ? true : false) : false;
		
		$this->_item_type = new \Framework\HTML\SelectBox();
		$this->_item_type->SetName("item_type");	
		$this->_item_type->AddOption("Item", \Framework\ItemShop::TYPE_ITEM, $isitem);
		$this->_item_type->AddOption("Callback", \Framework\ItemShop::TYPE_CALLBACK, $iscallback);
		
		$this->_item_category = new \Framework\HTML\SelectBox();
		$this->_item_category->SetName("item_category");
		$this->_item_category->SetSize(200);
		$this->_item_category->AddOption("Equipamentos", \Framework\ItemShop::CATEGORY_EQUIPMENTS, $isequipment);
		$this->_item_category->AddOption("Armas e Escudos", \Framework\ItemShop::CATEGORY_WEAPONS, $isweapon);		
		$this->_item_category->AddOption("Addons e Outfits", \Framework\ItemShop::CATEGORY_ADDONS, $isaddons);		
		$this->_item_category->AddOption("Valiosos e Diversos", \Framework\ItemShop::CATEGORY_VALUABLE, $isvaluable);		
		$this->_item_category->AddOption("Servi??os", \Framework\ItemShop::CATEGORY_SERVICES, $isservice);		
		
		$this->_item_name = new \Framework\HTML\Input();
		$this->_item_name->SetName("item_name");
		$this->_item_name->SetValue($name);
		
		$this->_item_id = new \Framework\HTML\Input();
		$this->_item_id->SetName("item_id");
		$this->_item_id->SetValue($itemid);
		$this->_item_id->SetSize(10);	
			
		$this->_item_count = new \Framework\HTML\Input();
		$this->_item_count->SetName("item_count");
		$this->_item_count->SetValue($itemcount);
		$this->_item_count->SetSize(10);		
		
		$this->_item_action_id = new \Framework\HTML\Input();
		$this->_item_action_id->SetName("item_action_id");	
		$this->_item_action_id->SetValue($actionid);	
		$this->_item_action_id->SetSize(10);		
		
		$this->_item_is_stackable = new \Framework\HTML\Input();
		$this->_item_is_stackable->IsCheackeable();
		if($stackable) $this->_item_is_stackable->IsDefault();
		$this->_item_is_stackable->SetName("item_is_stackable");		
		
		$this->_item_func = new \Framework\HTML\Input();
		$this->_item_func->SetName("item_func");	
			
		$this->_item_func_param = new \Framework\HTML\Input();
		$this->_item_func_param->SetName("item_func_param");		
		
		$this->_item_img_url = new \Framework\HTML\Input();
		$this->_item_img_url->SetName("item_img_url");		
		
		$this->_item_description = new \Framework\HTML\Input();
		$this->_item_description->IsTextArea(7, 50);
		$this->_item_description->SetName("item_description");	
		$this->_item_description->SetValue($description);	

		$this->_item_price = new \Framework\HTML\Input();
		$this->_item_price->SetName("item_price");		
		$this->_item_price->SetValue($price);		
		$this->_item_price->SetSize(10);				
		
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
	    $item = new \Framework\ItemShop();
	    
	    if(isset($_GET["edit"])){
	        if(!$item->load($_GET["edit"]))
	        {
	            $this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
	            return false;
	        }
	    }
	    
	    if(isset($_GET["delete"])){
	        if(!$item->load($_POST["item_id"]))
	        {
	            $this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
	            return false;
	        }
	        
	        $item->delete();
	        
	        $ret["error"] = false;
	        $ret["msg"] = "O item foi removido com sucesso!";
	        
	        echo json_encode($ret);
	        
	        die();	        
	    }
	    
		$name = $this->_item_name->GetPost();
		$category = $this->_item_category->GetPost();
		$description = $this->_item_description->GetPost();
		$price = $this->_item_price->GetPost();
		$type = $this->_item_type->GetPost();
		
		if($type == \Framework\ItemShop::TYPE_ITEM)
		{		
			$item_id = $this->_item_id->GetPost();
			$item_count = $this->_item_count->GetPost();
			$item_action_id = $this->_item_action_id->getPost();
			$item_is_stack = $this->_item_is_stackable->GetPost();
			
			if(!$item_id
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
    			|| ($item_action_id && !is_numeric($item_action_id))
		    )
		    {
		    	$this->_message = "Os campos de id do item, quantidade, action id precisam ser numericos.";
		    	return false;						
		    }			
		
		}
		elseif($type == \Framework\ItemShop::TYPE_CALLBACK)
		{
		    $item_func= $this->_item_func->GetPost();
		    $item_func_param = $this->_item_func_param->GetPost();
		    $item_img_url = $this->_item_img_url->getPost();

		    if(!$item_func)
		    {
		        $this->_message = "Preencha todos campos corretamente.";
		        return false;
		    }		    
		}
		
		if(!$name 
			|| !$description
			|| !$price
		)
		{
			$this->_message = "Preencha todos campos corretamente.";
			return false;						
		}
		
		if(!is_numeric($price))
		{
			$this->_message = "O pre??o precisam ser numericos.";
			return false;						
		}			
		
		$item->setName($name);
		$item->setCategory($category);
		$item->setType($type);
		$item->setDescription($description);
		$item->setPrice($price);
		
		$params = array();
		
		if($type == \Framework\ItemShop::TYPE_ITEM){
    		$params[\Framework\ItemShop::PARAM_ITEM_ID] = $item_id;
    		$params[\Framework\ItemShop::PARAM_ITEM_COUNT] = $item_count;
    		$params[\Framework\ItemShop::PARAM_ITEM_STACKABLE] = ($item_is_stack == "true") ? 1 : 0;
    		
    		if($item_action_id)
    			$params[\Framework\ItemShop::PARAM_ITEM_ACTION_ID] = $item_action_id;
		}
		elseif($type == \Framework\ItemShop::TYPE_CALLBACK){
		    $params[\Framework\ItemShop::PARAM_FUNCTION] = $item_func;
		    $params[\Framework\ItemShop::PARAM_PARAMETERS] = $item_func_param;
		    
		    if($item_img_url)
		        $params[\Framework\ItemShop::PARAM_IMAGE_URL] = $item_img_url;		    
		}
		
		$item->setParams($params);
		$item->setAddedIn(time());
		$item->setEnabled(true);
		$item->save();
		
		if(!isset($_GET["edit"]))
            $this->_message = "O item foi adicionado com sucesso.";
        else
            $this->_message = "O item foi editado com sucesso.";
	    
		return true;				
	}
	
	function Draw()
	{
		global $module;		
				
		$module .= "
		<script type='text/javascript'>				
		$(document).ready(function() {
		
		    $('select[name=item_type]').change(function(){
		        
		        var type = $('select[name=item_type] option:selected').val();
		        if(type == 0){ 
                    $('fieldset p.callback_param').each(function(){
	                    $(this).hide();
                    });
                    
                    $('fieldset p.item_param').each(function(){
	                    $(this).show();
                    });                    
                    
	            }else if(type == 1){
                    $('fieldset p.item_param').each(function(){
	                    $(this).hide();
                    });	     

                    $('fieldset p.callback_param').each(function(){
	                    $(this).show();
                    });                    
	            }
		    });

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
					<label for='item_category'>Categoria</label><br />
					{$this->_item_category->Draw()}
				</p>	
				
				<p class='item_param'>
					<label for='item_id'>Item ID (consulte no items.xml)</label><br />
					{$this->_item_id->Draw()}
				</p>			

				<p class='item_param'>
					<label for='item_count'>Quantidade</label><br />
					{$this->_item_count->Draw()}
				</p>		

				<p class='item_param'>
					<label for='item_count'>Action ID (necessario para alguns itens especiais, por padr??o deixe em branco)</label><br />
					{$this->_item_action_id->Draw()}
				</p>				
				
				<p class='item_param'>
					{$this->_item_is_stackable->Draw()} ?? agrupavel?<br />
				</p>	

				<p class='callback_param' style='display: none;'>
					<label for='item_func'>Fun????o</label><br />
					{$this->_item_func->Draw()}
				</p>				
				
				<p class='callback_param' style='display: none;'>
					<label for='item_func_param'>Parametros (separado por virgula)</label><br />
					{$this->_item_func_param->Draw()}
				</p>		
						
				<p class='callback_param' style='display: none;'>
					<label for='item_img_url'>URL da imagem</label><br />
					{$this->_item_img_url->Draw()}
				</p>				

				<p>
					<label for='item_description'>Descri????o</label><br />
					{$this->_item_description->Draw()}
				</p>
							
				<p>
					<label for='item_price'>Pre??o (insira o valor sem virgulas, por ex: R$ 10,00 digite 1000)</label><br />
					{$this->_item_price->Draw()}
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