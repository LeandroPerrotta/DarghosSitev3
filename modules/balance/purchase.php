<?
if($_POST)
{	
	$player = new \Framework\Player();
	
	$form_OrderName = $_POST["order_name"];
	$form_AddBalance = $_POST["add_balance"];
	
	$account = \Framework\Account::loadLogged();
	if(!$form_OrderName or !$form_AddBalance)
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
	}
	elseif(!$account->getEmail())
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->OPERATION_REQUIRE_VALIDATED_EMAIL);
	}
	else
	{
		$success = true;
		
		$_SESSION['contribute']["order_name"] = $form_OrderName;
		$_SESSION['contribute']["add_balance"] = $form_AddBalance;
	
		$module .= '	
			<form action="?ref=balance.confirm" method="post">
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
						'.$account->getEmail().'.
					</p>				
			
					<p><h3>Informações do Pedido</h3></p>
					<div id="line1"></div>
					
					<p>
						<label for="order_days">Quantidade de Saldo a ser adicionado</label><br />
						R$ '.number_format($form_AddBalance / 100, 2).'
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
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
	}
	
$module .= '	
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>			
			
			<p><h3>Adicionar saldo a sua conta no '.getConf(confEnum()->WEBSITE_NAME).'</h3></p>
			
			<p>Para adicionar saldo a sua conta no '.getConf(confEnum()->WEBSITE_NAME).', preencha o formulário a seguir. Lembre-se que todos os campos são obrigatórios. Ao final do processo você será direcionado a pagina de nosso parceiro para concluir o pagamento em uma pagina segura (HTTPS) e será confirmado o seu pedido.</p>
		
			<p><h3>Informações Pessoais</h3></p>
			<div id="line1"></div>
	
			<p>
				<label for="order_name">Nome</label><br />
				<input name="order_name" size="40" type="text" value="" /> <br><em>(preencha com seu nome real e completo)</em>
			</p>			
			
			<p>
				<label for="order_days">Quantidade de saldo</label>						
				<ul style="list-style: none;">
					<li style="margin-left: 0px;">	
						<label style="margin-left: 6px; display: inline; line-height: 30px;">					
							<input style="margin-top: 0px;" name="add_balance" type="radio" value="500">
							<span style="font-size: 12px;">R$ 5,00</span>
						</label>							
					</li>

					<li style="margin-left: 0px;">	
						<label style="margin-left: 6px; display: inline; line-height: 30px;">					
							<input style="margin-top: 0px;" name="add_balance" type="radio" value="1000">
							<span style="font-size: 12px;">R$ 10,00</span>
						</label>							
					</li>					

					<li style="margin-left: 0px;">	
						<label style="margin-left: 6px; display: inline; line-height: 30px;">					
							<input style="margin-top: 0px;" name="add_balance" type="radio" value="2000">
							<span style="font-size: 12px;">R$ 20,00</span>
						</label>							
					</li>	

					<li style="margin-left: 0px;">	
						<label style="margin-left: 6px; display: inline; line-height: 30px;">					
							<input style="margin-top: 0px;" name="add_balance" type="radio" value="3000">
							<span style="font-size: 12px;">R$ 30,00</span>
						</label>							
					</li>	

					<li style="margin-left: 0px;">	
						<label style="margin-left: 6px; display: inline; line-height: 30px;">					
							<input style="margin-top: 0px;" name="add_balance" type="radio" value="5000">
							<span style="font-size: 12px;">R$ 50,00</span>
						</label>							
					</li>						
				</ul>	
			</p>
			
			<h3>Observações IMPORTANTES:</h3>
			<ul>
				<li>Saldos adicionados via PagSeguro/Boleto Bancário são liberados apénas após compensar o documento (de 1 a 3 dias úteis).</li>
				<li>Saldos adicionados via PagSeguro/Transferencia Eletronica são liberados em um prazo de 6 horas.</li>
				<li><font color="red">Muito importante! -> Não efetuar depósito na conta informada no boleto, isto invalida o pagamento. O boleto deve ser quitado junto ao caixa de um banco credenciado.</font></li>
			</ul>
			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="Proximo" />					
			</p>
	</fieldset>
</form>';

}
?>