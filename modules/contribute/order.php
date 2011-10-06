<?
if($_POST)
{	
	$character = new Character();
	
	$form_OrderName = $_POST["order_name"];
	$form_OrderMail = $_POST["order_email"];
	$form_OrderTarget = $_POST["order_target"];	
	$form_OrderDays = $_POST["order_days"];
	
	$account = Account::loadLogged();
	if(!$form_OrderName or !$form_OrderMail or !$form_OrderTarget or !$form_OrderDays)
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
	elseif(!Contribute::isValidPeriod($form_OrderDays))
	{
		$error = Lang::Message(LMSG_REPORT);
	}
	else
	{
		$success = true;
		
		$_SESSION['contribute']["order_name"] = $form_OrderName;
		$_SESSION['contribute']["order_email"] = $form_OrderMail;
		$_SESSION['contribute']["order_target"] = $form_OrderTarget;
		$_SESSION['contribute']["order_period"] = $form_OrderDays;
		
		$premiumInfos =  Contribute::getPremiumInfoByPeriod($form_OrderDays);
	
		$module .= '	
			<form action="?ref=contribute.confirm" method="post">
				<fieldset>			
					
					<p><h3>Confirmação do Pedido</h3></p>
					
					<p>Verifique abaixo se todos dados foram preenchidos corretamente, e se assim for clique no botão Confirmar e você será criado o seu pedido e você será direcionado a uma pagina para anotar o numero de seu pedido e ir ao site do '.$form_OrderType.' para concluir a sua contribuição.</p>
				
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
						<label for="order_days">Detalhes da contribuição</label><br />
						'.$premiumInfos["product"].' por '.Contribute::formatCost($premiumInfos["cost"]).'.
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
				<label for="order_days">Periodo desta Contribuição</label>						
				<ul style="list-style: none;">
					';

						foreach(Contribute::$premiums as $k => $premium)
						{
							$premium = Contribute::getPremiumInfoByPeriod($premium["period"]);		
							$module .= "
							
								<li style='margin-left: 0px;'>	
									<label style='margin-left: 6px; display: inline; line-height: 30px;'>					
										<input style='margin-top: 0px;' name='order_days' type='radio' value='{$premium["period"]}'>
										<span style='font-size: 12px;'>{$premium["text"]}</span> por
										<span style='font-weight: bold;'>".Contribute::formatCost($premium["cost"])."</span>	
									</label>							
								</li>
							";
						}
					
					$module .= '
				</ul>	
			</p>
			
			<h3>Observações IMPORTANTES:</h3>
			<ul>
				<li>A promoção do brinde Yalaharian Outfit Ticket é valida apénas para pedidos para conta premium de 180 dias e gerados até 23:59 do dia 15/10.</li>
				<li>O outfit ticket brinde para o Dia das Criança concede apénas uma unica parte do Outfit ou seus Addon que você não possua, na ordem: Outfit, 1o Addon, 2o Addon.</li>
				<li>O outfit ticket será recebido no primeiro login do personagem escolhido acima para receber a Conta Premium após esta liberada e aceita.</li>
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