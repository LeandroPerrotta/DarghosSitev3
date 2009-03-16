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
	elseif($account->getGuildLevel($guild->get("name")) > 2)
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
			$invites_list = explode(";", $post[0]);
			$invites_limit = true;
			
			$dontExists = array();
			$wasGuild = array();
			$wasInvited = array();
			
			if(count($invites_list) < 20)
			{
				$invites_limit = false;
				
				foreach($invites_list as $player_name)
				{
					$character = $core->loadClass("Character");
					
					if(!$character->loadByName($player_name, "name, rank_id"))
					{
						$dontExists[] = $player_name;	
					}	
					else
					{	
						if($character->loadGuild())
							$wasGuild[] = $player_name;
							
						if($character->wasInvitedToGuild())
							$wasInvited[] = $player_name;	
					}	
				}
			}
			
			if($account->get("password") != $strings->encrypt($post[1]))
			{
				$error = "Confirmação da senha falhou.";
			}	
			elseif($invites_limit)
			{				
				$error = "Somente é permitido enviar 20 convites por vez.";
			}					
			elseif(count($wasGuild) != 0)
			{
				foreach($wasGuild as $name)
				{
					$wasGuild_list .= $name."<br>";
				}
				
				$error = "Os seguintes personagens já são membros de uma guild e não podem ser convidados a sua guild:<br> {$wasGuild_list}";
			}
			elseif(count($wasInvited) != 0)
			{
				foreach($wasInvited as $name)
				{
					$wasInvited_list .= $name."<br>";
				}
				
				$error = "Os seguintes personagens já estão convidados para outra guild e não podem ser convidados a sua guild:<br> {$wasInvited_list}";
			}		
			elseif(count($dontExists) != 0)
			{
				foreach($dontExists as $name)
				{
					$dontExists_list .= $name."<br>";
				}
				
				$error = "Os seguintes personagens não existem em nosso banco de dados:<br> {$dontExists_list}";
			}					
			else
			{		
				foreach($invites_list as $player_name)
				{
					$character = $core->loadClass("Character");
					
					$character->loadByName($player_name, "name, rank_id");
					$character->inviteToGuild($guild->get("id"));
				}				
				
				$success = "
				<p>Caro jogador,</p>
				<p>Todos jogadores da lista foram convidados para sua guilda com sucesso!</p>
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
						<label for="guild_invites">Personagem(s)</label><br />
						<textarea name="guild_invites" rows="10" wrap="physical" cols="55"></textarea>
						<em><br><b>Instruções:</b> Lista de personagens a serem convidados a sua guild, ultilize um ; (ponto e virgula) para separar cada personagem. (ex: Slash;Fawkes;Baracs)</em>
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