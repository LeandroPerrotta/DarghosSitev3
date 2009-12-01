<?
if($_SESSION['contribute'])
{
	$contribute = $core->loadClass("Contribute");
	$orderNumber = $contribute->getNewOrderNumber();
	
	$character = $core->loadClass("Character");
	$character->loadByName($_SESSION['contribute'][2], "account_id");
	$target_account = $character->get("account_id");
	
	if(!$orderNumber)
	{
		$error = "Ouve uma falha ao obter um numero para seu pedido. Por favor tente novamente, se o problema persistir aguarde algumas horas.";
	}
	
	$contribute->set("name", $_SESSION['contribute'][0]);
	$contribute->set("email", $_SESSION['contribute'][1]);
	$contribute->set("target", $_SESSION['contribute'][2]);
	$contribute->set("type", $_SESSION['contribute'][3]);
	$contribute->set("period", $_SESSION['contribute'][4]);
	$contribute->set("cost", $_contribution[$_SESSION['contribute'][3]][$_SESSION['contribute'][4]]);
	$contribute->set("server", SERVER_ID);
	$contribute->set("generated_by", $_SESSION['login'][0]);
	$contribute->set("generated_in", time());
	$contribute->set("target_account", $target_account);
	$contribute->set("email_vendor", $_contribution['emailadmin']);
	
	$contribute->save();

	$module .= '	
		<fieldset>			
			
			<p><h3>Pedido Gerado com sucesso!</h3></p>
			
			<p>Caro jogador, o seu pedido foi gerado com sucesso! Anote abaixo o numero de seu pedido para consulta ou qualquer eventual problema.</p>
			<p>Clicando no botão Finalizar abaixo você será direcionado ao site do '.$_SESSION['contribute'][3].' aonde você irá terminar o processo efetuando o pagamento de sua contribuição.</p>
			
			<p>Numero do Pedido de sua Contribuição: <h3>'.$orderNumber.'</h3></p>
			
			<p>
				'.$contribute->sendUrl().'				
			</p>
		</fieldset>';	
		
	unset($_SESSION['contribute']);
}
?>