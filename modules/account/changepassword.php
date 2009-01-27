<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	if($account->get("password") != $core->encrypt($post[2]))
	{
		$error = "Confirmação da senha atual falhou.";
	}
	elseif($post[0] != $post[1])
	{
		$error = "Confirmação da nova senha falhou.";
	}
	elseif($post[0] == $post[2])
	{
		$error = "A nova senha deve ser diferente da senha atual.";
	}
	elseif(strlen($post[0]) < 6 or strlen($post[0]) > 20)
	{
		$error = "A nova senhas deve possuir de 5 a 20 caracteres.";
	}
	else
	{
		$account->set("password", $strings->encrypt($post[0]));
		$account->save();
		
		$_SESSION[1] = $account->get("password");
		
		$success = "A sua senha foi modificada com sucesso!";
	}
}

if($success)	
{
	$module .=	'
		
	<div id="sucesso">
		<h2>'.$success.'</h2>
	</div>
	
	';
}
else
{
	if($error)	
	{
		$module .=	'
		
		<div id="error">
			<h2>'.$error.'</h2>
		</div>
		
		';
	}

$module .= '
<form action="" method="post">
	<fieldset>
		
		<p>
			<label for="account_newpassword">Nova Senha</label><br />
			<input name="account_newpassword" size="40" type="password" value="" />
		</p>
		
		<p>
			<label for="account_confirmation">Confirmar nova Senha</label><br />
			<input name="account_confirmation" size="40" type="password" value="" />
		</p>	

		<p>
			<label for="account_password">Senha Atual</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>