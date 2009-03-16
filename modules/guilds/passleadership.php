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
		$guild->loadRanks();
		$guild->loadMembersList();
		
		$members = $guild->getMembersList();		
		
		$post = $core->extractPost();
		if($post)
		{						
			$ranks = $guild->getRanks();
			$members = $guild->getMembersList();
			
			foreach($members as $member_name => $member_values)
			{
				$members_list[] = $member_name;
			}
			
			if($account->get("password") != $strings->encrypt($post[1]))
			{
				$error = "Confirmação da senha falhou.";
			}		
			elseif(!in_array($post[0], $members_list))
			{
				$error = "Falha fatal.";				
			}
			else
			{			
				$leader_id = 0;
				$vice_id = 0;
				
				foreach($ranks as $rank_id => $rank_values)
				{
					if($rank_values['level'] == 1)
						$leader_id = $rank_id;
						
					if($rank_values['level'] == 2)
						$vice_id = $rank_id;
				}
				
				$newLeader_char = $core->loadClass("Character");
				$newLeader_char->loadByName($post[0], "name, rank_id");
				$newLeader_char->set("rank_id", $leader_id);
				$newLeader_id = $newLeader_char->get("id");
				$newLeader_char->save();
				
				$oldLeader_char = $core->loadClass("Character");
				$oldLeader_char->load($guild->get("ownerid"), "name, rank_id");
				$oldLeader_char->set("rank_id", $vice_id);
				$oldLeader_name = $oldLeader_char->get("name");
				$oldLeader_char->save();
				
				$guild->set("ownerid", $newLeader_id);
				$guild->save();
				
				$success = "
				<p>Caro jogador,</p>
				<p>A guilda {$_GET['name']} teve a liderança transferida de {$oldLeader_name} para {$post[0]} com sucesso!</p>
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
			
			$vices = 0;
			
			foreach($members as $member_name => $member_values)
			{
				if($member_values['level'] == 2)
				{
					$options .= "<option value='{$member_name}'>{$member_name}</option>";
					$vices++;
				}
			}
			
			
			$module .=	'
			<form action="" method="post">
				<fieldset>

			';

				if($vices > 0)
				{
					$module .=	'
					<p>
						<label for="member_candidate">Membros Cadidatos</label><br />		
						<select name="member_candidate">'.$options.'</select>
					</p>	

					<p>
						<label for="account_password">Senha</label><br />
						<input name="account_password" size="40" type="password" value="" />
					</p>						
					
					<div id="line1"></div>
					
					<p>
						<input type="submit" value="Enviar" />
					</p>					
					';
				}
				else
				{
					$module .=	'
					<p>
						É necessario possuir ao menos 1 vice lider disponivel para que seja possivel a  transferencia a liderança de uma guilda.
					</p>	
					';			
				}
				
				$module .=	'
				</fieldset>
			</form>';	
		}	
	}

}		
?>