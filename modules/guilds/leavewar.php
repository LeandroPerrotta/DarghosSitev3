<?php
if($_GET['name'])
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList(true);	
	
	$guild = $core->loadClass("guilds");
	
	if(!$guild->loadByName($_GET['name']))
	{	
		$core->sendMessageBox("Erro!", "Esta guilda não existe em nosso banco de dados.");		
	}
	elseif($account->getGuildLevel($guild->get("name")) > 1)
	{
		$core->sendMessageBox("Erro!", "Você não tem permissão para acessar está pagina.");	
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
			else
			{				
				$guild->leaveWar();
				
				$success = "
				<p>Caro jogador,</p>
				<p>A guilda {$_GET['name']} foi retirada do modo de guerra com sucesso. Agora sua guilda é pacifica, e terá todos assassinatos injustificados.</p>
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
			
		$module .=	'
			<form action="" method="post">
				<fieldset>

					<p>
						Você está optando por tirar sua guilda do modo de guerra. Se você tiver certeza dessa decisão, confirme sua senha abaixo.
									
					</p>	
			
					<p>
						<label for="account_password">Confirme sua senha</label><br />
						<input name="account_password" size="40" type="password" value="" />
					</p>						
					
					<div id="line1"></div>
					
					<p>
						<input class="button" type="submit" value="Confirmar saida do estado de Guerra" />
					</p>
				</fieldset>
			</form>';	
		}	
	}

}		
?>