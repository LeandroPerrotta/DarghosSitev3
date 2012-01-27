<?php
class View
{
	//html fields
	private $_account_email, $_account_password;
	
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
		
		if($_GET['code'])
		{			
			$success = false;
			if(!$this->loggedAcc || !$this->loggedAcc->activateEmailByCode($_GET['code']))
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CAN_NOT_VALIDATE_EMAIL);
			}
			else
			{	
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->VALIDATE_EMAIL_SUCCESSFULY, $this->loggedAcc->getEmail());
				$success = true;
			}
			
			if($success)	
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
			}
			else
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			}	
			
			return true;
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
		$this->_account_password = new \Framework\HTML\Input();
		$this->_account_password->SetName("account_password");		
		$this->_account_password->IsPassword();		
		$this->_account_password->SetSize(\Framework\HTML\Input::SIZE_SMALL);

		$this->_account_email = new \Framework\HTML\Input();
		$this->_account_email->SetName("account_email");	
		$this->_account_email->SetSize(\Framework\HTML\Input::SIZE_SMALL);	
		
		$this->loggedAcc = \Framework\Account::loadLogged();
		
		if($this->loggedAcc->getEmail())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_ALREADY_VALIDATED_EMAIL);
			return false;
		}
		
		return true;
	}
	
	function Post()
	{
		$email = $this->_account_email->GetPost();
		$password = $this->_account_password->GetPost();		
	
		if(\Core\Strings::isNull($email) || \Core\Strings::isNull($password))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
			return false;
		}
		
		if($this->loggedAcc->getPassword() != \Core\Strings::encrypt($password))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			return false;
		}
		
		if(!\Core\Strings::validEmail($email))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_EMAIL);
			return false;
		}
		
		$account = new \Framework\Account();
		
		if($account->loadByEmail($email))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_EMAIL_ALREADY_USED);
			return false;
		}
		
		$code = \Core\Strings::randKey(12, 1, "lower+number");
		
		if(!\Core\Emails::send($email, \Core\Emails::EMSG_VALIDATE_EMAIL, array($code)))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FAIL_SEND_EMAIL);
			return false;
		}	
		
		$this->loggedAcc->addEmailValidate($email, $code);		
		
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_VALIDATING_EMAIL_SEND, $email);		
		return true;
	}
	
	function Draw()
	{
		global $module;		
		
		$module .= "
		<form action='' method='post'>
			<fieldset>

				<p>Atravez deste formulario você irá registrar um e-mail em sua conta. O processo é simples e não costuma levar mais do que um minuto.</p>
				<p>Após preenchido o formulario, será enviado uma mensagem ao e-mail informado, nesta mensagem haverá um link, que ao ser acessado, irá ativar este endereço de e-mail em sua conta.</p>	
				<p><span id='notify'>Atenção:</span> É comum que em alguns serviços de e-mail nossas mensagens levem vários minutos (até horas) para chegar ou sejam recebidas na caixa de \"Spam\" (ie: Lixo Eletronico).</p>
				<p class='long-margin-top'> • Se você estiver tendo problemas para receber o e-mail de ativação em seu endereço de e-mail, não possui uma conta de e-mail ou está em duvida em qual ultilizar, nos recomendamos o uso do <a href='http://mail.google.com'>Google Mail</a> (GMail).</p>	

				<p>
					<label for='account_email'>Endereço de E-mail</label><br />
					{$this->_account_email->Draw()}
				</p>				
				
				<p>
					<label for='account_password'>Confirmar senha</label><br />
					{$this->_account_password->Draw()}
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