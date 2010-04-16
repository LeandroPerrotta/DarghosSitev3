<?
$module .=	"

	<p>
		Caro jogador, o sistema de Tickets foi momentaneamente desativado para que seja efetuadas algumas melhorias no sistema, tais melhorarias permitir�o a equipe responder todos tickets de forma mais r�pida e eficaz. Caso voc� tenha algum problema com rela��o a Conta Premium, Bugs, etc voc� ainda pode nos contactar atravez dos nossos emails: <b>premium@darghos.com</b> (para assuntos relacionados a premium) e <b>suporte@darghos.com</b> (para assuntos gerais). Obrigado pela sua compreens�o.
	</p>
";
/*
include("classes/contribute.php");

$account = new Account();
$account->load($_SESSION['login'][0]);

$list = $account->getCharacterList();

if($_POST)
{	
	Core::extractPost();
	
	$ticket  = new Tickets();
	
	if(!$_POST['ticket_title'] or !$_POST['ticket_main'])
	{
		$error = $boxMessage['INCOMPLETE_FORM'];
	}
	else
	{
		$_account 	= $account->getId();
		$player = new Character();
		
		if($_POST['type'] != 2)
		{
			$_player	= 0;
		}
		else
		{
			$player->loadByName($_POST['ticket_character']);
			$_player	= $player->getId();	
		}
			
		$_title		= Strings::SQLInjection($_POST['ticket_title']);
		$_main		= Strings::SQLInjection($_POST['ticket_main']);
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
	Core::sendMessageBox($boxMessage['SUCCESS'], $success);
}
else
{
	if($error)	
	{
		Core::sendMessageBox($boxMessage['ERROR'], $error);
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
			
			$ordersList .= "<option value='{$contribute->get("id")}'>{$contribute->get("id")} de {$contribute->get("cost")} em {Core::formatDate($contribute->get("generated_in"))}</option>";
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
					<br /><em>Se possivel, para facilitar e agilizar a solu��o de seu ticket tire um print screen da tela na qual apresenta o problema e o coloque em um servi�o de hospedagem de imagens (por exemplo o Imageshack: http://imageshack.us/). Ap�s isso, cole o link fornecido pelo servi�o para acessar a imagem neste campo e n�s poderemos ver com maior clareza o problema.</em>
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

}*/
?>	
