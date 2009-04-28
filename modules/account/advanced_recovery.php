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
				$error = "Está operação está bloqueada, por favor aguarde 24 horas após a ultima tentativa.";
			}
			elseif($postSecretKey != $secretkey['key'])
			{
				$core->increaseIpTries();
				
				if($core->getIpTries() < 3)
					$error = "A chave secreta informada para mudança de e-mail de sua conta está incorreta. Por movitos de segurança você só poderá efetuar 3 tentativas desta operação, após as 3 tentativas este recurso estará bloqueado por 24 horas.";
				else
					$error = "Você efetuou três tentativas erradas desta operação, por motivos de segurança este recurso estará bloqueado pelas proximas 24 horas.";
			}
			elseif($chkEmail->loadByEmail($postEmail))
			{
				$error = "Este e-mail já esta em uso por outra conta em nosso banco de dados.";
			}
			elseif(!$strings->validEmail($postEmail))
			{
				$error = "Este não é um e-mail valido.";
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
							<input readonly="readonly" name="recovery_lembrete" size="40" type="text" value="'.$secretkey["lembrete"].'" /> <br><em>(lembrete de sua chave secreta configurado por você)</em>
						</p>';			
					}			
					
					$module .= '
					<p>
						<label for="recovery_secretkey">Chave Secreta</label><br />
						<input name="recovery_secretkey" size="40" type="password" value="" /> <br><em>(informe sua chave secreta corretamente)</em>
					</p>
					
					<p>
						<label for="recovery_email">Novo e-mail</label><br />
						<input name="recovery_email" size="40" type="text" value="" /> <br><em>(informe o novo endereço de e-mail que você deseja configurar em sua conta)</em>
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
		$core->sendMessageBox("Erro!", "Esta conta não possui uma chave secreta configurada.");		
	}		
}
?>