<?
if($_POST)
{
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$checkPassword = Ajax_account::checkPassword();
	
	if($account->getPassword() != Strings::encrypt($_POST["account_password_current"]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}
	elseif($checkPassword["error"])
	{
		$error = $checkPassword["text"];
	}
	elseif(Strings::encrypt($_POST["account_password"]) == $account->getPassword())
	{
		$error = Lang::Message(LMSG_CHANGEPASS_SAME_PASSWORD);
	}
	else
	{
		$account->setPassword(Strings::encrypt($_POST["account_password"]));
		$account->save();
		
		$_SESSION["login"] = array();
		
		$_SESSION["login"][] = $account->getId();
		$_SESSION["login"][] = $account->getPassword();
		
		$success = Lang::Message(LMSG_ACCOUNT_PASSWORD_CHANGED);
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
			<label for="account_password">'.$pages["ACCOUNT.CHANGE_PASSWORD.NEW_PASSWORD"].'</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>
		
		<p>
			<label for="account_confirm_password">'.$pages["ACCOUNT.CHANGE_PASSWORD.NEW_PASSWORD_CONFIRM"].'</label><br />
			<input name="account_confirm_password" size="40" type="password" value="" />
		</p>	

		<p>
			<label for="account_password_current">'.$pages["ACCOUNT.CHANGE_PASSWORD.CURRENT_PASSWORD"].'</label><br />
			<input name="account_password_current" size="40" type="password" value="" />
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
		</p>
	</fieldset>
</form>';

}
?>