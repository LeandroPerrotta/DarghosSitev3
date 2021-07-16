<?
if($_POST)
{	
	$core->extractPost();
	
	$account = $core->loadClass("Account");
	$password = $strings->randKey(8, 1, "lower+number");
	
	//argumentos para e-mail
	$_arg = array();
	$_arg[] = stripslashes($_POST['account_name']);
	$_arg[] = $password;
	
	if(!$_POST['account_email'] or !$_POST['account_name'])
	{
		$error = $boxMessage['INCOMPLETE_FORM'];
	}
	elseif(!$_POST['account_privacypolicy'])
	{
		$error = $boxMessage['NEED_ACCEPT_PRIVACY_POLICY'];
	}
	elseif(strlen($_POST['account_name']) < 5 or strlen($_POST['account_name']) > 25)
	{
		$error = $boxMessage['ACCOUNT_NAME_INCORRECT_SIZE'];
	}
	elseif($account->loadByEmail($_POST['account_email']))
	{
		$error = $boxMessage['EMAIL_ALREADY_IN_USE'];
	}		
	elseif($account->loadByName($_POST['account_name']))
	{
		$error = $boxMessage['ACCOUNT_NAME_ALREADY_IN_USE'];
	}			
	elseif(!$strings->validEmail($_POST['account_email']))
	{
		$error = $boxMessage['INVALID_EMAIL'];
	}	
	elseif(!$core->mail(EMAIL_REGISTER, $_POST['account_email'], $_arg))
	{
		$error = $boxMessage['FAIL_SEND_EMAIL'];
	}
	else
	{
		$account->setEmail($_POST['account_email']);
		$account->setPassword($strings->encrypt($password));
		$account->setName($_POST['account_name']);
		$account->setCreation(time());
		
		$account->save();
	
		$success = $boxMessage['SUCCESS.REGISTER'];
	}
}

if($success)	
{
	$core->sendMessageBox($boxMessage['SUCCESS'], $success);
}
else
{
	if($error)	
	{
		$core->sendMessageBox($boxMessage['ERROR'], $error);
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
