<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	if($account->get("password") != $strings->encrypt($post[1]))
	{
		$error = "Confirmação da senha falhou.";
	}
	elseif($account->loadByEmail($post[0]))
	{
		$error = "Este e-mail já esta em uso por outra conta em nosso banco de dados.";
	}			
	elseif(!$strings->validEmail($post[0]))
	{
		$error = "Este não é um e-mail valido.";
	}
	elseif(is_array($newemail = $account->getEmailToChange()))
	{
		$error = "Está conta já possui uma mudança de e-mail agendada.";
	}
	else
	{		
		$account->addEmailToChange($post[0]);
		$newemail = $account->getEmailToChange();
		
		$success = "
		<p>Caro jogador,</p>
		<p>A mudança de email de sua conta foi agendada com sucesso para {$core->formatDate($newemail['date'])}.</p>
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
			<label for="account_newemail">Novo e-mail</label><br />
			<input name="account_newemail" size="40" type="text" value="" />
		</p>
		
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