<?php
class View
{
	//html fields
	private $character, $_password;
	
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $topic, $user;	
	
	function View()
	{		
		if(!$_GET['name'])
			return false;		
		
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
		$this->loggedAcc = new Account();
		
		if(!$this->loggedAcc->load($_SESSION['login'][0]))
		{
			$this->_message = Lang::Message(LMSG_NEED_LOGIN);
			return false;			
		}		
		
		$this->character = new Character();
		
		if(!$this->character->loadByName($_GET["name"]))
		{
			$this->_message = Lang::Message(LMSG_CHARACTER_WRONG);
			return false;
		}
		
		if($this->character->getAccountId() != $this->loggedAcc->getId())
		{
			$this->_message = Lang::Message(LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT);
			return false;
		}		
		
		$this->_password = new HTML_Input();
		$this->_password->SetName("password");
		$this->_password->IsPassword();
		$this->_password->SetLabel("Confirmar senha");		
		
		return true;
	}
	
	function Post()
	{
		if($this->loggedAcc->getPassword() != Strings::encrypt($this->_password->getPost()))
		{
			$this->_message = Lang::Message(LMSG_FAIL_LOGIN);
			return false;			
		}
		
		if(!$_POST["stamina-value"] || !is_numeric($_POST["stamina-value"]) || $_POST["stamina-value"] <= floor($this->character->getStamina() / 1000 / 60 / 60) || $_POST["stamina-value"] > 42)
		{
			$this->_message = Lang::Message(LMSG_STAMINA_VALUE_WRONG);
			return false;			
		}
		
		if($this->character->getOnline() == 1)
		{
			$this->_message = Lang::Message(LMSG_CHARACTER_NEED_OFFLINE);
			return false;				
		}
		
		$staminachange = $_POST["stamina-value"] - floor($this->character->getStamina() / 1000 / 60 / 60);
		$newstamina = floor($this->character->getStamina() / 1000 / 60 / 60);
		$cost = 0;
		
		while($newstamina < $_POST["stamina-value"])
		{
			$newstamina++;
			
			if($newstamina >= 39)
				$cost += 12;
			else
				$cost += 4;
		}
		
		if($this->loggedAcc->getPremEnd() == 0 || $this->loggedAcc->getPremEnd() <= time() || floor($this->loggedAcc->getPremEnd() - time()) / 60 / 60 < $cost)
		{
			$this->_message = Lang::Message(LMSG_STAMINA_NOT_HAVE_PREMDAYS);
			return false;			
		}
	
		$this->loggedAcc->setPremDays($this->loggedAcc->getPremEnd() - ($cost * 60 * 60));
		$this->loggedAcc->save();
		
		$this->character->setStamina($_POST["stamina-value"] * 60 * 60 * 1000);
		$this->character->save();
		
		Core::$DB->query("INSERT INTO ".DB_WEBSITE_PREFIX."changelog (`type`,`player_id`,`value`,`time`) values ('stamina','{$this->character->getId()}','{$cost}','".time()."')");
		
		$this->_message = Lang::Message(LMSG_STAMINA_SUCCESSFULY, $this->character->getName(), $_POST["stamina-value"]);		
		return true;
	}
	
