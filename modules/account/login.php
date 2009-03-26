<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	
	if(!$strings->SQLInjection($post[0]))
	{
		$error = "Numero da conta ou senha incorretos.";
	}
	elseif(($account->load($post[0], "password")) and ($account->get("password") == $strings->encrypt($post[1])))
	{
		$_SESSION['login'][] = $post[0];
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
			<label for="account_number">Conta</label><br />
			<input name="account_number" size="40" type="password" value="" />
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