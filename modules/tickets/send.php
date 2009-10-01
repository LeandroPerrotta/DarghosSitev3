<?
include("classes/contribute.php");

$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0]);

$list = $account->getCharacterList();

if($_POST)
{	
	$core->extractPost();
	
	$ticket  = $core->loadClass("Tickets");
	$string  = $core->loadClass("Strings");
	
	if(!$_POST['ticket_title'] or !$_POST['ticket_main'])
	{
		$error = $boxMessage['INCOMPLETE_FORM'];
	}
	else
	{
		$_account 	= $account->getId();
		$player = $core->loadClass("Character");
		
		if($_POST['type'] != 2)
		{
			$_player	= 0;
		}
		else
		{
			$player->loadByName($_POST['ticket_character']);
			$_player	= $player->getId();	
		}
			
		$_title		= $string->SQLInjection($_POST['ticket_title']);
		$_main		= $string->SQLInjection($_POST['ticket_main']);
		$_type		= $_POST['type'];

		if($_POST['ticket_attachment'])
			$ticket->sendNew(0,$_player,$_account,$_title,$_main, time(), $_type, 0, 1, 0, $_POST['ticket_attachment']);
		else	
			$ticket->sendNew(0,$_player,$_account,$_title,$_main, time(), $_type, 0, 1, 0);
		
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


	if(is_array($list))
	{	
		foreach($list as $pid)
		{
			$characterList .= '<option value="'.$pid.'">'.$pid.'</option>';
		}
	}	
	
	$contribute = new Contribute();
	$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);
	
	if(is_array($oders))
	{
		foreach($oders as $orderId)
		{
			$contribute->load($orderId, "id, name, target, type, period, cost, generated_in, status");	
			
			$ordersList .= "<option value='{$contribute->get("id")}'>{$contribute->get("id")} de {$contribute->get("cost")} em {$core->formatDate($contribute->get("generated_in"))}</option>";
		}
	}
	
$module .=	'
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
			
			<p>
				<label for="ticket_title">Titulo</label><br />
				<input id="ticket_title" name="ticket_title" size="40" type="text" value="" />
			</p>
			
			<div class="autoaction" style="margin: 0px; padding: 0px;">
				<p>
					<label for="type">Categoria</label><br />
					<select name="type">
						<option value="2">Jogo</option>
						<option value="1">Website</option>					
						<option value="3">Conta Premium</option>
					</select>
				</p>
			</div>	
			
			<p>
				<label for="ticket_main">Ticket</label><br />
				<textarea id="ticket_main" name="ticket_main" cols="80" rows="10" type="text" value=""/></textarea>
			</p>	

			<div title="2" class="viewable" style="margin: 0px; padding: 0px;">			
				<p>
					<label for="ticket_character">Personagem </label><br />
					<select name="ticket_character">
					
						'.$characterList.'
					
					</select>
				</p>			
			
				<p>
					<label for="ticket_attachment">Imagem de Acompanhamento</label><br />
					<input name="ticket_attachment" size="40" type="text" value="" />
					<br /><em>Se possivel, para facilitar e agilizar a solução de seu ticket tire um print screen da tela na qual apresenta o problema e o coloque em um serviço de hospedagem de imagens (por exemplo o Imageshack: http://imageshack.us/). Após isso, cole o link fornecido pelo serviço para acessar a imagem neste campo e nós poderemos ver com maior clareza o problema.</em>
				</p>	
			
			</div>		

			<div title="3" style="margin: 0px; padding: 0px;">			
				<p>
					<label for="ticket_attachment">Pedido relacionado </label><br />
					<select name="ticket_attachment">
					
						'.$ordersList.'
					
					</select>
				</p>						
			</div>				

			
			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="'.$buttons['SUBMIT'].'" />
			</p>
		</fieldset>
	</form>';

}
?>	
