<?
if($_POST)
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}
	elseif($_POST["account_newpassword"] != $_POST["account_confirmation"])
	{
		$error = Lang::Message(LMSG_CHANGEPASS_WRONG_NEWPASS_CONFIRM);
	}
	elseif($_POST["account_newpassword"] == $_POST["account_password"])
	{
		$error = Lang::Message(LMSG_CHANGEPASS_SAME_PASSWORD);
	}
	elseif(strlen($_POST["account_newpassword"]) < 6 or strlen($_POST["account_newpassword"]) > 20)
	{
		$error = Lang::Message(LMSG_CHANGEPASS_WRONG_NEWPASS_LENGHT);
	}
	else
	{
		$account->setPassword(Strings::encrypt($_POST["account_newpassword"]));
		$account->save();
		
		$_SESSION["login"][0] = $account->getPassword();
		
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
			<label for="account_newpassword">'.$pages["ACCOUNT.CHANGE_PASSWORD.NEW_PASSWORD"].'</label><br />
			<input name="account_newpassword" size="40" type="password" value="" />
		</p>
		
		<p>
			<label for="account_confirmation">'.$pages["ACCOUNT.CHANGE_PASSWORD.NEW_PASSWORD_CONFIRM"].'</label><br />
			<input name="account_confirmation" size="40" type="password" value="" />
		</p>	

		<p>
			<label for="account_password">'.$pages["ACCOUNT.CHANGE_PASSWORD.CURRENT_PASSWORD"].'</label><br />
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