<?php
if($_GET['name'])
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList(true);	
	
	$guild = $core->loadClass("guilds");
	
	if(!$guild->loadByName($_GET['name']))
	{
		$module .=	'
		
		<div id="error">
			<h2>Esta guilda não existe em nosso banco de dados.</h2>
		</div>
		
		';			
	}
	elseif($account->getGuildLevel($guild->get("name")) > 1)
	{
		$module .=	'
		
		<div id="error">
			<h2>Você não tem permissão para acessar está pagina.</h2>
		</div>
		
		';		
	}	
	else
	{		
		$post = $core->extractPost();
		if($post)
		{
			$guild_image = isset($_FILES['guild_image']) ? $_FILES['guild_image'] : false;
			
			if($guild_image["name"])
			{
				$image_infos = getimagesize($guild_image["tmp_name"]);
			}
			
			if($account->get("password") != $strings->encrypt($post[1]))
			{
				$error = "Confirmação da senha falhou.";
			}			
			elseif(strlen($post[0]) > 500)
			{
				$error = "O campo comentario deve possuir no maximo 500 caracteres.";
			}
			elseif($guild_image["name"] and $guild_image["size"] > 100000)
			{
				$error = "O tamanho do logotipo para sua guilda deve possuir no maximo 100 kb.";
			}
			elseif($guild_image["name"] and !$image_infos)
			{
				$error = "Este não é um arquivo valido.";
			}
			elseif($guild_image["name"] and ($image_infos[0] != 100 or $image_infos[1] != 100))
			{
				$error = "As dimensões do logotipo de sua guilda deve ser exatamente de 100x100 pixels.";
			}	
			elseif($guild_image["name"] and $image_infos[2] > 3)
			{
				$error = "O logotipo de sua guilda deve ser no formato GIF, JPG ou PNG.";
			}						
			else
			{		
				$guild->set("comment", $post[0]);
				
				if($guild_image)
				{
					$extenção = null;
					preg_match("/\.(gif|jpg|jpeg|png){1}$/i", $guild_image["name"], $extenção);
					
					$name = $strings->randKey(10, 1, "lower+number").$extenção[0];
					$file = GUILD_IMAGE_DIR.$name;
					
					if(move_uploaded_file($guild_image["tmp_name"], $file))
					{
						$guild->set("image", $name);
					}		
				}
				
				$guild->save();
				
				$success = "
				<p>Caro jogador,</p>
				<p>As mudanças nas descrições de sua guilda foram efetuadas com sucesso!</p>
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
			<form action="" method="post" ENCTYPE="multipart/form-data">
				<fieldset>

					<p><h3>Logotipo da Guild</h3></p>
				
					<p>
					    <label for="guild_image">Selecione a Imagem</label>
						<input type="file" name="guild_image" size="35">
						<em><br>A imagem não deve possuir tamanho superior a 100 kb.</em>
						<em><br>A imagem deve ser em formato jpg, gif ou png.</em>
						<em><br>A imagem deve possuir resolução exata de 100x100 pixels.</em>
				    </p>			
				    
					<p>
						<label for="guild_comment">Comentario</label><br />
						<textarea name="character_comment" rows="10" wrap="physical" cols="55">'.$guild->get("comment").'</textarea>
						<em><br>Limpe para deletar.</em>
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

}		
?>