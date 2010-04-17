<?php
if($_GET['name'])
{
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$character_list = $account->getCharacterList(true);	
	
	$guild = new Guilds();
	
	if(!$guild->loadByName($_GET['name']))
	{	
		Core::sendMessageBox("Erro!", "Esta guilda n�o existe em nosso banco de dados.");		
	}
	elseif($account->getGuildLevel($guild->get("name")) > 1)
	{
		Core::sendMessageBox("Erro!", "Voc� n�o tem permiss�o para acessar est� pagina.");	
	}	
	else
	{		
		$post = Core::extractPost();
		if($post)
		{			
			$guild->loadRanks();
			$guild->loadMembersList();
			
			$members = $guild->getMembersList();
			
			if($account->get("password") != Strings::encrypt($post[0]))
			{
				$error = "Confirma��o da senha falhou.";
			}
			elseif($guild->isOnWar())
			{
				$error = "Sua guild est� em modo de guerra, s� ser� possivel sair do mesmo, no dia <b>".Core::formatDate($guild->getWarEnd())."</b>.";
			}
			else
			{				
				$guild->leaveWar();
				
				$success = "
				<p>Caro jogador,</p>
				<p>A guilda {$_GET['name']} foi retirada do modo de guerra com sucesso. Agora sua guilda � pacifica, e ter� todos assassinatos injustificados.</p>
				<p>Tenha um bom jogo!</p>
				";
			}
		}
		
		if($success)	
		{
			Core::sendMessageBox("Sucesso!", $success);
		}
		else
		{
			if($error)	
			{
				Core::sendMessageBox("Erro!", $error);
			}
			
		$module .=	'
			<form action="" method="post">
				<fieldset>

					<p>
						Voc� est� optando por tirar sua guilda do modo de guerra. Se voc� tiver certeza dessa decis�o, confirme sua senha abaixo.
									
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