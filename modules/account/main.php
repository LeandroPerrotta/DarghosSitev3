<?
$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0]);
$secretkey = $account->getSecretKey();

$player_list = $account->getCharacterList();
$character = $core->loadClass("Character");

$premium = ($account->getPremDays() > 1) ? $account->getPremDays()." dias restantes" : "Voc� n�o possui dias de conta premium.";	
$warns = ($account->getWarnings() > 1) ? "Sua conta possui".$account->getWarnings()." warnings." : "Sua conta n�o possui warnings.";	
$email = $account->getEmail();	
$creation = ($account->getCreation() != 0) ? $core->formatDate($account->getCreation()) : "Indispon�vel";	
$realname = ($account->getRealName()) ?	$account->getRealName() : "<i>Sem Nome</i>";
$location = ($account->getLocation()) ?	$account->getLocation() : "<i>Sem Localidade</i>";
$url = ($account->getUrl()) ?	$account->getUrl() : "<i>Sem Endere�o</i>";

$contribute = $core->loadClass("Contribute");
$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);

$bans = $core->loadClass('bans');

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
		
		unset($charStatus);
		unset($statusString);
		unset($charOptions);
		
		$charStatus = array();
		$charOptions = "<a href='?ref=character.edit&name={$character->getName()}'>Editar</a>";
		
		if(SHOW_SHOPFEATURES == 1)
		{
			$charOptions .= " - <a href='?ref=character.itemshop&name={$character->getName()}'>Item Shop</a>";
		}
		
		if($character->deletionStatus())
		{
			$charStatus[] = "<font color='red'>ser� deletado em: {$core->formatDate($character->deletionStatus())}</font>";
			$charOptions .= " - <a href='?ref=character.undelete&name={$character->getName()}'>Cancelar Exclus�o</a>";
		}
		
		if($character->get("hide") == 1)
		{
			$charStatus[] = "escondido";
		}
		
		if($bans->isNameLocked($character->getid()))
		{
			$charStatus[] = "<font color='red'>nome bloqueado</font>";
		}
		
		if(count($charStatus) != 0)
		{
			$i = 0;
			foreach($charStatus as $status)
			{
				$i++;
				
				$statusString .= $status;
				
				if($i != count($charStatus))
					$statusString .= ", ";
			}
		}
		else
			$statusString = "nenhum";
			
			
		$charList .= "
		<tr>
			<td>
				<a style='float: left' href='?ref=character.view&name={$character->getName()}'>{$character->getName()}</a> <span class='tooglePlus'></span>
				<br />
				<div style='float: left; width: 100%; padding: 0px; margin: 0px; position: relative;'>
					<table cellspacing='0' cellpadding='0'>
						<tr>
							<td width='20%'><b>Status</b></td> <td>{$statusString}</td>
						</tr>
						<tr>	
							<td><b>A��es</b></td> <td>{$charOptions}</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>		
		";	
	}
}


$module .= "
<p>Seja bem vindo a sua conta, {$realname}. Voc� pode efetuar muitas opera��es como criar um personagem, mudar sua senha ou obter a conta premium atravez do menu minha conta ao lado esquerdo.";

if(is_array($newemail = $account->getEmailToChange()))
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Aten��o:</font> Existe uma mudan�a de email registrado em sua conta para o endere�o '.$newemail['email'].' que foi agendada para o dia '.$core->formatDate($newemail['date']).'. Voc� pode cancelar est� mudan�a a qualquer momento clicando <a href="?ref=account.cancelchangeemail">aqui</a>.</p>';
}

if($confirmed and $confirmed >= 1)
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Aten��o:</font> Caro jogador, um pedido efetuado por sua conta foi confirmado com sucesso! Voc� j� pode aceitar este pagamento ou visualizar maiores informa��es deste pedindo na categoria Conta Premium, na se��o Meus Pedidos. Tenha um bom jogo!</p>';
}

