<?
if($_POST)
{	
	$core->extractPost();
	
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0]);
	
	$ticket  = $core->loadClass("Tickets");
	$string  = $core->loadClass("Strings");
	
	if(!$_POST['ticket_title'] or !$_POST['ticket_main'])
	{
		$error = $boxMessage['INCOMPLETE_FORM'];
	}
	else
	{
		$_account 	= $account->getId();
		$_player	= $account->getHighCharacter();
		$_title		= $string->SQLInjection($_POST['ticket_title']);
		$_main		= $string->SQLInjection($_POST['ticket_main']);
		$_type		= $_POST['type'];
				 
		$ticket->sendNew(0,$_player,$_account,$_title,$_main, time(), $_type, 0, 1);
		
		$success = "Seu ticket foi enviado com sucesso, visite a pagina Meus Tickets, para visualisar as respostas e o andamento dos mesmos. <p>Atenciosamente,<br>Equipe Ultraxsoft. </p>";
	}
}

if($success)	
{
	$core->sendMessageBox($boxMessage['SUCCESS'], $success);
}
else
{
	if($error)	
	{
		$core->sendMessageBox($boxMessage['ERROR'], $error);
	}


$module .=	'
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
			
			<p>
				<label for="ticket_title">Titulo</label><br />
				<input id="ticket_title" name="ticket_title" size="40" type="text" value="" />
			</p>
			
			<p>
				<label for="type">Categoria</label><br />
				<select name="type" style="width:90px;">
					<option value="1">Website</option>				
					<option value="2">Jogo</option>
					<option value="3">Premium</option>
				</select>
			</p>
			
			<p>
				<label for="ticket_main">Ticket</label><br />
				<textarea id="ticket_main" name="ticket_main" cols="80" rows="10" type="text" value=""/></textarea>
			</p>			

			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
			</p>
		</fieldset>
	</form>';

}
?>	
