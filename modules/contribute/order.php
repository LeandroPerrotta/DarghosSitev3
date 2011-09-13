<?
if($_POST)
{	
	$character = new Character();
	
	$_paymentMethods = array("PagSeguro");
	$_paymentPeriods = array(30, 60, 90);
	
	$form_OrderName = $_POST["order_name"];
	$form_OrderMail = $_POST["order_email"];
	$form_OrderTarget = $_POST["order_target"];
	$form_OrderType = $_POST["order_type"];
	
	$form_OrderDays = $_POST["order_days"];
	
	$form_OrderDaysStr = $_POST["order_days"];

	
	$account = Account::loadLogged();
	if(!$form_OrderName or !$form_OrderMail or !$form_OrderTarget or !$form_OrderType or !$form_OrderDays)
	{
		$error = Lang::Message(LMSG_FILL_FORM);
	}
	elseif(!$account->getEmail())
	{
		$error = Lang::Message(LMSG_OPERATION_REQUIRE_VALIDATED_EMAIL);
	}
	elseif(!$character->loadByName($form_OrderTarget))
	{
		$error = Lang::Message(LMSG_CHARACTER_WRONG);
	}
	elseif(!in_array($form_OrderType, $_paymentMethods) or !in_array($form_OrderDays, $_paymentPeriods))
	{
		$error = Lang::Message(LMSG_REPORT);
	}
	else
	{
		$success = true;
		
		foreach($_POST as $value)
		{
			$_SESSION['contribute'][] = $value;
		}
	
		$module .= '	
			<form action="?ref=contribute.confirm" method="post">
				<fieldset>			
					
					<p><h3>Confirmação do Pedido</h3></p>
					
					<p>Analize abaixo se todos dados foram preenchidos corretamente, e se assim for clique no botão Confirmar e você será criado o seu pedido e você será direcionado a uma pagina para anotar o numero de seu pedido e ir ao site do '.$form_OrderType.' para concluir a sua contribuição.</p>
				
					<p><h3>Informações Pessoais</h3></p>
					<div id="line1"></div>
			
					<p>
						<label for="order_name">Nome</label><br />
						'.$form_OrderName.'.
					</p>	

					<p>
						<label for="order_email">E-mail para Contato</label><br />
						'.$form_OrderMail.'.
					</p>				
			
					<p><h3>Informações da Contribuição</h3></p>
					<div id="line1"></div>
			
					<p>
						<label for="order_target">Personagem Destino desta Contribuição</label><br />
						'.$form_OrderTarget.'
					</p>	
				
					<p>
						<label for="order_target">Forma de Pagamento desta Contribuição</label><br />
						'.$form_OrderType.'.
					</p>
					
					<p>
						<label for="order_days">Periodo e valor desta Contribuição</label><br />
						
						'.$form_OrderDaysStr.' dias por '.$_contribution[$form_OrderType][$form_OrderDays].'.
					</p>
					
					<div id="line1"></div>
					
					<p>
						<input class="button" type="submit" value="Confirmar" />					
					</p>
			</fieldset>
		</form>';		
	}
}

if(!$success)	
{
	if($error)	
	{
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), $error);
	}
	
$module .= '	
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>			
			
			<p><h3>Formulário de Pedido de Contribuição</h3></p>
			
			<p>Preencha os campos abaixo atentamente para efetuar um pedido de contribuição, todos os campos são obrigatórios. Ao final do processo você será direcionado a pagina de nosso parceiro para concluir o pagamento em uma pagina segura (HTTPS) e será confirmado o seu pedido.</p>
		
			<p><h3>Informações Pessoais</h3></p>
			<div id="line1"></div>
	
			<p>
				<label for="order_name">Nome</label><br />
				<input name="order_name" size="40" type="text" value="" /> <br><em>(preencha com seu nome real e completo)</em>
			</p>	

			<p>
				<label for="order_email">E-mail para Contato</label><br />
				<input name="order_email" size="40" type="text" value="" /> <br><em>(será ultilizado para o envio da confirmação de sua contribuição)</em>
			</p>				
	
			<p><h3>Informações da Contribuição</h3></p>
			<div id="line1"></div>
	
			<p>
				<label for="order_target">Personagem destino desta Contribuição</label><br />
				<input name="order_target" size="40" type="text" value="" /> <br><em>(Preencha com o nome de um personagem de sua conta ou o nome do personagem um amigo caso seja um presente por exemplo.)</em>
			</p>	
		
			<p>
				<label for="order_type">Forma de Pagamento desta Contribuição</label><br />
				
				<ul id="pagelist">
					<li><input name="order_type" type="radio" checked="checked" value="PagSeguro"> PagSeguro <em>(recomendado para pagamentos nacionais como boleto e transferencia eletronica)</em></li>
				</ul>	
			</p>
			
			<p>
				<label for="order_days">Periodo desta Contribuição</label><br />
				
				<ul id="pagelist">
					<li><input name="order_days" type="radio" value="30"> 30 dias - R$ 7.50</li>
					<li><input name="order_days" type="radio" value="60"> 60 dias - R$ 15.00</li>
					<li><input name="order_days" type="radio" value="90"> 90 dias - R$ 22.50</li>
				</ul>	
			</p>
			
			<h3>Observações IMPORTANTES:</h3>
			<ul>
				<li>Pagamentos via PagSeguro/Boleto Bancário são liberados apénas após compensar o pagamento (de 1 a 3 dias úteis).</li>
				<li>Pagamentos via PagSeguro/Transferencia Eletronica são liberados em um prazo de 6 horas.</li>
				<li><font color="red">Não efetuar depósito na conta informada no boleto, isto invalida o pagamento. O boleto deve ser quitado junto ao caixa de um banco credenciado.</font></li>
			</ul>
			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="Proximo" />					
			</p>
	</fieldset>
</form>';

}
?>