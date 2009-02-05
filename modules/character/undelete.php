<?php
$post = $core->extractPost();

if($post)
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	$list = $account->getCharacterList();	
	
	$character = $core->loadClass("Character");
	$character->loadByName($post[0]);
	
	if($account->get("password") != $strings->encrypt($post[1]))
	{
		$error = "Confirmação da senha falhou.";
	}	
	elseif(!$character->deletionStatus())
	{
		$error = "Este personagem não está agendado para ser excluido.";
	}
	elseif(!in_array($post[0], $list))
	{	
		$error = "Este personagem não pertence a sua conta.";
	}
	else
	{
		$character->cancelDeletion();
		
		$success = "
		<p>Caro jogador,</p>
		<p>A exclusão do seu personagem {$post[0]} foi cancelada!</p>
		<p>Tenha um bom jogo!</p>
		";		
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

$module .=	'
	<form action="" method="post">
		<fieldset>

			<p>
				<label for="character_name">Personagem</label><br />
				<input readonly="readonly" name="character_name" size="40" type="text" value="'.$_GET['name'].'" />
			</p>		
		
			<p>
				<label for="account_password">Senha</label><br />
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