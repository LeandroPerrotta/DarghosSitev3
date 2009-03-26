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
	elseif($account->getGuildLevel($guild->get("name")) > 2)
	{
		$core->sendMessageBox("Erro!", "Você não tem permissão para acessar está pagina.");		
	}	
	else
	{						
		$guild->loadRanks();
		$guild->loadMembersList();
		
		$members = $guild->getMembersList();
		$ranks = $guild->getRanks();		
		
		$post = $core->extractPost();
		if($post)
		{			
			$canEditMember = true;
			$character = $core->loadClass("Character");
			
			if($members[$_POST["guild_member"]]['level'] <= $account->getGuildLevel($guild->get("name")))
				$canEditMember = false;
			
			if(in_array($_POST["guild_member"], $character_list))
				$canEditMember = true;		

			if($members[$_POST["guild_member"]])
			{			
				if($canEditMember)
				{
					if($_POST["guild_action"] == "setRank")
					{		
						$character->loadByName($_POST["guild_member"], "account_id, name, rank_id");
						$accountMember = $core->loadClass("Account");
						$accountMember->load($character->get("account_id"), "premdays");
						
						if($account->getGuildLevel($guild->get("name")) == 2 and $account->getGuildLevel($guild->get("name")) <=  $ranks[$_POST["member_rank"]]["level"])
						{
							$canNotPromove = true;		
						}
						elseif($members[$_POST["guild_member"]]['level'] == 1)
						{
							$canNotDemote = true;						
						}
						elseif($ranks[$_POST["member_rank"]]["level"] == 2 and $accountMember->get("premdays") == 0)
						{
							$rankOnlyToPremium = true;	
						}
						elseif($ranks[$_POST["member_rank"]]["level"] == 2 and $accountMember->isGuildHighMember())
						{
							$alreadyIsHighMember = true;	
						}
						else
						{
							$character->set("rank_id", $_POST["member_rank"]);
							$character->save();										
						}
					}
					elseif($_POST["guild_action"] == "setTitle")
					{
						if(strlen($_POST["member_nick"]) < 3 or strlen($_POST["member_nick"]) > 15)
						{
							$titleIsLong = true;
						}
						else
						{
							$character->loadByName($_POST["guild_member"], "name, guildnick");
							$character->set("guildnick", $_POST["member_nick"]);
							$character->save();
						}
					}
					elseif($_POST["guild_action"] == "exclude")
					{						
						if($members[$_POST["guild_member"]]['level'] == 1)
						{
							$canNotDemote = true;						
						}			
						else
						{
							$character->loadByName($_POST["guild_member"], "name, rank_id, guildnick, guild_join_date");
							$character->set("guildnick", "");		
							$character->set("rank_id", 0);
							$character->set("guild_join_date", 0);
							$character->save();
						}
					}
				}
			}	
			else
				$memberInexistente = true;
		
			if($account->get("password") != $strings->encrypt($post[4]))
			{
				$error = "Confirmação da senha falhou.";
			}	
			elseif($memberInexistente)		
			{
				$error = "Este personagem não é membro desta guild.";						
			}			
			elseif($rankOnlyToPremium)		
			{
				$error = "Somente membros com uma Conta Premium podem ser promovidos a este Rank.";						
			}				
			elseif(!$canEditMember)
			{
				$error = "Apénas Lideres de Guildas podem modificar Vice-Lideres.";
			}			
			elseif($titleIsLong)
			{
				$error = "O titulos do membro deve possuir entre 3 e 15 caracteres.";
			}		
			elseif($canNotPromove)		
			{
				$error = "Você não pode promover membros para este rank.";						
			}
			elseif($canNotDemote)		
			{
				$error = "Esta ação não pode ser efetuada para este membro.";						
			}	
			elseif($rankOnlyToPremium)		
			{
				$error = "Somente membros com uma Conta Premium podem ser promovidos a este Rank.";						
			}		
			elseif($alreadyIsHighMember)
			{
				$error = "O membro em questão já é Lider ou Vice-Lider em outra guild.";					
			}
			else
			{						
				$success = "
				<p>Caro jogador,</p>
				<p>O membro {$post[0]} foi modificado na guilda com sucesso!</p>
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
		
		$lowerRank = $guild->getLowerRank();
		
		foreach($members as $player_name => $member_values)
		{			
			$options .= "<option value='{$player_name}'>{$player_name} ({$member_values['rank']})</option>";
		}
		
		foreach($ranks as $rank_id => $rank_values)
		{
			if($rank_values['level'] >= 2)
			{
				$option_ranks .= "<option ".(($lowerRank == $rank_id) ? "selected='selected'" : "")." value='{$rank_id}'>{$rank_values['level']}: {$rank_values['name']}</option>";
			}	
		}
			
		$module .=	'
			<form action="" method="post">
				<fieldset>
					
					<p>
						<label for="guild_member">Lista de Membros</label><br />
						<select name="guild_member">'.$options.'</select>
					</p>					
					
					<p>
						<label for="guild_action">Ações</label><br />
						
						<ul id="pagelist">
							<li><input name="guild_action" type="radio" value="setRank"> Configurar rank para <select name="member_rank">'.$option_ranks.'</select></li>
							<li><input name="guild_action" type="radio" value="setTitle"> Configurar titulo para <input name="member_nick" size="25" type="text" value="" /></li>
							<li><input name="guild_action" type="radio" value="exclude"> Dispençar da Guilda </li>
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