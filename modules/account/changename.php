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
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		
		$this->_password = new HTML_Input();
		$this->_password->SetName("account_password");
		$this->_password->IsPassword();
		
		$this->_name = new HTML_Input();
		$this->_name->SetName("account_name");			
		
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
		$this->loggedAcc->load($_SESSION['login'][0]);		

		if($this->loggedAcc->getPremDays() == 0)
		{
			$this->_message = Lang::Message(LMSG_NEED_PREMIUM);
			return false;
		}
		
		return true;
	}
	
	function Post()
	{
		$newacc = new Account();
		
		$password = $this->_password->GetPost();
		$name = $this->_name->GetPost();
		
		$checkName = Ajax_account::checkName();
		
		if($checkName["error"])
		{
			$this->_message = $checkName["text"];
			return false;				
		}
		
		if($this->loggedAcc->getPassword() != Strings::encrypt($password))
		{
			$this->_message = Lang::Message(LMSG_WRONG_PASSWORD);
			return false;			
		}
		
		if($this->loggedAcc->getPremDays() < 15)
		{
			$this->_message = Lang::Message(LMSG_OPERATION_NEED_PREMDAYS, 15);
			return false;	
		}
		
		$this->loggedAcc->setName($name);
		$this->loggedAcc->updatePremDays(15, false);
		$this->loggedAcc->save();
		
		Core::addChangeLog('acc_rename', $this->loggedAcc->getId(), $name);
		$this->_message = Lang::Message(LMSG_ACCOUNT_CHANGENAME_SUCCESS, $name);
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