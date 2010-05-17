<?php
if($_GET['name'])
{
	$result = false;
	$message = "";	

	function proccessPost(&$message, Account $account, Guilds $guild)
	{
		$guild_image = isset($_FILES['guild_image']) ? $_FILES['guild_image'] : false;
		
		if($guild_image["name"])
		{
			$image_infos = getimagesize($guild_image["tmp_name"]);
		}
		
		if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
		{
			$message = Lang::Message(LMSG_WRONG_PASSWORD);
			return false;
		}			
		
		if(strlen($_POST["guild_motd"]) > 500)
		{
			$message = Lang::Message(LMSG_GUILD_COMMENT_SIZE);
			return false;
		}
		
		if($guild_image["name"] and $guild_image["size"] > 100000)
		{
			$message = Lang::Message(LMSG_GUILD_LOGO_SIZE);
			return false;
		}
		
		if($guild_image["name"] and !$image_infos)
		{
			$message = Lang::Message(LMSG_GUILD_FILE_WRONG);
			return false;
		}
		
		if($guild_image["name"] and ($image_infos[0] != 100 or $image_infos[1] != 100))
		{
			$message = Lang::Message(LMSG_GUILD_LOGO_DIMENSION_WRONG);
			return false;
		}
			
		if($guild_image["name"] and $image_infos[2] > 3)
		{
			$message = Lang::Message(LMSG_GUILD_LOGO_EXTENSION_WRONG);
			return false;
		}						

		
		$guild->SetMotd($_POST["guild_motd"]);
		
		if($guild_image)
		{
			$extension = null;
			preg_match("/\.(gif|jpg|jpeg|png){1}$/i", $guild_image["name"], $extension);
			
			$name = Strings::randKey(10, 1, "lower+number").$extension[0];
			$file = GUILD_IMAGE_DIR.$name;
			
			if(move_uploaded_file($guild_image["tmp_name"], $file))
			{
				$guild->SetImage($name);
			}		
		}
		
		$guild->Save();
		
		$message = Lang::Message(LMSG_GUILD_DESC_CHANGED);		
		return true;
	}
	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$guild = new Guilds();
	
	if(!$guild->LoadByName($_GET['name']))
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_GUILD_NOT_FOUND, $_GET['name']));
	}
	elseif(Guilds::GetAccountLevel($account, $guild->GetId()) != GUILD_RANK_LEADER)
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_REPORT));	
	}	
	else
	{		
		if($_POST)
		{
			$result = (proccessPost($message, $account, $guild)) ? true : false;		
		}
			
		if($result)	
		{
			Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $message);
		}
		else
		{
			if($_POST)	
			{
				Core::sendMessageBox(Lang::Message(LMSG_ERROR), $message);
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
						<textarea name="guild_motd" rows="10" wrap="physical" cols="55">'.$guild->GetMotd().'</textarea>
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