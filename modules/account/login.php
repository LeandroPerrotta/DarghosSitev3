<?
//echo ;
$post = Core::extractPost();
if($post)
{
	$account = new Account();
	
	if(($account->loadByName($post[0], "password")) and ($account->getPassword() == Strings::encrypt($post[1])))
	{
		$_SESSION['login'][] = $account->getId();
		$_SESSION['login'][] = Strings::encrypt($post[1]);
		
		Core::redirect("index.php?ref=account.main");	
	}
	else
	{
		$error = Lang::Message(LMSG_FAIL_LOGIN);
	}
}

if($error)	
{
	Core::sendMessageBox(Lang::Message(LMSG_ERROR), $error);
}

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>
			<label for="account_name">'.$pages["ACCOUNT.LOGIN.ACCOUNT_NAME"].'</label><br />
			<input name="account_name" size="40" type="password" value="" />
		</p>
		
		<p>
			<label for="account_password">'.$pages["ACCOUNT.LOGIN.PASSWORD"].'</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.$buttons['LOGIN'].'" />
		</p>
	</fieldset>
</form>';
?>