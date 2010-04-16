<?php
if($_POST)
{
	$player = new Character();
	
	if(!$_POST["addprize_character"])
	{
		$error = "Preencha todos campos do formulario corretamente.";
	}
	elseif(!$player->loadByName($_POST["addprize_character"]))
	{
		$error = "Este personagem n�o existe.";
	}
	else
	{
		$_accountp = $player->getAccountId();
		
		$_account = new Account();
		$_account->load($_accountp);
		
		$_account->setCanSeeAdPage();
		$_account->save();
		
		$success = "
		<p>A conta do personagem {$_POST["addprize_character"]} agora pode visualizar a pagina de click por premium!</p>
		";		
	}
}

if($success)	
{
	Core::sendMessageBox("Sucesso!", $success);
}	
elseif($error)	
{
	Core::sendMessageBox("Erro!", $error);
}

$module .=	'
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>			
		
		<p>
			<label for="addprize_character">Nome do Personagem</label><br />
			<input id="addprize_character" name="addprize_character" size="40" type="text" value="" />
		</p>	

		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>			

	</fieldset>
</form>';	
?>