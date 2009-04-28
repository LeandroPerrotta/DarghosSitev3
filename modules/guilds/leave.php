<?php
if($_GET['name'])
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList();	
	
	$guild = $core->loadClass("guilds");
	
	if(!$guild->loadByName($_GET['name']))
	{	
		$core->sendMessageBox("Erro!", "Esta guilda não existe em nosso banco de dados.");	
	}
	elseif(!$account->getGuildLevel($guild->get("name")))
	{
		$core->sendMessageBox("Erro!", "Você não tem permissão para acessar está pagina.");		
	}	
	else
	{		
		$guild->loadRanks();
		$guild->loadMembersList();
		
		$members = $guild->getMembersList();		
		
		$post = $core->extractPost();
		if($post)
		{									
			foreach($members as $member_name => $member_values)
			{
				$members_list[] = $member_name;
			}
			
			if($account->get("password") != $strings->encrypt($post[1]))
			{
				$error = "Confirmação da senha falhou.";
			}		
			elseif(!in_array($post[0], $members_list) or $members[$post[0]]['level'] == 1)
			{
				$error = "Falha fatal.";				
			}
			else
			{						
				$character = $core->loadClass("Character");
				$character->loadByName($post[0], "name, rank_id, guildnick, guild_join_date");
				$character->set("rank_id", 0);
				$character->set("guildnick", "");
				$character->set("guild_join_date", 0);
				$character->save();
				
				$success = "
				<p>Caro jogador,</p>
				<p>O personagem {$post[0]} não mais pertence a guilda {$_GET['name']}.</p>
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
			
			foreach($members as $member_name => $member_values)
			{
				if(in_array($member_name, $character_list) and $member_values['level'] > 1)
				{
					$options .= "<option value='{$member_name}'>{$member_name}</option>";
				}
			}
			
			
			$module .=	'
			<form action="" method="post">
				<fieldset>

					<p>
						<label for="member_candidate">Selecione o Personagem</label><br />		
						<select name="member_candidate">'.$options.'</select>
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