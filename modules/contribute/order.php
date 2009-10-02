<?
$post = $core->extractPost();
if($post)
{	
	$character = $core->loadClass("character");
	
	$_paymentMethods = array("PagSeguro", "PayPal");
	$_paymentPeriods = array(7, 30, 60, 90, 180);

	if(!$post[0] or !$post[1] or !$post[2] or !$post[3] or !$post[4])
	{
		$error = "Preencha todos campos do formulario corretamente.";
	}
	elseif(!$character->loadByName($post[2]))
	{
		$error = "Este personagem n�o existe em nosso banco de dados.";
	}
	elseif(!in_array($_POST["order_type"], $_paymentMethods) or !in_array($_POST["order_days"], $_paymentPeriods))
	{
		$error = "Erro desconhecido, log reportado ao administrador.";
	}
	else
	{
		$success = true;
		
		foreach($post as $value)
		{
			$_SESSION['contribute'][] = $value;
		}
	
		$module .= '	
			<form action="?ref=contribute.confirm" method="post">
				<fieldset>			
					
					<p><h3>Confirma��o do Pedido</h3></p>
					
					<p>Analize abaixo se todos dados foram preenchidos corretamente, e se assim for clique no bot�o Confirmar e voc� ser� criado o seu pedido e voc� ser� direcionado a uma pagina para anotar o numero de seu pedido e ir ao site do '.$post[3].' para concluir a sua contribui��o.</p>
				
					<p><h3>Informa��es Pessoais</h3></p>
					<div id="line1"></div>
			
					<p>
						<label for="order_name">Nome</label><br />
						'.$post[0].'.
					</p>	

					<p>
						<label for="order_email">E-mail para Contato</label><br />
						'.$post[1].'.
					</p>				
			
					<p><h3>Informa��es da Contribui��o</h3></p>
					<div id="line1"></div>
			
					<p>
						<label for="order_target">Personagem Destino desta Contribui��o</label><br />
						'.$post[2].'
					</p>	
				
					<p>
						<label for="order_target">Forma de Pagamento desta Contribui��o</label><br />
						'.$post[3].'.
					</p>
					
					<p>
						<label for="order_days">Periodo e valor desta Contribui��o</label><br />
						
						'.$post[4].' dias por '.$_contribution[$post[3]][$post[4]].'.
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
		$core->sendMessageBox("Erro!", $error);
	}
	
$module .= '	
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>			
			
			<p><h3>Formul�rio de Pedido de Contribui��o</h3></p>
			
			<p>Preencha os campos abaixo atentamente para efetuar um pedido de contribui��o, todos os campos s�o obrigat�rios. Ao final do processo voc� ser� direcionado a pagina de nosso parceiro para concluir o pagamento em uma pagina segura (HTTPS) e ser� confirmado o seu pedido.</p>
		
			<p><h3>Informa��es Pessoais</h3></p>
			<div id="line1"></div>
	
			<p>
				<label for="order_name">Nome</label><br />
				<input name="order_name" size="40" type="text" value="" /> <br><em>(preencha com seu nome real e completo)</em>
			</p>	

			<p>
				<label for="order_email">E-mail para Contato</label><br />
				<input name="order_email" size="40" type="text" value="" /> <br><em>(ser� ultilizado para o envio da confirma��o de sua contribui��o)</em>
			</p>				
	
			<p><h3>Informa��es da Contribui��o</h3></p>
			<div id="line1"></div>
	
			<p>
				<label for="order_target">Personagem destino desta Contribui��o</label><br />
				<input name="order_target" size="40" type="text" value="" /> <br><em>(Preencha com o nome de um personagem de sua conta ou o nome do personagem um amigo caso seja um presente por exemplo.)</em>
			</p>	
		
			<p>
				<label for="order_target">Forma de Pagamento desta Contribui��o</label><br />
				
				<ul id="pagelist">
					<li><input name="order_type" type="radio" value="PagSeguro"> PagSeguro <em>(recomendado para pagamentos nacionais como boleto e transferencia eletronica)</em></li>
					<li><input name="order_type" type="radio" value="PayPal"> PayPal <em>(recomendado para pagamentos internacionais como cart�o de credito internacional)</em></li>
				</ul>	
			</p>
			
			<p>
				<label for="order_days">Periodo desta Contribui��o</label><br />
				
				<ul id="pagelist">
					<li><input name="order_days" type="radio" value="7"> 7 dias - R$ 2.90 / USD 1.90 </li>
					<li><input name="order_days" type="radio" value="30"> 30 dias - R$ 8.90 / USD 5.90 </li>
					<li><input name="order_days" type="radio" value="60"> 60 dias - R$ 16.55 / USD 8.55 </li>
					<li><input name="order_days" type="radio" value="90"> 90 dias - R$ 22.90 / USD 13.55 </li>
					<li><input name="order_days" type="radio" value="180"> 180 dias - R$ 41.55 / USD 24.90 </li>
				</ul>	
			</p>
			
			<h3>Observa��es:</h3>
			<ul>
				<li>R$ <em>(valor em reais, para contribui��es por PagSeguro).</em></li>
				<li>USD <em>(valor em dolares americanos, para contribui��es por PayPal).</em></li>
				<li>Pagamentos via PagSeguro/Boleto Banc�rio s�o liberados ap�nas ap�s compensar o pagamento (de 1 a 3 dias �teis).</li>
				<li>Pagamentos via PagSeguro/Transferencia Eletronica s�o liberados imediatamente.</li>
				<li><font color="red">N�o efetuar dep�sito na conta informada no boleto, sujeito a invalida��o do pagamento. O boleto deve ser quitado junto ao caixa de um banco credenciado.</font></li>
			</ul>
			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="Proximo" />					
			</p>
	</fieldset>
</form>';

}
?>