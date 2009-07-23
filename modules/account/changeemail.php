<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	if($account->get("password") != $strings->encrypt($post[1]))
	{
		$error = $boxMessage['CONFIRMATION_PASSWORD_FAIL'];
	}
	elseif($account->loadByEmail($post[0]))
	{
		$error = $boxMessage['EMAIL_ALREADY_IN_USE'];
	}			
	elseif(!$strings->validEmail($post[0]))
	{
		$error = $boxMessage['INVALID_EMAIL'];
	}
	elseif(is_array($newemail = $account->getEmailToChange()))
	{
		$error = $boxMessage['ACCOUNT_ALREADY_HAVE_CHANGE_EMAIL_REQUEST'];
	}
	else
	{		
		$account->addEmailToChange($post[0]);
		$newemail = $account->getEmailToChange();
		$success = $boxMessage['SUCCESS.CHANGE_EMAIL'];
	}
}

if($success)	
{
	$core->sendMessageBox("Sucesso!", $success);
}
else
{
	if($error)	
	{
		$core->sendMessageBox("Erro!", $error);
	}

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>
			<label for="account_newemail">'.$pages["ACCOUNT.CHANGE_EMAIL.NEW_EMAIL"].'</label><br />
			<input name="account_newemail" size="40" type="text" value="" />
		</p>
		
		<p>
			<label for="account_password">'.$pages["ACCOUNT.CHANGE_EMAIL.PASSWORD"].'</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
		</p>
	</fieldset>
</form>';

}
?>