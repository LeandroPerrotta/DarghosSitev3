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
        $_acc = new \Framework\Account();
        if($_acc->loadByName($_POST["acc_name"]))
        {
            $_acc->addBalance($_POST["balance"]);
            $_acc->balanceRequest($_POST["auth"], $_POST["ref"], $_POST["balance"]);
            $_acc->save();
            
            $success = "Pedido liberado.";
        }        
	    else
		  $error = "Conta não encontrada.";
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
			<label for="auth">Código de Transação PagSeguro</label><br />
			<input name="auth" size="40" type="text" value="" />
		</p>
		
		<p>
			<label for="ref">Código de Referência</label><br />
			<input name="ref" size="40" type="text" value="" />
		</p>  
		
		<p>
			<label for="acc_name">Conta</label><br />
			<input name="nome" size="40" type="text" value="" />
		</p>  
        
		<p>
			<label for="value">Valor</label><br />
			<select name="balance">
                <option value="500">R$ 5,00</option>
                <option value="1000" selected="selected">R$ 10,00</option>
                <option value="2000">R$ 20,00</option>
                <option value="3000">R$ 30,00</option>
                <option value="5000">R$ 50,00</option>
            </select>
		</p>          
          
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
		</p>
	</fieldset>
</form>';

}
?>