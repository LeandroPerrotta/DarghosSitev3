<?
$post = $core->extractPost();
if($post)
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0]);
	
	if($account->getPassword() != $strings->encrypt($_POST['account_password']))
	{
		$error = "Confirmação da senha falhou.";
	}
	elseif(strlen($_POST['account_name']) < 5 or strlen($_POST['account_name']) > 25)
	{
		$error = "O nome de sua conta deve possuir entre 5 e 25 caracteres.";
	}	
	elseif($account->getName() == $_POST['account_name'])
	{
		$error = "O nome de sua conta deve ser diferente do seu antigo numero.";
	}
	elseif($account->loadByName($_POST['account_name']))
	{
		$error = "Este nome já esta em uso por outra conta em nosso banco de dados.";
	}			
	else
	{		
		$account->setName($_POST['account_name']);
		$account->save();
		
		$success = "
		<p>Caro jogador,</p>
		<p>A sua conta agora possui um Nome configurado corretamente!</p>
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
			<label for="account_name">Nome da Conta</label><br />
			<input name="account_name" size="40" type="text" value="" />
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