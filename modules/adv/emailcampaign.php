<?php
use \Core\Configs;
class View
{
	//html fields
	private $_subject, $_content, $_sendToAccounts, $_accountsQuanty, $_inactiveDays, $_extraEmails; 
	
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $user;	
	
	function View()
	{		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;
		}
		
		if($_SESSION['login'])
		{
			$this->loggedAcc = new \Framework\Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
		}		
		
		$this->_subject = new \Framework\HTML\Input();
		$this->_subject->SetName("subject");
		
		$this->_content = new \Framework\HTML\Input();
		$this->_content->IsTextArea(7, 50);
		$this->_content->SetName("content");
		
		$this->_sendToAccounts = new \Framework\HTML\Input();
		$this->_sendToAccounts->IsCheackeable();
		$this->_sendToAccounts->SetValue("true");
		$this->_sendToAccounts->SetName("sendToAccounts");
		
		$this->_accountsQuanty = new \Framework\HTML\Input();
		$this->_accountsQuanty->SetName("accountsQuanty");
		$this->_accountsQuanty->SetSize(10);
		
		$this->_inactiveDays = new \Framework\HTML\Input();
		$this->_inactiveDays->SetName("inactiveDays");
		$this->_inactiveDays->SetSize(10);	

		$this->_extraEmails = new \Framework\HTML\Input();
		$this->_extraEmails->IsTextArea(7, 50);
		$this->_extraEmails->SetName("extraEmails");		
		
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
		$this->user = new \Framework\Forums\User();
		
		if(!$this->user->LoadByAccount($_SESSION['login'][0]))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_ACCOUNT_NOT_HAVE_USER);
			return false;			
		}
		
		return true;
	}
	
	function sendEmail($subject, $content, $to, $from = NULL) 
	{		
		if(!$from)
			$from = Configs::Get(Configs::eConf()->WEBSITE_URL);
		
		$mail = new PHPMailer();
		
		$mail->IsHTML(true);
		$mail->IsSMTP();
		//$mail->SMTPDebug = true;

		$mail->SMTPAuth   = true;
		$mail->Host       = Configs::Get(Configs::eConf()->SMTP_HOST);
		$mail->Port       = Configs::Get(Configs::eConf()->SMTP_PORT);

		$mail->Username   = Configs::Get(Configs::eConf()->SMTP_USER);
		$mail->Password   = Configs::Get(Configs::eConf()->SMTP_PASSWORD);
			
		$mail->FromName = Configs::Get(Configs::eConf()->WEBSITE_NAME);
		$mail->From = Configs::Get(Configs::eConf()->SMTP_USER);
			
		$mail->AddAddress($to);

		$mail->Subject = $subject;
		$mail->Body = $content;
	
		return $mail->Send();
	}		
	
	function Post()
	{
		if(!$this->_subject->GetPost() || !$this->_content->GetPost() || (!$this->_sendToAccounts->GetPost() && !$this->_extraEmails->GetPost()))
		{
			$this->_message = "Preencha todos campos corretamente.";
			return false;			
		}		
		
		$emailBody = "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"> 
		
		<html xmlns=\"http://www.w3.org/1999/xhtml\"> 
		
		<head> 
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
			<title>Darghos Server</title>
		</head> 

		<body>
			{$this->_content->GetPost()}
		</body>
		</html>	
		";
		
		$emailsList = array();
		
		if($this->_extraEmails->GetPost())
		{
			$emailsList = explode(";", $this->_extraEmails->GetPost());
			
			foreach($emailsList as $email)
			{
				if(!$this->sendEmail($this->_subject->GetPost(), $emailBody, $email))
				{
					$this->_message = "Falha ao enviar e-mail, favor checar configurações de SMTP.";
					return false;
				}
			}
		}
		
		$this->_message = "A campanha de email foi enviada com sucesso!";
		return true;			
	}
	
	function Draw()
	{
		global $module;		
				
		$module .= "		
		<script type='text/javascript'>				
		$(document).ready(function() {
		
			var subpanel1 = false;
		
			$('.subpanel1').hide();
		
			$('input[name=sendToAccounts]').change(function() {
			
				if(!subpanel1)
				{
					$('.subpanel1').show();
					subpanel1 = true;
				}
				else
				{
					$('.subpanel1').hide();
					subpanel1 = false;
				}
			});
		});
		</script>		
		
		<form action='{$_SERVER['REQUEST_URI']}' method='post'>
			<fieldset>
	
				<p>
					<label for='subject'>Assunto</label><br />
					{$this->_subject->Draw()}
				</p>					
				
				<p>
					<label for='content'>Conteudo</label><br />
					{$this->_content->Draw()}
				</p>

				<p>
					{$this->_sendToAccounts->Draw()} Quero enviar emails a contas cadastradas no servidor.<br />
				</p>				

				<div class='subpanel1' style='margin: 0px; margin-top: 20px; padding: 0px;'>							
					<p>
						<label for='accountsQuanty'>Quantidade de contas que a campanha deve ser enviada</label><br />
						{$this->_accountsQuanty->Draw()}
					</p>				
					
					<p>
						<label for='inactiveDays'>Enviar a contas inativas a mais de</label><br />
						{$this->_inactiveDays->Draw()} dias
					</p>					
				</div>

				<p>
					<label for='extraEmails'>Emails extra que a campanha deve ser enviada (separado por um ;)</label><br />
					{$this->_extraEmails->Draw()}
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