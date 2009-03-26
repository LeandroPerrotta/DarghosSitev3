<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	if($account->get("password") != $strings->encrypt($post[0]))
	{
		$error = "Confirma��o da senha falhou.";
	}		
	elseif(!is_array($newemail = $account->getEmailToChange()))
	{
		$error = "Est� conta n�o possui nenhuma mudan�a de email a ser cancelada.";
	}
	else
	{		
		$account->cancelEmailToChange();
		
		$success = "
		<p>Caro jogador,</p>
		<p>As mudan�as de e-mail agendadas para sua conta foram canceladas com exito! Nenhuma mudan�a de e-mail ocorrera em sua conta.</p>
		<p>Tenha um bom jogo!</p>
		";
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
<form action="" method="post">
	<fieldset>
		
		<p>
			<label for="account_password">Senha</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>