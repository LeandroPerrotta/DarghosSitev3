<?
if($_POST)
{
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}		
	elseif(!is_array($newemail = $account->getEmailToChange()))
	{
		$error = Lang::Message(LMSG_CHANGEEMAIL_NOTHING);
	}
	else
	{		
		$account->cancelEmailToChange();
		
		$success = Lang::Message(LMSG_CHANGEEMAIL_CANCELED);
	}
}

if($success)	
{
	Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $success);
}
else
{
	if($error)	
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), $error);
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