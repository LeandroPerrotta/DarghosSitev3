<?
$post = Core::extractPost();
if($post)
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	if($account->get("password") != Strings::encrypt($post[2]))
	{
		$error = Lang::Message(LMSG_WRONG_PASSWORD);
	}
	elseif($post[0] != $post[1])
	{
		$error = Lang::Message(LMSG_CHANGEPASS_WRONG_NEWPASS_CONFIRM);
	}
	elseif($post[0] == $post[2])
	{
		$error = Lang::Message(LMSG_CHANGEPASS_SAME_PASSWORD);
	}
	elseif(strlen($post[0]) < 6 or strlen($post[0]) > 20)
	{
		$error = Lang::Message(LMSG_CHANGEPASS_WRONG_NEWPASS_LENGHT);
	}
	else
	{
		$account->set("password", Strings::encrypt($post[0]));
		$account->save();
		
		$_SESSION[1] = $account->get("password");
		
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