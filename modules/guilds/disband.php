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
			$guild->loadRanks();
			$guild->loadMembersList();
			
			$members = $guild->getMembersList();
			
			if($account->get("password") != $strings->encrypt($post[0]))
			{
				$error = "Confirmação da senha falhou.";
			}		
			elseif(count($members) > 1)
			{
				$error = "A sua guild ainda possui membros ativos, por favor expulse todos membros primeiramente antes de ultilizar esta função.";				
			}
			else
			{				
				$guild->disband();
				
				$success = "
				<p>Caro jogador,</p>
				<p>A guilda {$_GET['name']} foi desmanchada com sucesso e não existe mais em ".CONFIG_SITENAME.".</p>
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
						Para desmanchar sua guild é necessario não possuir nenhum membro em atividade, expulsando todos membros da guild, exepto o proprio dono.			
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