if(!$secretkey)
{
	$module .= '
	<p><font style="color: red; font-weight: bold;">Aten��o:</font> Caro jogador, sua conta ainda n�o possui uma chave secreta configurada, esta chave � necessaria em situa��es criticas para recuperar sua conta. Recomendamos que voc� gere a sua chave secreta agora mesmo clicando <a href="?ref=account.secretkey">aqui</a>.</p>';
}

if(isset($charDel))
{
	foreach($charDel as $name => $deletion)
	{
		$module .= '
		<p><font style="color: red; font-weight: bold;">Aten��o:</font> O seu personagem <b>'.$name.'</b> est� agendado para ser deletado do jogo no dia '.$core->formatDate($deletion).'. Para cancelar este opera��o clique <a href="?ref=character.undelete&name='.$name.'">aqui</a>.</p>';
	}
}		

$module .= "
<p>
	<table cellspacing='0' cellpadding='0' id='table'>
	
		<tr>
			<th colspan='2'>Informa��es da Conta</th>
		</tr>
		
		<tr>
			<td width='30%'><b>Endere�o de E-mail:</b></td><td>{$email}</td>
		</tr>
					
		<tr>
			<td><b>Conta premium:</b></td><td>{$premium}</td>
		</tr>
		
		<tr>
			<td><b>Warnings:</b></td><td>{$warns}</td>
		</tr>
		
		<tr>
			<td><b>Cria��o:</b></td><td>".$creation."</td>
		</tr>";
		
		if($bans->isBannished($account->getId()))
		{
			$ban = $bans->getBannishment($account->getId());
					
			if($ban['type'] == 3 OR $ban['type'] == 5)
			{
				$banstring .= "<font color='red'>";
				
				if($ban['type'] == 3)
				{
					$banstring .= "Banido por: <b>{$tools->getBanReason($ban['reason'])}</b><br>
							   	   Dura��o: At� {$core->formatDate($ban['expires'])}.";
				}
				elseif($ban['type'] == 5)	
				{
					$banstring .= "Deletado por: <b>{$tools->getBanReason($ban['reason'])}</b><br>
							   	   Dura��o: permanentemente.";		
				}			   	   				   	   
							   
				$banstring .= "</font>";
				
				$module .= "
				<tr>
					<td><b>Puni��o:</b></td> <td>{$banstring}</td>
				</tr>";			
			}
		}	
		
	$module .= "</table>
</p>

<p>";
			
if($account->getName() == $account->getId())
{
	$module .= "
	<a class='buttonstd' href='?ref=account.changepassword'>Mudar Senha</a> <a class='buttonstd' href='?ref=account.changeemail'>Mudar E-mail</a> <a class='buttonstd' href='?ref=account.setname'>Configurar Nome</a>";	
}
else
{			
	$module .= "
	<a class='buttonstd' href='?ref=account.changepassword'>Mudar Senha</a> <a class='buttonstd' href='?ref=account.changeemail'>Mudar E-mail</a>";
}

$module .= "
</p>

<p>
	<table cellspacing='0' cellpadding='0' id='table'>
	
		<tr>
			<th colspan='2'>Informa��es Personalizadas</th>
		</tr>
		
		<tr>
			<td width='30%'><b>Nome Real:</b></td><td>{$realname}</td>
		</tr>
					
		<tr>
			<td><b>Localiza��o:</b></td><td>{$location}</td>
		</tr>
		
		<tr>
			<td><b>Website:</b></td><td>{$url}</td>
		</tr>
		
	</table>
</p>

<p>
	<a class='buttonstd' href='?ref=account.changeinfos'>Mudar Informa��es</a>
</p>

<p>
	<table cellspacing='0' cellpadding='0' class='dropdowntable'>
	
		<tr>
			<th colspan='3'>Meus Personagens</th>
		</tr>
					
		$charList
		
	</table>
</p>

<p>
	<a class='buttonstd' href='?ref=character.create'>Criar Personagem</a> <a class='buttonstd' href='?ref=character.delete'>Deletar Personagem</a>
</p>
";	
				
?>