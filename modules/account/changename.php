<?php
class View
{
	//html fields
	private $_name, $_password;
	
	//variables
	private $_message;		
	
	//custom variables
	private $loggedAcc;		
	
	function View()
	{		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}
		
		$this->_password = new \Framework\HTML\Input();
		$this->_password->SetName("account_password");
		$this->_password->IsPassword();
		
		$this->_name = new \Framework\HTML\Input();
		$this->_name->SetName("account_name");			
		
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
		$this->loggedAcc->load($_SESSION['login'][0]);		

		if($this->loggedAcc->getPremDays() == 0)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->NEED_PREMIUM);
			return false;
		}
		
		return true;
	}
	
	function Post()
	{
		$newacc = new \Framework\Account();
		
		$password = $this->_password->GetPost();
		$name = $this->_name->GetPost();
		
		$checkName = \Framework\Account::checkName();
		
		if($checkName["error"])
		{
			$this->_message = $checkName["text"];
			return false;				
		}
		
		if($this->loggedAcc->getPassword() != \Core\Strings::encrypt($password))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;			
		}
		
		if($this->loggedAcc->getPremDays() < 15)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->OPERATION_NEED_PREMDAYS, 15);
			return false;	
		}
		
		$this->loggedAcc->setName($name);
		$this->loggedAcc->updatePremDays(15, false);
		$this->loggedAcc->save();
		
		\Core\Main::addChangeLog('acc_rename', $this->loggedAcc->getId(), $name);
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_CHANGENAME_SUCCESS, $name);
		return true;
	}
	
	function Draw()
	{
		global $module;		
		
		$module .= "
		<form action='' method='post'>
			<fieldset>

				<p>Bem vindo a seção de mudança de nome de conta. Este é um serviço especial oferecido para que você possa renomear a sua conta.</p>
				<p><b>Como este é um serviço especial, para cada mudança de nome terá um custo de 15 premdays, que será descontado automaticamente de sua conta no final da operação.</b></p>	
				<p><b>Obs:</b></p>	
				<p> • O sistema é sensivel ao uso de letras maiusculas e minusculas, o que significa que MeuNovoNome é diferente de meunovonome que também é diferente de MEUNOVONOME.</p>	

				<p>
					<label for='account_name'>Novo nome</label><br />
					{$this->_name->Draw()}
				</p>				
				
				<p>
					<label for='account_password'>Confirmar senha</label><br />
					{$this->_password->Draw()}
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