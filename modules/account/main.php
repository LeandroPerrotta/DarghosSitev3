<?
$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "password, premdays, warnings, email, creation, real_name, location, url");
$secretkey = $account->getSecretKey();

$player_list = $account->getCharacterList();
$character = $core->loadClass("Character");

$premium = ($account->get("premdays") > 1) ? $account->get("premdays")." dias restantes" : "Você não possui dias de conta premium.";	
$warns = ($account->get("warnings") > 1) ? "Sua conta possui".$account->get("warnings")." warnings." : "Sua conta não possui warnings.";	
$email = $account->get("email");	
$creation = ($account->get("creation") != 0) ? $core->formatDate($account->get("creation")) : "Indisponível";	
$realname = ($account->get("real_name")) ?	$account->get("real_name") : "<i>Sem Nome</i>";
$location = ($account->get("location")) ?	$account->get("location") : "<i>Sem Localidade</i>";
$url = ($account->get("url")) ?	$account->get("url") : "<i>Sem Endereço</i>";

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
		$character->loadByName($player, "name");
		
		if($character->deletionStatus())
			$charDel[$player] = $character->deletionStatus();
			
		$charList .= "
		<tr>
			<td><a href='?ref=character.view&name={$character->get("name")}'>{$character->get("name")}</a></td> <td>nenhum</td> <td><a href='?ref=character.edit&name={$character->get("name")}'>Editar</a> - <a href='?ref=character.itemshop&name={$character->get("name")}'>Item Shop</a></td>
		</tr>		
		";	
	}
}


$module .= "
<p>Seja bem vindo a sua conta, {$realname}. Você pode efetuar muitas operações como criar um personagem, mudar sua senha ou obter a conta premium atravez do menu minha conta ao lado esquerdo.";

if(is_array($newemail = $account->getEmailToChange()))
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Atenção:</font> Existe uma mudança de email registrado em sua conta para o endereço '.$newemail['email'].' que foi agendada para o dia '.$core->formatDate($newemail['date']).'. Você pode cancelar está mudança a qualquer momento clicando <a href="?ref=account.cancelchangeemail">aqui</a>.';
}

if($confirmed and $confirmed >= 1)
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Atenção:</font> Caro jogador, um pedido efetuado por sua conta foi confirmado com sucesso! Você já pode aceitar este pagamento ou visualizar maiores informações deste pedindo na categoria Conta Premium, na seção Meus Pedidos. Tenha um bom jogo!';
}

if(!$secretkey)
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Atenção:</font> Caro jogador, sua conta ainda não possui uma chave secreta configurada, esta chave é necessaria em situações criticas para recuperar sua conta. Recomendamos que você gere a sua chave secreta agora mesmo clicando <a href="?ref=account.secretkey">aqui</a>.';
}

if(isset($charDel))
{
	foreach($charDel as $name => $deletion)
	{
		$module .= '
		<p><font style="color: red; font-weight: bold;">Atenção:</font> O seu personagem <b>'.$name.'</b> está agendado para ser deletado do jogo no dia '.$core->formatDate($deletion).'. Para cancelar este operação clique <a href="?ref=character.undelete&name='.$name.'">aqui</a>.';
	}
}		

$module .= "
<p>
	<table cellspacing='0' cellpadding='0' id='table'>
	
		<tr>
			<th colspan='2'>Informações da Conta</th>
		</tr>
		
		<tr>
			<td width='30%'><b>Endereço de E-mail:</b></td><td>{$email}</td>
		</tr>
					
		<tr>
			<td><b>Conta premium:</b></td><td>{$premium}</td>
		</tr>
		
		<tr>
			<td><b>Warnings:</b></td><td>{$warns}</td>
		</tr>
		
		<tr>
			<td><b>Criação:</b></td><td>".$creation."</td>
		</tr>
		
	</table>
</p>

<p>
	<a class='buttonstd' href='?ref=account.changepassword'>Mudar Senha</a> <a class='buttonstd' href='?ref=account.changeemail'>Mudar E-mail</a>
</p>

<p>
	<table cellspacing='0' cellpadding='0' id='table'>
	
		<tr>
			<th colspan='2'>Informações Personalizadas</th>
		</tr>
		
		<tr>
			<td width='30%'><b>Nome Real:</b></td><td>{$realname}</td>
		</tr>
					
		<tr>
			<td><b>Localização:</b></td><td>{$location}</td>
		</tr>
		
		<tr>
			<td><b>Website:</b></td><td>{$url}</td>
		</tr>
		
	</table>
</p>

<p>
	<a class='buttonstd' href='?ref=account.changeinfos'>Mudar Informações</a>
</p>

<p>
	<table cellspacing='0' cellpadding='0' id='table'>
	
		<tr>
			<th colspan='3'>Meus Personagens</th>
		</tr>
		
		<tr>
			<td width='30%'><b>Nome:</b></td> <td width='35%'><b>Status:</b></td> <td><b>Opções</b> </td>
		</tr>
					
		$charList
		
	</table>
</p>

<p>
	<a class='buttonstd' href='?ref=character.create'>Criar Personagem</a> <a class='buttonstd' href='?ref=character.delete'>Deletar Personagem</a>
</p>
";	
				
?>