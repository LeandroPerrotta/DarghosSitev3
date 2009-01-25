<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	
	if(($account->load($post[0], "password")) and ($account->get("password") == $core->encrypt($post[1])))
	{
		$_SESSION['login'][] = $post[0];
		$_SESSION['login'][] = $post[1];
		
		$core->redirect("index.php?ref=account.main");	
	}
	else
	{
		$error = "Numero da conta ou senha incorretos.";
	}
}

if($error)	
{
	echo '
	
	<div id="error">
		<h2>'.$error.'</h2>
	</div>
	
	';
}
?>

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
			<input type="submit" value="Login" />
		</p>
	</fieldset>
</form>