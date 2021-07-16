<?php
if($_POST)
{	
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
	}	
	elseif(!$account->getEmail())
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->OPERATION_REQUIRE_VALIDATED_EMAIL);
	}	
	elseif($account->getSecretKey())
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->SECRETKEY_ALREADY_EXISTS);	
	}
	elseif($_POST["recovery_usekey"] == "system")
	{
		$account->setSecretKey($_POST["recovery_keysystem"], "default");
		
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->SECRETKEY_SUCCESS, $_POST["recovery_keysystem"]);
	}	
	elseif($_POST["recovery_usekey"] == "custom")
	{
		if(!$_POST["recovery_key"] or !$_POST["recovery_lembrete"])
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
		}	
		elseif(strlen($_POST["recovery_key"]) < 6 or strlen($_POST["recovery_key"]) > 15 or strlen($_POST["recovery_lembrete"]) < 5 or strlen($_POST["recovery_lembrete"]) > 25)		
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->SECRETKEY_WRONG_SIZE);
		}
		elseif($_POST["recovery_lembrete"] == $_POST["recovery_key"])
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->SECRETKEY_MUST_BY_UNLIKE_REMINDER);
		}
		elseif(!is_numeric($_POST["recovery_key"]))
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_NUMERIC_FIELDS);
		}
		else
		{
			$account->setSecretKey($_POST["recovery_key"], $_POST["recovery_lembrete"]);			
			$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->SECRETKEY_CUSTOM_SUCCESS, $_POST["recovery_key"], $_POST["recovery_lembrete"]);			
		}
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
	
$secretkey = \Core\Strings::randKey(5, 4, "number+upper");	
	
$module .= '	
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>			
		<p>Atravez deste painel você pode configurar a chave secreta de sua conta, ultilizada em situações criticas para recuperar sua conta. Ela pode ser uma chave de alta segurança definida pelo sistema (memorize-a), ou então uma chave definida por você mesmo, com direito a um lembrete para uso posterior.</p>
		
		<p>
			<label for="account_password">Senha da Conta</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>		
		
		<div id="line1"></div>		
		
		<div class="autoaction" style="margin: 0px; margin-top: 20px; padding: 0px;">	
			<p>
				<input name="recovery_usekey" type="radio" value="system" checked="checked"> Eu desejo usar a chave secreta gerada pelo sistema. 
			</p>		
	
			<p>
				<input name="recovery_usekey" type="radio" value="custom"> Eu desejo configurar uma chave secreta de minha preferência. 
			</p>					
		</div>

		<div title="system" class="viewable" style="margin: 0px; padding: 0px;">
			<p>
				<label for="recovery_character">Chave de Recuperação</label><br />
				<input readonly="readonly" name="recovery_keysystem" size="40" type="text" value="'.$secretkey.'" />
			</p>	
		</div>		
		
		<div title="custom" style="margin: 0px; padding: 0px;">
			<p>Observações para chaves definidas pelo usuario:</p>
			<ul>
				<li>Somente é permitido caraters numericos para a chave (0 a 9).</li>
				<li>É obrigatorio o uso de ao menos 6 caraters para a chave, e no maximo 15 caraters são permitidos.</li>
			</ul>		

			<p>
				<label for="recovery_lembrete">Digite o lembrete</label><br />
				<input name="recovery_lembrete" size="40" type="text" value="" />
			</p>			
			
			<p>
				<label for="recovery_key">Chave de Recuperação</label><br />
				<input name="recovery_key" size="40" type="text" value="" />
			</p>
		</div>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Proximo" />					
		</p>
	</fieldset>
</form>';
}
?>