<?
$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "password");

$contribute = $core->loadClass("Contribute");
$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);

foreach($oders as $orderId);
{
	$contribute->load($orderId, "status");
	
	if($contribute->get("status") == 1)
		$confirmed++;
}


$module .= '
<p>Seja bem vindo a sua conta. Voc� pode efetuar muitas opera��es como criar um personagem, mudar sua senha ou obter a conta premium atravez do menu minha conta ao lado esquerdo.';

if(is_array($newemail = $account->getEmailToChange()))
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Aten��o:</font> Existe uma mudan�a de email registrado em sua conta para o endere�o '.$newemail['email'].' que foi agendada para o dia '.$core->formatDate($newemail['date']).'. Voc� pode cancelar est� mudan�a a qualquer momento clicando <a href="?ref=account.cancelchangeemail">aqui</a>.';
}

if($confirmed and $confirmed >= 1)
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Aten��o:</font> Caro jogador, um pedido efetuado por sua conta foi confirmado com sucesso! Voc� j� pode aceitar este pagamento ou visualizar maiores informa��es deste pedindo na categoria Conta Premium, na se��o Meus Pedidos. Tenha um bom jogo!';
}

?>