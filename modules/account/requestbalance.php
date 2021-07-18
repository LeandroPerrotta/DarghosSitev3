<?
if($_POST)
{
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	if(!$_POST["ref"]  || !$_POST["auth"])
	{
		$error = "Preencha todos os campos.";
	}
	else
	{		        
        if(!\Core\Emails::send("platinum@darghos.com", \Core\Emails::EMSG_REQUEST_BALANCE, array($_POST["auth"], $_POST["ref"], $account->getName(), $_POST["nome"], $_POST["end1"], $_POST["end2"], $_POST["email"])))
        {
            $error = \Core\Lang::Message(\Core\Lang::$e_Msgs->FAIL_SEND_EMAIL);
        }        
	    else
		  $success = "Estaremos analisando o seu pedido, seu saldo será debitado em sua conta o mais rápido possível.";
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
		
        <h2 style="margin-top: 25px;">Informações sobre a Reativação de Saldo</h2>

 		<p>
			Todos os dados solicitados são encontrados em:
            
            <ul>
                <li>Informação do pedido em sua conta no PagSeguro</li>
                <li>E-mail enviado pelo PagSeguro no dia da compra</li>
            </ul>
		</p>       
        
        <h2 style="margin-top: 25px;">Formulário de Reativação de Saldo</h2>
        
		<p>
			<label for="auth">Código de Transação PagSeguro</label><br />
			<input name="auth" size="40" type="text" value="" />
		</p>
		
		<p>
			<label for="ref">Código de Referência</label><br />
			<input name="ref" size="40" type="text" value="" />
		</p>  
		
		<p>
			<label for="ref">Nome (usado no PagSeguro)</label><br />
			<input name="nome" size="40" type="text" value="" />
		</p>  
        
		<p>
			<label for="ref">Endereço (usado no PagSeguro)</label><br />
			<input name="end1" size="40" type="text" value="" /> <br>
			<input name="end2" size="40" type="text" value="" />
		</p>       

  		<p>
			<label for="ref">E-mail (usado no PagSeguro)</label><br />
			<input name="email" size="40" type="text" value="" />
		</p>       
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
		</p>
	</fieldset>
</form>';

}
?>