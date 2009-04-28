<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	
	if(($account->loadByName($post[0], "password")) and ($account->getPassword() == $strings->encrypt($post[1])))
	{
		$_SESSION['login'][] = $account->getId();
		$_SESSION['login'][] = $strings->encrypt($post[1]);
		
		$core->redirect("index.php?ref=account.main");	
	}
	else
	{
		$error = "Numero da conta ou senha incorretos.";
	}
}

if($error)	
{
	$core->sendMessageBox("Erro!", $error);
}

$module .= '
<form action="" method="post">
	<fieldset>
		
		<p>
			<label for="account_name">Conta</label><br />
			<input name="account_name" size="40" type="password" value="" />
		</p>
		
		<p>
			<label for="account_password">Senha</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Login" />
		</p>
	</fieldset>
</form>';
?>