<?
if($_POST)
{
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
	}		
	elseif(!is_array($newemail = $account->getEmailToChange()))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHANGEEMAIL_NOTHING);
	}
	else
	{		
		$account->cancelEmailToChange();
		
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHANGEEMAIL_CANCELED);
	}
}

if($success)	
{
	\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $success);
}
else
{
	if($error)	
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
	}

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>
			<label for="account_password">Senha</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>