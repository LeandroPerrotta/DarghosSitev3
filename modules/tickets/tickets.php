<?
	$module .=	"

		<p>
			Caro jogador, o sistema de Tickets foi momentaneamente desativado para que seja efetuadas algumas melhorias no sistema, tais melhorarias permitirão a equipe responder todos tickets de forma mais rápida e eficaz. Caso você tenha algum problema com relação a Conta Premium, Bugs, etc você ainda pode nos contactar atravez dos nossos emails: <b>premium@darghos.com</b> (para assuntos relacionados a premium) e <b>suporte@darghos.com</b> (para assuntos gerais). Obrigado pela sua compreensão.
		</p>
	";

	/*
	$core->extractPost();
	
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0]);
	
	$ticket  = $core->loadClass("Tickets");
	$string  = $core->loadClass("Strings");
	
	$query = $db->query("SELECT id FROM wb_tickets WHERE account = '{$account->getId()}' ORDER by send_date DESC");
	

	$module .=	"

		<p>
			Abaixo você poderá visualizar o andamento de seus tickets. Lembre-se de que qualquer atitude inaceitável dentro dos tickets, pode resultar em um alerta em sua conta, ou ate mesmo o banimento da mesma.
		</p>
	";



	$module .= "
	<table cellspacing='0' cellpadding='0' id='table'>
		<tr>
			<th width='45%'>Titulo</th> <th>Enviado</th> <th>Categoria</th> <th>Estado</th>
		</tr>	
	";	
	
while($fetch = $query->fetch())
{
	$ticket = $core->loadClass("Tickets");
	$ticket->load($fetch->id);
	
	$type = Tools::GetTicketTypeName($ticket->getType());;	
	
	if($ticket->getClosed() != 0)
	{
		$state = "Fechado";
	}
	else 
	{
		$state = "Aberto";
	}
	
	if(strlen($ticket->getTitle()) > 32)
	{
		$formated = substr($ticket->getTitle(),0,31);
		$titulo = $formated."...";
	}
	else 
	{
		$titulo = $ticket->getTitle();
	}
	
	$module .= "
		<tr>
			<td class='name'><a href='?ref=tickets.view&id={$ticket->getID()}'>{$titulo}</a></td><td>{$core->formatDate($ticket->getSendDate())}</td><td>$type</td><td>$state</td>
		</tr>
	";
}

$module .= "
</table>
";*/

?>	
