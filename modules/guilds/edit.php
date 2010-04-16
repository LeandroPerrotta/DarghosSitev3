<?php
if($_GET['name'])
{
	$account = new Account();
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList(true);	
	
	$guild = new Guilds();
	
	if(!$guild->loadByName($_GET['name']))
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']));
	}
	elseif($account->getGuildLevel($guild->get("name")) > 1)
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_REPORT));	
	}	
	else
	{		
		$post = Core::extractPost();
		if($post)
		{
			$guild_image = isset($_FILES['guild_image']) ? $_FILES['guild_image'] : false;
			
			if($guild_image["name"])
			{
				$image_infos = getimagesize($guild_image["tmp_name"]);
			}
			
			if($account->get("password") != Strings::encrypt($post[1]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}			
			elseif(strlen($post[0]) > 500)
			{
				$error = Lang::Message(LMSG_GUILD_COMMENT_SIZE);
			}
			elseif($guild_image["name"] and $guild_image["size"] > 100000)
			{
				$error = Lang::Message(LMSG_GUILD_LOGO_SIZE);
			}
			elseif($guild_image["name"] and !$image_infos)
			{
				$error = Lang::Message(LMSG_GUILD_FILE_WRONG);
			}
			elseif($guild_image["name"] and ($image_infos[0] != 100 or $image_infos[1] != 100))
			{
				$error = Lang::Message(LMSG_GUILD_LOGO_DIMENSION_WRONG);
			}	
			elseif($guild_image["name"] and $image_infos[2] > 3)
			{
				$error = Lang::Message(LMSG_GUILD_LOGO_EXTENSION_WRONG);
			}						
			else
			{		
				$guild->set("motd", $post[0]);
				
				if($guild_image)
				{
					$extension = null;
					preg_match("/\.(gif|jpg|jpeg|png){1}$/i", $guild_image["name"], $extension);
					
					$name = Strings::randKey(10, 1, "lower+number").$extension[0];
					$file = GUILD_IMAGE_DIR.$name;
					
					if(move_uploaded_file($guild_image["tmp_name"], $file))
					{
						$guild->set("image", $name);
					}		
				}
				
				$guild->save();
				
				$success = Lang::Message(LMSG_GUILD_DESC_CHANGED);
			}
		}
		
		if($success)	
		{
			Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $success);
		}
		else
		{
			if($error)	
			{
				Core::sendMessageBox(Lang::Message(LMSG_ERROR), $error);
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
						<label for="guild_motd">Comentario</label><br />
						<textarea name="guild_motd" rows="10" wrap="physical" cols="55">'.$guild->get("motd").'</textarea>
						<em><br>Limpe para deletar.</em>
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
	}

}		
?>