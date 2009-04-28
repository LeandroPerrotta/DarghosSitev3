<?php
if($_SESSION['recovery'])
{
	$post = $core->extractPost();
	
	$character = $core->loadClass("Character");
	$character->loadByName($_SESSION['recovery'][0], "account_id"); 
	
	$chkEmail = $core->loadClass("Account");
	
	$account = $core->loadClass("Account");
	$account->load($character->get("account_id"), "password, email");
	$secretkey = $account->getSecretKey();
	
	if($secretkey)
	{
		if($post)
		{	
			$postSecretKey = $_POST['recovery_secretkey'];
			$postEmail = $_POST['recovery_email'];
		
			if($core->getIpTries() >= 3)
			{
				$error = "Est� opera��o est� bloqueada, por favor aguarde 24 horas ap�s a ultima tentativa.";
			}
			elseif($postSecretKey != $secretkey['key'])
			{
				$core->increaseIpTries();
				
				if($core->getIpTries() < 3)
					$error = "A chave secreta informada para mudan�a de e-mail de sua conta est� incorreta. Por movitos de seguran�a voc� s� poder� efetuar 3 tentativas desta opera��o, ap�s as 3 tentativas este recurso estar� bloqueado por 24 horas.";
				else
					$error = "Voc� efetuou tr�s tentativas erradas desta opera��o, por motivos de seguran�a este recurso estar� bloqueado pelas proximas 24 horas.";
			}
			elseif($chkEmail->loadByEmail($postEmail))
			{
				$error = "Este e-mail j� esta em uso por outra conta em nosso banco de dados.";
			}
			elseif(!$strings->validEmail($postEmail))
			{
				$error = "Este n�o � um e-mail valido.";
			}
			else
			{
				$account->setEmail($postEmail);
				$account->save();
				
				$success = "
				<p>Caro jogador,</p>
				<p>O e-mail registrado em sua conta foi modificado para {$postEmail} ultilizando sua chave secreta com sucesso!</p>
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
			
		$module .= '	
			<form action="" method="post">
				<fieldset>			
			
					<p>
						<label for="recovery_name">Nome do Personagem</label><br />
						<input readonly="readonly" name="recovery_name" size="40" type="text" value="'.$_SESSION['recovery'][0].'" />
					</p>		
					
					';
			
					if($secretkey["lembrete"] != "default")
					{
						$module .= '
						<p>
							<label for="recovery_character">Lembrete</label><br />
							<input readonly="readonly" name="recovery_lembrete" size="40" type="text" value="'.$secretkey["lembrete"].'" /> <br><em>(lembrete de sua chave secreta configurado por voc�)</em>
						</p>';			
					}			
					
					$module .= '
					<p>
						<label for="recovery_secretkey">Chave Secreta</label><br />
						<input name="recovery_secretkey" size="40" type="password" value="" /> <br><em>(informe sua chave secreta corretamente)</em>
					</p>
					
					<p>
						<label for="recovery_email">Novo e-mail</label><br />
						<input name="recovery_email" size="40" type="text" value="" /> <br><em>(informe o novo endere�o de e-mail que voc� deseja configurar em sua conta)</em>
					</p>			
					
					<div id="line1"></div>				
					
					<p>
						<input class="button" type="submit" value="Proximo" />					
					</p>
			</fieldset>
		</form>';
		}
	}
	else
	{
		$core->sendMessageBox("Erro!", "Esta conta n�o possui uma chave secreta configurada.");		
	}		
}
?>