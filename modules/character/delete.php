<?php
$post = $core->extractPost();

$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "password");

$list = $account->getCharacterList();

if($post)
{
	$character = $core->loadClass("Character");
	$character->loadByName($post[0]);
	
	if($account->get("password") != $strings->encrypt($post[1]))
	{
		$error = "Confirmação da senha falhou.";
	}	
	elseif($character->deletionStatus())
	{
		$error = "Este personagem já está agendado para ser deletado em sua conta.";
	}
	elseif(!in_array($post[0], $list))
	{	
		$error = "Este personagem não pertence a sua conta.";
	}
	else
	{
		$character->addToDeletion();
		
		$success = "
		<p>Caro jogador,</p>
		<p>Foi agendado com sucesso a exclusão de seu personagem {$post[0]} para o dia {$core->formatDate($character->deletionStatus())}!</p>
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
			
			<p>Selecione abaixo qual personagem de sua conta você deseja agendar uma exclusão. Este agendamento leva 30 dias e pode ser cancelado a qualquer momento dentro deste periodo. Note que após passado o periodo de 30 dias é impossivel cancelar a exclusão, recuperar o personagem ou qualquer um de seus pertences.</p>		
		
			<p>
				<label for="account_email">Personagem</label><br />
				<select name="character_name">
					';

if(is_array($list))
{	
	foreach($list as $pid)
	{
		$module .=	'<option value="'.$pid.'">'.$pid.'</option>';
	}
}

			$module .=	'
				</select>
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