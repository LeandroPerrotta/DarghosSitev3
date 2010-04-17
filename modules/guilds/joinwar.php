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
			elseif(count($members) < 8)
			{
				$error = "A sua guild precisa ter no minimo 8 membros ativos, para entrar em modo de guerra.";				
			}
			else
			{				
				$guild->joinWar();
				
				$success = "
				<p>Caro jogador,</p>
				<p>A guilda {$_GET['name']} agora est� em modo de guerra. Tome cuidado, pois voc� e toda a guilda, est�o vulner�veis a ataques e assassinatos justificados por qualquer outra guilda em modo de guerra.</p>
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
						Voc� est� optando por colocar sua guilda inteira em modo de guerra.
									
					</p>	
					
					<p>
						<ul>
							<li>Durante o periodo em que a mesma estiver neste modo, qualquer assasinato cometido ou sofrido, por jogadores de guildas diferentes, mas que est�o em modo de guerra, ser� <b>justificado</b>.</li>					
							<li>O level minimo, para um membro de uma guilda participar do modo de guerra, � 130. Sendo assim, qualquer jogador com o level menor que este, mesmo estando na guilda, ir� ter seus assassinatos injustificados.</li>
							<li>O modo de guerra n�o possui limite de mortes e assassinatos.</li>
							<li>Ap�s aceitar e entrar no modo de guerra, durante 30 dias, nenhum membro poder� sair da guilda, e a guilda n�o poder� ser desmanchada.</li>
						</ul>
					</p>
			
					<p>
						<label for="account_password">Confirme sua senha</label><br />
						<input name="account_password" size="40" type="password" value="" />
					</p>						
					
					<div id="line1"></div>
					
					<p>
						<input class="button" type="submit" value="Confirmar estado de Guerra" />
					</p>
				</fieldset>
			</form>';	
		}	
	}

}		
?>