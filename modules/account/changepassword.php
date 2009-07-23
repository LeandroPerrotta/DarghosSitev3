<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	if($account->get("password") != $strings->encrypt($post[2]))
	{
		$error = $boxMessage['CURRENT_PASSWORD_FAIL'];
	}
	elseif($post[0] != $post[1])
	{
		$error = $boxMessage['NEW_PASSWORD_FAIL'];
	}
	elseif($post[0] == $post[2])
	{
		$error = $boxMessage['NEW_AND_CURRENT_PASSWORD_CAN_NOT_SAME'];
	}
	elseif(strlen($post[0]) < 6 or strlen($post[0]) > 20)
	{
		$error = $boxMessage['NEW_PASSWORD_INCORRECT_LENGHT'];
	}
	else
	{
		$account->set("password", $strings->encrypt($post[0]));
		$account->save();
		
		$_SESSION[1] = $account->get("password");
		
		$success = $boxMessage['SUCCESS.PASSWORD_CHANGED'];
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