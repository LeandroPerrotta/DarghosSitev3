<?
if($_POST)
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	if($account->get("password") != Strings::encrypt($_POST["account_password"]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}
	elseif($account->loadByEmail($_POST["account_newemail"]))
	{
		$error = Lang::Message(LMSG_ACCOUNT_EMAIL_ALREADY_USED);
	}			
	elseif(!Strings::validEmail($_POST["account_newemail"]))
	{
		$error = Lang::Message(LMSG_WRONG_EMAIL);
	}
	elseif(is_array($newemail = $account->getEmailToChange()))
	{
		$error = Lang::Message(LMSG_CHANGEEMAIL_ALREADY_HAVE_REQUEST);
	}
	else
	{		
		$account->addEmailToChange($_POST["account_newemail"]);
		$newemail = $account->getEmailToChange();
		$success = Lang::Message(LMSG_CHANGEEMAIL_SCHEDULED);
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