	function Draw()
	{
		global $module;

		$premleft = ($this->loggedAcc->getPremEnd() > 0 && $this->loggedAcc->getPremEnd() > time()) ? floor(($this->loggedAcc->getPremEnd() - time()) / 60 / 60) : 0;
		
		$module .= "		
		<script type='text/javascript'>
		var hourPrice = 4;
		price = 0;
		var bonusPrice = 12;
		var bonusStart = 39;
		var premleft = {$premleft};
		
		function updateStaminaBar(value)
		{
			var size = value * 10;	
			
			if(value <= 15)
			{
				$('#stamina-count').css('background-color', 'red');
			}
			else if(value <= 38)
			{
				$('#stamina-count').css('background-color', '#ff9600');
			}
			else
			{
				$('#stamina-count').css('background-color', '#54ff3d');
			}			
			
			$('#stamina-count').css('width', size + 'px');	
			$('#stamina-value').val(value);	
		}
		
		function incrementPrice(value)
		{
			if(value >= bonusStart)
			{
				price = price + bonusPrice;
			}
			else
			{
				price = price + hourPrice;
			}
			
			updateStaminaPrice(value);
		}
		
		function decrementPrice(value)
		{
			if((value + 1) >= bonusStart)
			{
				price = price - bonusPrice;
			}
			else
			{
				price = price - hourPrice;
			}
			
			updateStaminaPrice(value);
		}		
		
		function updateStaminaPrice(value)
		{				
		
			if(getMinStamina() == 42)
			{
				$('#stamina-price').val('nenhum');	
				$('#stamina-price').css('color', 'white');
				return;			
			}
			
			if(value == getMinStamina())
			{
				$('#stamina-price').val('nenhum');	
				$('#stamina-price').css('color', 'white');
				return;
			}
			
			if(price > premleft)
				$('#stamina-price').css('color', 'red');
			else
				$('#stamina-price').css('color', 'green');			
			
			if(price < 24)
			{
				$('#stamina-price').val(price + ' horas');	
				return;
			}
			
			if(price == 24)
			{
				$('#stamina-price').val('1 dia');	
				return;
			}			
			
			var days = 0;
			var rest = 0;
			
			var x = price;
			var result = true;
			
			while(result)
			{
				x -= 24;
				
				if(x >= 0)
				{
					days++;
					rest = x;
				}
				
				if(x < 0) { result = false; }
			}
			
			if(rest > 0)
			{
				$('#stamina-price').val(days + ' dias e ' + rest + ' horas');	
			}
			else
			{
				$('#stamina-price').val(days + ' dias');
			}
		}
		
		function getStaminaValue()
		{
			return parseInt($('#stamina-value').val());
		}
		
		function getMinStamina()
		{
			return parseInt($('#stamina-min').val());
		}
		
		$(document).ready(function() {	
			
			updateStaminaBar(getMinStamina());
			updateStaminaPrice(getMinStamina())
			
			$('#stamina-minus').click( function(){
				var newvalue = getStaminaValue() - 1;
				
				if(newvalue < getMinStamina())
				{
					return;
				}
					
				decrementPrice(newvalue);
				updateStaminaBar(newvalue);
			});
			
			$('#stamina-plus').click( function(){
				var newvalue = getStaminaValue() + 1;
	
				if(newvalue > 42)
				{
					return;
				}
				
				incrementPrice(newvalue);
				updateStaminaBar(newvalue);
			});
		});
		</script>
		
		<style>
		#stamina-bar{
			width: 420px;
			height: 10px;
			border: 1px solid black;
			display: inline-block;
		}
		
		#stamina-count{
			height: 10px;
		}
		
		#stamina-value, #stamina-min, #stamina-price{
			background-color: transparent;
			color: white;
			font-weight: bold;
			border: 1px solid black;
			width: 20px;
			text-align: center;
			margin-left: 5px;
			margin-right: 5px;
		}
		
		#stamina-price{
			width: 150px;
		}
		
		#stamina-minus, #stamina-plus{
			width: 22px;
			heidth: 22px;
			display: inline;
		}
		</style>		
		
		<form action='".$_SERVER['REQUEST_URI']."' method='POST'>
			<fieldset>
				
				<span>Quantidade de stamina (em horas) que o personagem <b>{$this->character->getName()}</b> possui agora:</span> <input type='text' readonly value='".floor($this->character->getStamina() / 1000 / 60 / 60)."' id='stamina-min'/>
			
				<div style='height: 25px; width: 490px; margin: 10px auto;'>
					<input id='stamina-minus' class='button' type='button' value='-'/>
					<div style='padding: 0px;' id='stamina-bar'>	
						<div style='padding: 0px;' id='stamina-count'></div>	
					</div>
					<input id='stamina-plus' class='button' type='button' value='+'/>
				</div>
				
				<span>Total de stamina (em horas) que o personagem ficará:</span> <input type='text' readonly value='3' id='stamina-value' name='stamina-value'/><br/>
				<span>Custo em premium time da operação:</span> <input type='text' readonly value='0' id='stamina-price'/>
				<p>{$this->_password->Draw()}</p>
				
				
				<div class='line'></div>
				
				<p><input id='btNext' class='button' type='submit' value='Proximo' /><p>
			</fieldset>
		</form>
		";		
	}
}

$view = new View();
?>