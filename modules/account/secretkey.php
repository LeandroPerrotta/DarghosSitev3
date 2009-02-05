<?php
$post = $core->extractPost();

if($post)
{	
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	if($account->get("password") != $strings->encrypt($post[0]))
	{
		$error = "Confirmação da senha falhou.";
	}	
	elseif($account->getSecretKey())
	{
		$error = "Esta conta já possui uma chave secreta configurada.";	
	}
	elseif($post[2] == 1)
	{
		$account->setSecretKey($post[1], "default");
		
		$success = "
		<p>Caro jogador,</p>
		<p>A chave secreta {$post[1]} foi configurada com sucesso em sua conta!</p>
		<p>Tenha um bom jogo!</p>
		";
	}	
	elseif($post[2] == 2)
	{
		if(!$post[3] or !$post[4])
		{
			$error = "Preencha todos campos do formulario corretamente.";
		}	
		elseif(strlen($post[3]) < 10 or strlen($post[3]) > 50 or strlen($post[4]) < 5 or strlen($post[4]) > 25)		
		{
			$error = "A sua chave secreta deve possuir entre 10 e 50 caracteres e seu lembrete entre 5 e 25 caracteres.";
		}
		else
		{
			$account->setSecretKey($post[3], $post[4]);
			
			$success = "
			<p>Caro jogador,</p>
			<p>A sua chave secreta {$post[3]} com o lembrete {$post[4]} foi configurada com sucesso em sua conta!</p>
			<p>Tenha um bom jogo!</p>
			";			
		}
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
	
$secretkey = $strings->randKey(5, 4, "number+upper");	
	
$module .= '	
<form action="" method="post">
	<fieldset>			
		<p>Atravez deste painel você pode configurar a chave secreta de sua conta, ultilizada em situações criticas para recuperar sua conta. Ela pode ser uma chave de alta segurança definida pelo sistema (memorize-a), ou então uma chave definida por você mesmo, com direito a um lembrete para uso posterior.</p>
		
		<p>
			<label for="recovery_character">Senha da Conta</label><br />
			<input name="recovery_password" size="40" type="password" value="" />
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<label for="recovery_character">Chave de Recuperação</label><br />
			<input readonly="readonly" name="recovery_keysystem" size="40" type="text" value="'.$secretkey.'" />
		</p>		
		
		<p>
			<input checked="checked" name="recovery_usekey" type="radio" value="1"> Eu desejo usar a chave secreta gerada pelo sistema. 
		</p>		
		
		<div id="line1"></div>

		<p>
			<input name="recovery_usekey" type="radio" value="2"> Eu desejo configurar uma chave secreta de minha prefêrencia. 
		</p>			
		
		<p>
			<label for="recovery_character">Chave de Recuperação</label><br />
			<input name="recovery_key" size="40" type="text" value="" />
		</p>			

		<p>
			<label for="recovery_character">Lembrete</label><br />
			<input name="recovery_lembrete" size="40" type="text" value="" />
		</p>					
		
		<div id="line1"></div>
		
		<p>
			<input type="submit" value="Proximo" />					
		</p>
	</fieldset>
</form>';
}
?>