<?php
if($_GET['name'])
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList(true);	
	$character_listByName = $account->getCharacterList();	
	
	$guild = $core->loadClass("guilds");
		
	if(!$guild->loadByName($_GET['name']))
	{
		$guildLoad = false;
	}
	else 
	{
		$guildLoad = true;
		$guild->loadInvitesList();
		$invites = $guild->getInvites();	
		
		$validInvites = false;
		$guild_invites = array();
		
		foreach($invites as $player_name => $invite_date)
		{
			if(in_array($player_name, $character_listByName))
			{
				$options .= "<option value='{$player_name}'>{$player_name}</option>";
				$validInvites = true;
			}	
			
			$guild_invites[] = $player_name;
		}			
	}
	
	if(!$guildLoad)
	{
		$core->sendMessageBox("Erro!", "Esta guilda não existe em nosso banco de dados.");	
	}
	elseif(!$validInvites)
	{
		$core->sendMessageBox("Erro!", "Você não tem permissão para acessar está pagina.");		
	}	
	else
	{		
		$post = $core->extractPost();
		if($post)
		{			
			if($account->get("password") != $strings->encrypt($post[2]))
			{
				$error = "Confirmação da senha falhou.";
			}	
			elseif(!in_array($post[0], $character_listByName))
			{				
				$error = "Este personagem não percente a sua conta.";
			}	
			elseif(!in_array($post[0], $guild_invites))
			{				
				$error = "Este personagem não está convidado para esta guilda.";
			}											
			else
			{		
				$character = $core->loadClass("Character");
				$character->loadByName($post[0], "name, rank_id, guild_join_date");
				
				if($post[1] == "accept")		
				{			
					$character->acceptInvite();
					$character->save();
					
					$success = "
					<p>Caro jogador,</p>
					<p>O personagem {$post[0]} bandeou-se para a guild {$guild->get("name")}!</p>
					<p>Tenha um bom jogo!</p>
					";
				}
				elseif($post[1] == "reject")
				{
					$character->removeInvite();
					
					$success = "
					<p>Caro jogador,</p>
					<p>O personagem {$post[0]} recusou bandear-se para a guild {$guild->get("name")}!</p>
					<p>Tenha um bom jogo!</p>
					";					
				}
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
			
			$module .=	'
			<form action="" method="post">
				<fieldset>
					
					<p>
						<label for="character_name">Personagem Convidado</label><br />
						<select name="character_name">'.$options.'</select>
					</p>	

					<p>
						<label for="invite_action">Ação</label><br />
						<ul id="pagelist">
							<li><input checked="checked" name="invite_action" type="radio" value="accept"> Aceitar Convite </li>
							<li><input name="invite_action" type="radio" value="reject"> Rejeitar Convite </li>
						</ul>
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