<?php
if(isset($_POST['character_name']))
{
	$core->redirect("?ref=character.edit&name={$_POST['character_name']}");
}

$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "password");

$list = $account->getCharacterList();

if($_GET['name'])
{
	if(in_array($_GET['name'], $list))
	{
		$character = $core->loadClass("character");
		$character->loadByName($_GET['name'], "name, comment, hide");
		
		$post = $core->extractPost();
		if($post)
		{
			if($account->get("password") != $strings->encrypt($post[2]))
			{
				$error = "Confirmação da senha falhou.";
			}			
			elseif(strlen($post[0]) > 500)
			{
				$error = "O campo comentario deve possuir no maximo 500 caracteres.";
			}
			else
			{		
				$hide = ($post[1] == 1) ? "1" : "0";
				$character->set("comment", $post[0]);
				$character->set("hide", $hide);
				
				$character->save();
				
				$success = "
				<p>Caro jogador,</p>
				<p>A mudança das informações de seu personagem foi efetuada com exito!</p>
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
						<label>Personagem:</label><br />
						'.$_GET['name'].'
					</p>
					
					<p>
						<label for="character_comment">Comentario</label><br />
						<textarea name="character_comment" rows="10" wrap="physical" cols="55">'.$character->get("comment").'</textarea>
						<em><br>Limpe para deletar.</em>
					</p>	

					<p>
						<input '.(($character->get("hide") == "1") ? "checked=\"checked\"" : null).' name="character_hide" type="checkbox" value="1" /> Marque está opção para esconder este personagem.
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
	}
	else
	{			
		$module .=	'
		
		<div id="error">
			<h2>Este personagem não existe ou não é de sua conta.</h2>
		</div>
		
		';		
	}
}
else
{

$module .=	'
	<form action="" method="post">
		<fieldset>
			
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
			
			<div id="line1"></div>
			
			<p>
				<input type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';
}			
?>