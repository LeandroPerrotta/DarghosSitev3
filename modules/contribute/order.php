<?
$post = $core->extractPost();
if($post)
{	
	$character = $core->loadClass("character");

	if(!$post[0] or !$post[1] or !$post[2] or !$post[3] or !$post[4])
	{
		$error = "Preencha todos campos do formulario corretamente.";
	}
	elseif(!$character->loadByName($post[2]))
	{
		$error = "Este personagem não existe em nosso banco de dados.";
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
					
					<p><h3>Confirmação do Pedido</h3></p>
					
					<p>Analize abaixo se todos dados foram preenchidos corretamente, e se assim for clique no botão Confirmar e você será criado o seu pedido e você será direcionado a uma pagina para anotar o numero de seu pedido e ir ao site do '.$post[3].' para concluir a sua contribuição.</p>
				
					<p><h3>Informações Pessoais</h3></p>
					<div id="line1"></div>
			
					<p>
						<label for="order_name">Nome</label><br />
						'.$post[0].'.
					</p>	

					<p>
						<label for="order_email">E-mail para Contato</label><br />
						'.$post[1].'.
					</p>				
			
					<p><h3>Informações da Contribuição</h3></p>
					<div id="line1"></div>
			
					<p>
						<label for="order_target">Personagem Destino desta Contribuição</label><br />
						'.$post[2].'
					</p>	
				
					<p>
						<label for="order_target">Forma de Pagamento desta Contribuição</label><br />
						'.$post[3].'.
					</p>
					
					<p>
						<label for="order_days">Periodo e valor desta Contribuição</label><br />
						
						'.$post[4].' dias por '.$_contribution[$post[3]][$post[4]].'.
					</p>
					
					<div id="line1"></div>
					
					<p>
						<input type="submit" value="Confirmar" />					
					</p>
			</fieldset>
		</form>';		
	}
}

if(!$success)	
{
	if($error)	
	{
		$module .=	'
		
		<div id="error">
			<h2>'.$error.'</h2>
		</div>
		
		';
	}
	
$module .= '	
	<form action="" method="post">
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
				<label for="order_target">Destino desta Contribuição</label><br />
				<input name="order_target" size="40" type="text" value="" /> <br><em>(Preencha com o nome de um personagem de sua conta ou o nome do personagem um amigo caso seja um presente por exemplo.)</em>
			</p>	
		
			<p>
				<label for="order_target">Forma de Pagamento desta Contribuição</label><br />
				
				<ul id="pagelist">
					<li><input name="order_type" type="radio" value="PagSeguro"> PagSeguro <em>(recomendado para pagamentos nacionais como boleto e transferencia eletronica)</em></li>
					<li><input name="order_type" type="radio" value="PayPal"> PayPal <em>(recomendado para pagamentos internacionais como cartão de credito internacional)</em></li>
				</ul>	
			</p>
			
			<p>
				<label for="order_days">Periodo desta Contribuição</label><br />
				
				<ul id="pagelist">
					<li><input name="order_days" type="radio" value="30"> 30 dias - R$ 14.55 / USD 6.45 </li>
					<li><input name="order_days" type="radio" value="60"> 60 dias - R$ 25.55 / USD 11.45 </li>
					<li><input name="order_days" type="radio" value="90"> 90 dias - <del>R$ 45.55</del> R$ 35.55 / USD 20.45 <strong>*</strong> </li>
					<li><input name="order_days" type="radio" value="180"> 180 dias - <del>R$ 55.55</del> R$ 49.55 / USD 29.95 <strong>*</strong> </li>
					<li><input name="order_days" type="radio" value="360"> 360 dias - <del>R$ 89.55</del> R$ 75.55 / USD 39.95 <strong>*</strong> </li>
				</ul>	
			</p>
			
			<h1>Descrição:</h1>
			<ul>
				<li>* <em>(item com preço promocional valido até dia 30/02/2009)</em></li>
				<li>R$ <em>(valor em reais, para contribuições por PagSeguro)</em></li>
				<li>USD <em>(valor em dolares americanos, para contribuições por PayPal)</em></li>
			</ul>
			
			<div id="line1"></div>
			
			<p>
				<input type="submit" value="Proximo" />					
			</p>
	</fieldset>
</form>';

}
?>