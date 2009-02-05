<?
$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "password");
$secretkey = $account->getSecretKey();

$player_list = $account->getCharacterList();
$character = $core->loadClass("Character");

$contribute = $core->loadClass("Contribute");
$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);

if(is_array($oders))
{
	foreach($oders as $orderId);
	{
		$contribute->load($orderId, "status");
		
		if($contribute->get("status") == 1)
			$confirmed++;
	}
}

if(is_array($player_list))
{
	foreach($player_list as $player)
	{
		$character->loadByName($player);
		
		if($character->deletionStatus())
			$charDel[$player] = $character->deletionStatus();
	}
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

if(!$secretkey)
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Aten��o:</font> Caro jogador, sua conta ainda n�o possui uma chave secreta configurada, esta chave � necessaria em situa��es criticas para recuperar sua conta. Recomendamos que voc� gere a sua chave secreta agora mesmo clicando <a href="?ref=account.secretkey">aqui</a>.';
}

if(isset($charDel))
{
	foreach($charDel as $name => $deletion)
	{
		$module .= '
		<p><font style="color: red; font-weight: bold;">Aten��o:</font> O seu personagem <b>'.$name.'</b> est� agendado para ser deletado do jogo no dia '.$core->formatDate($deletion).'. Para cancelar este opera��o clique <a href="?ref=character.undelete&name='.$name.'">aqui</a>.';
	}
}
?>