<?php
if($_SESSION['recovery'])
{	
	$player = new \Framework\Player();
	$player->loadByName($_SESSION['recovery']); 
	
	$chkEmail = new \Framework\Account();
	
	$account = new \Framework\Account();
	$account->load($player->get("account_id"));
	$secretkey = $account->getSecretKey();
	
	if($secretkey)
	{
		if($_POST)
		{	
			$postSecretKey = $_POST['recovery_secretkey'];
			$postEmail = $_POST['recovery_email'];
		
			if(\Core\Main::getIpTries() >= 3)
			{
				$error = tr("Esta operação está bloqueada, por favor aguarde 24 horas após a ultima tentativa.");
			}
			elseif($postSecretKey != $secretkey['key'])
			{
				\Core\Main::increaseIpTries();
				
				if(\Core\Main::getIpTries() < 3)
					$error = tr("A chave secreta informada para mudança de e-mail de sua conta está incorreta. Por movitos de segurançaa você só poder efetuar 3 tentativas desta operação, após as 3 tentativas este recurso estará bloqueado por 24 horas.");
				else
					$error = tr("Você efetuou três tentativas erradas desta operação, por motivos de segurança este recurso estará bloqueado pelas proximas 24 horas.");
			}
			elseif($chkEmail->loadByEmail($postEmail))
			{
				$error = tr("O e-mail informado já está em uso por outra conta no ".getConf(confEnum()->WEBSITE_NAME).".");
			}
			elseif(!\Core\Strings::validEmail($postEmail))
			{
				$error = tr("Este não parece ser um formato de e-mail valido. Tente novamente.");
			}
			else
			{
				$account->setEmail($postEmail);
				$account->save();
				
		        $success = "<p>".tr("Caro jogador,")."</p>"; 
		        $success .= "<p>".tr("O e-mail registrado em sua conta foi modificado ultilizando sua chave secreta com sucesso!")."</p>"; 
		        $success .= "<p>".tr("Tenha um bom jogo!")."</p>"; 
			}
		}
	
		if($success)	
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $success);
		}
		else
		{
			if($error)	
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
			}
			
		global $pages, $buttons;	
			
		$module .= '	
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset>			
			
					<p>
						<label for="recovery_name">'.tr("Nome do personagem").'</label><br />
						<input readonly="readonly" name="recovery_name" size="40" type="text" value="'.$_SESSION['recovery'].'" />
					</p>		
					
					';
			
					if($secretkey["lembrete"] != "default")
					{
						$module .= '
						<p>
							<label for="recovery_character">'.tr("Pergunta secreta").'</label><br />
							<input readonly="readonly" name="recovery_lembrete" size="40" type="text" value="'.$secretkey["lembrete"].'" /> <br><em>('.tr("dica de resposta deixada por você").')</em>
						</p>';		

						$module .= '
						<p>
    						<label for="recovery_secretkey">'.tr("Resposta").'</label><br />
    						<input name="recovery_secretkey" size="40" type="password" value="" /> <br><em>('.tr("informe sua resposta secreta corretamente").')</em>
						</p>		
						';				
					}
					else{
					    $module .= '
					    <p>
					        <label for="recovery_secretkey">'.tr("Chave Secreta").'</label><br />
					        <input name="recovery_secretkey" size="40" type="password" value="" /> <br><em>('.tr("informe sua chave secreta corretamente").')</em>
					    </p>
					    ';					    
					}	
					
					$module .= '
					<p>
						<label for="recovery_email">'.tr("Novo e-mail").'</label><br />
						<input name="recovery_email" size="40" type="text" value="" /> <br><em>('.tr("informe o novo e-mail que deverá ser registrado em sua conta").')</em>
					</p>			
					
					<div id="line1"></div>				
					
					<p>
						<input class="button" type="submit" value="'.tr("Enviar").'" />					
					</p>
			</fieldset>
		</form>';
		}
	}
	else
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), \Core\Lang::Message(\Core\Lang::$e_Msgs->RECOVERY_DISABLED));		
	}		
}
?>