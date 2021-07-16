<?
$db_elerian = new MySQL();
$db_elerian->connect("localhost", "other", "SECRETPASS", "eleriannew");

$post = $core->extractPost();

if($_SESSION['loginElerian'])
{
	$query = $db_elerian->query("SELECT id, name, premend FROM accounts WHERE id = '{$_SESSION['loginElerian'][0]}'");
	$acc_fetch = $query->fetch();
	
	if($_GET["action"] == "importPremium")
	{
		$leftDays = $acc_fetch->premend - time();
		$leftDays = ($leftDays > 0) ? floor($leftDays / 86400) : 0;	

		if($leftDays > 0)
		{
			$account = $core->loadClass("account");
			$account->load($_SESSION["login"][0]);
			
			$account->updatePremDays($leftDays);		
			$account->save();	
			
			$db_elerian->query("UPDATE accounts SET premend = '0' WHERE id = '{$_SESSION['loginElerian'][0]}'");
			
			$core->sendMessageBox("Sucesso!", "A sua conta recebeu {$leftDays} dias de Conta Premium importado de sua antiga conta de Elerian!");
		}
	}
	elseif($_GET["action"] == "importOrder")
	{
		$orderId = $_GET["id"];
		$db_contr = new MySQL();
		$db_contr->connect(DB_ULTRAXSOFT_HOST, DB_ULTRAXSOFT_USER, DB_ULTRAXSOFT_PASS, DB_ULTRAXSOFT_SCHEMA);		
		
		$db_contr->query("UPDATE orders SET target_account = '{$_SESSION["login"][0]}', server = '1' WHERE md5(id) = '{$orderId}'");
	
		$core->sendMessageBox("Sucesso!", "O seu antigo pedido de Elerian foi transferido para esta conta com sucesso!");
	}
	elseif($_GET["action"] == "logout")
	{
		unset($_SESSION['loginElerian']);
		$core->redirect("?ref=account.importElerian");
	}	
	else
	{
		$leftDays = $acc_fetch->premend - time();
		$leftDays = ($leftDays > 0) ? floor($leftDays / 86400) : 0;	
		
		$module .= "
		<p><h3>Importar Conta Premium:</h3></p>
		<div id='line1'></div>";		
		
		if($leftDays > 0)
		{
			$module .= "
				<p>A sua conta {$acc_fetch->name} em Elerian possui {$leftDays} dias restantes. Para importar estes dias para esta conta no novo Darghos clique <a href='?ref=account.importElerian&action=importPremium'>aqui</a>.</p>
			";		
		}
		else
		{
			$module .= "
				<p>A sua conta {$acc_fetch->name} em Elerian não possuia Conta Premium.</p>
			";			
		}
		
		$module .= "
		<p><h3>Importar a Aceitar:</h3></p>
		<div id='line1'></div>";			
		
		$db_contr = new MySQL();
		$db_contr->connect(DB_ULTRAXSOFT_HOST, DB_ULTRAXSOFT_USER, DB_ULTRAXSOFT_PASS, DB_ULTRAXSOFT_SCHEMA);	
		
		$query_contr = $db_contr->query("SELECT * FROM orders WHERE target_account = '{$acc_fetch->id}' and `server` = '2' and `status` = '1'");
		
		if($query_contr->numRows() != 0)
		{
			while($fetch = $query_contr->fetch())
			{
				$module .= "
					<p>Pedido {$fetch->id} em {$core->formatDate($fetch->generated_in)} para {$fetch->period} dias. <a href='?ref=account.importElerian&action=importOrder&id=".md5($fetch->id)."'>Transferir pedido</a>.</p>
				";			
			}
		}
		else
		{
			$module .= "
				<p>A sua conta {$query->fetch()->name} em Elerian não possui nenhum pedido pendente a aceitação.</p>
			";			
		}	

		$module .= "<p>Para importar outra conta clique <a href='?ref=account.importElerian&action=logout'>aqui</a>.</p>";
	}
}
else
{
	if($post)
	{
		$query = $db_elerian->query("SELECT id, password, premend FROM accounts WHERE name = '{$strings->SQLInjection($_POST['elerian_account'])}'");	
		$fetch = $query->fetch();
		
		if($query->numRows() == 0)
		{
			$error = "Esta conta não existia em Elerian ou a senha está incorreta.";
		}
		elseif($fetch->password != $strings->encrypt($_POST['elerian_password']))
		{
			$error = "Esta conta não existia em Elerian ou a senha está incorreta.";
		}			
		else
		{								
			$_SESSION['loginElerian'][0] = $fetch->id;
			$_SESSION['loginElerian'][1] = $fetch->password;
			
			$core->redirect("?ref=account.importElerian");
		}
	}
	

	if($error)	
	{
		$core->sendMessageBox("Erro!", $error);
	}

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>Com este recurso você poderá importar sua Conta Premium em andamento de Elerian para esta conta no novo Darghos. Ou ainda importar um pedido pendente de aceitação. Abaixo preencha os campos com o seu antigo login de Elerian para que o sistema rastreie as operações possiveis para sua conta.</p>	
		
		<p>
			<label for="elerian_account">Elerian Numero/Nome da Conta</label><br />
			<input name="elerian_account" size="40" type="password" value="" />
		</p>	

		<p>
			<label for="elerian_password">Elerian Senha</label><br />
			<input name="elerian_password" size="40" type="password" value="" />
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';
}
?>