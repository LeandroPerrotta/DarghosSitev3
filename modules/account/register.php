<?
if($_POST)
{	
	Core::extractPost();
	
	$account = new Account();
	$password = Strings::randKey(8, 1, "lower+number");
	
	//argumentos para e-mail
	$_arg = array();
	$_arg[] = stripslashes($_POST['account_name']);
	$_arg[] = $password;
	
	if(!$_POST['account_email'] or !$_POST['account_name'])
	{
		$error = Lang::Message(LMSG_FILL_FORM);
	}
	elseif(!$_POST['account_privacypolicy'])
	{
		$error = Lang::Message(LMSG_PRIVACY_POLICY);
	}
	elseif(strlen($_POST['account_name']) < 5 or strlen($_POST['account_name']) > 25)
	{
		$error = Lang::Message(LMSG_ACCOUNT_NAME_WRONG_SIZE);
	}
	elseif($account->loadByEmail($_POST['account_email']))
	{
		$error = Lang::Message(LMSG_ACCOUNT_EMAIL_ALREADY_USED);
	}		
	elseif($account->loadByName($_POST['account_name']))
	{
		$error = Lang::Message(LMSG_ACCOUNT_NAME_ALREADY_USED);
	}			
	elseif(!Strings::validEmail($_POST['account_email']))
	{
		$error = Lang::Message(LMSG_WRONG_EMAIL);
	}	
	elseif((USE_EMAILVALIDATION) and !Core::mail(EMAIL_REGISTER, $_POST['account_email'], $_arg))
	{
		$error = Lang::Message(LMSG_FAIL_SEND_EMAIL);
	}
	else
	{
		$account->setEmail($_POST['account_email']);
		$account->setPassword(Strings::encrypt($password));
		$account->setName($_POST['account_name']);
		$account->setCreation(time());
		
		$account->save();
	
		$success = Lang::Message(LMSG_ACCOUNT_REGISTERED);
	
		if(USE_EMAILVALIDATION)
			$success .= Lang::Message(LMSG_ACCOUNT_INFOS_SEND);
		else
			$success .= Lang::Message(LMSG_ACCOUNT_PASSWORD_IS, $password);
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


$module .=	'
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
			
			<p>
				<label for="account_email">'.$pages["ACCOUNT.REGISTER.EMAIL_ADDRESS"].'</label><br />
				<input id="account_email" name="account_email" size="40" type="text" value="" />
			</p>
			
			<p>
				<label for="account_email">'.$pages["ACCOUNT.REGISTER.ACCOUNT_NAME"].'</label><br />
				<input id="account_email" name="account_name" size="40" type="text" value="" />
			</p>			
				
			<p>
				<input name="account_privacypolicy" id="account_privacypolicy" type="checkbox" value="1" /> '.$pages["ACCOUNT.REGISTER.PRIVACY_POLICY_ACCEPT"].'
			</p>
			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
			</p>
		</fieldset>
	</form>';

}
?>	
