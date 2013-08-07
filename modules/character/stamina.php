<?php
class View
{
	//html fields
	private $player, $_password;
	
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
		$this->loggedAcc = new \Framework\Account();
		
		if(!$this->loggedAcc->load($_SESSION['login'][0]))
		{
			\Core\Main::requireLogin();
			return false;			
		}		
		
		$this->player = new \Framework\Player();
		
		if(!$this->player->loadByName($_GET["name"]))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_WRONG);
			return false;
		}
		
		if($this->player->getAccountId() != $this->loggedAcc->getId())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_FROM_YOUR_ACCOUNT);
			return false;
		}		
		
		$this->_password = new \Framework\HTML\Input();
		$this->_password->SetName("password");
		$this->_password->IsPassword();
		$this->_password->SetLabel("Confirmar senha");		
		
		return true;
	}
	
	function Post()
	{
		if($this->loggedAcc->getPassword() != \Core\Strings::encrypt($this->_password->getPost()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FAIL_LOGIN);
			return false;			
		}
		
		if(!$_POST["stamina-value"] || !is_numeric($_POST["stamina-value"]) || $_POST["stamina-value"] <= floor($this->player->getStamina() / 1000 / 60 / 60) || $_POST["stamina-value"] > 42)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->STAMINA_VALUE_WRONG);
			return false;			
		}
		
		if($this->player->getOnline() == 1)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NEED_OFFLINE);
			return false;				
		}
		
		$staminachange = $_POST["stamina-value"] - floor($this->player->getStamina() / 1000 / 60 / 60);
		$newstamina = floor($this->player->getStamina() / 1000 / 60 / 60);
		$cost = 0;
		
		while($newstamina < $_POST["stamina-value"])
		{
			$newstamina++;
			
			if($newstamina >= 39)
				$cost += 400;
			else
				$cost += 30;
		}
		
		if($this->loggedAcc->getBalance() == 0 || $this->loggedAcc->getBalance() < $cost)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->STAMINA_NOT_HAVE_PREMDAYS);
			return false;			
		}
	
		$this->loggedAcc->addBalance(-$cost);
		$this->loggedAcc->save();
		
		$this->player->setStamina($_POST["stamina-value"] * 60 * 60 * 1000);
		$this->player->save();
				
		\Core\Main::addChangeLog('stamina', $this->player->getId(), $cost);
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->STAMINA_SUCCESSFULY, $this->player->getName(), $_POST["stamina-value"]);		
		return true;
	}
	
	function Draw()
	{
		global $module;

		$balance = $this->loggedAcc->getBalance();
		$balance_str = "R$ " . number_format($balance / 100, 2);
		
		$module .= "		
		<script type='text/javascript'>
		var hourPrice = 30;
		price = 0;
		var bonusPrice = 400;
		var bonusStart = 39;
		var balance = {$balance};
		
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
			
			if(price > balance)
				$('#stamina-price').css('color', 'red');
			else
				$('#stamina-price').css('color', 'green');			
			
		    $('#stamina-price').val('R$ ' + (price / 100).toFixed(2));	
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
		
		#stamina-value, #stamina-min, #stamina-price, #account-balance{
			background-color: transparent;
			color: white;
			font-weight: bold;
			border: 1px solid black;
			width: 20px;
			text-align: center;
			margin-left: 5px;
			margin-right: 5px;
		}
		
		#stamina-price, #account-balance{
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
				
				<span>Quantidade de stamina (em horas) que o personagem <b>{$this->player->getName()}</b> possui agora:</span> <input type='text' readonly value='".floor($this->player->getStamina() / 1000 / 60 / 60)."' id='stamina-min'/>
			
				<div style='height: 25px; width: 490px; margin: 10px auto;'>
					<input id='stamina-minus' class='button' type='button' value='-'/>
					<div style='padding: 0px;' id='stamina-bar'>	
						<div style='padding: 0px;' id='stamina-count'></div>	
					</div>
					<input id='stamina-plus' class='button' type='button' value='+'/>
				</div>
				
				<span>Total de stamina (em horas) que o personagem ficará:</span> <input type='text' readonly value='3' id='stamina-value' name='stamina-value'/><br/>
				<span>Saldo disponível em sua conta:</span> <input type='text' readonly value='{$balance_str}' id='account-balance'/><br/>
				<span>Custo em saldo da conta deste serviço:</span> <input type='text' readonly value='0' id='stamina-price'/>
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