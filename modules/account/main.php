<?
$account = new Account();
$account->load($_SESSION['login'][0]);
$secretkey = $account->getSecretKey();

$player_list = $account->getCharacterList();

$premium = ($account->getPremDays() > 0) ? $account->getPremDays()." dias restantes (expira em ".Core::formatDate($account->getPremEnd()).")" : "Você não possui dias de conta premium.";	
$warns = ($account->getWarnings() > 1) ? "Sua conta possui".$account->getWarnings()." warnings." : "Sua conta não possui warnings.";	
$email = ($account->getEmail()) ? $account->getEmail() : "<span style='color: red; font-weight: bold'>Nenhum e-mail registrado!</span>";	
$creation = ($account->getCreation() != 0) ? Core::formatDate($account->getCreation()) : "Indisponível";	
$realname = ($account->getRealName()) ?	$account->getRealName() : "<i>Sem Nome</i>";
$location = ($account->getLocation()) ?	$account->getLocation() : "<i>Sem Localidade</i>";
$url = ($account->getUrl()) ?	$account->getUrl() : "<i>Sem Endereço</i>";

$contribute = new Contribute();
$oders = $contribute->getOrdersListByAccount($_SESSION['login'][0]);

$bans = new Bans();

$invitesList = "";

$confirmed = 0;
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
		$character = new Character();
		$character->loadByName($player);
		
		unset($charStatus);
		unset($statusString);
		unset($charOptions);
		unset($npcOptions);
		
		$invite = $character->getInvite();
		
		if($invite)
		{
			list($guild_id, $invite_date) = $invite;
			
			$guild = new Guilds();
			$guild->Load($guild_id);
			
			$invitesList .= "
				<p><font style='font-weight: bold;'>Convite de Guild:</font> O seu personagem {$character->getName()} foi convidado em ".Core::formatDate($invite_date)." para se tornar membro da guilda {$guild->GetName()}! Clique <a href='?ref=guilds.invitereply&name={$character->getName()}'>aqui</a> para responder este convite. Obs: Esta mensagem desaparecerá automaticamente quando o convite for respondido.</p>
			";
		}
		
		$charStatus = array();
		$charOptions = "<a href='?ref=character.edit&name={$character->getName()}'>Editar</a>";
		
		if(SHOW_SHOPFEATURES == 1)
		{
			$charOptions .= " - <a href='?ref=itemshop.purchase&name={$character->getName()}'>Item Shop</a>";
			if(ENABLE_BUY_STAMINA) $charOptions .= " - <a href='?ref=character.stamina&name={$character->getName()}'>Regenerar Stamina</a>";
		}
		
		if(ENABLE_REBORN_SYSTEM == 1)
		{
			$npcOptions .= "<a href='?ref=character.reborn&name={$character->getName()}'>Baron Samedi</a>";
		}
		
		if($character->deletionStatus())
		{
			$charStatus[] = "<font color='red'>será deletado em: ".Core::formatDate($character->deletionStatus())."</font>";
			$charOptions .= " - <a href='?ref=character.undelete&name={$character->getName()}'>Cancelar Exclusão</a>";
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
							<td><b>Ações</b></td> <td>{$charOptions}</td>
						</tr>
						<tr>	
							<td><b>NPCs</b></td> <td>{$npcOptions}</td>
						</tr>						
					</table>
				</div>
			</td>
		</tr>		
		";	
	}
}


$module .= "
<p>Seja bem vindo a sua conta, {$realname}. Você pode efetuar muitas operações como criar um personagem, mudar sua senha ou obter a conta premium atravez do menu minha conta ao lado esquerdo.";

if(is_array($newemail = $account->getEmailToChange()))
{
	$module .= '
	<p><span id="notify">Atenção:</span> Existe uma mudança de email registrado em sua conta para o endereço '.$newemail['email'].' que foi agendada para o dia '.Core::formatDate($newemail['date']).'. Você pode cancelar esta mudança a qualquer momento clicando <a href="?ref=account.cancelchangeemail">aqui</a>.</p>';
}

if($confirmed and $confirmed >= 1)
{
	$module .= '
	<p><span id="notify">Atenção:</span> Caro jogador, um pedido efetuado por sua conta foi confirmado com sucesso! Você já pode aceitar este pagamento ou visualizar maiores informações deste pedindo na categoria Conta Premium, na seções Meus Pedidos. Tenha um bom jogo!</p>';
}

if($account->getEmail() && !$secretkey)
{
	$module .= '
	<p><span id="notify">Atenção:</span> Caro jogador, sua conta ainda não possui uma chave secreta configurada, esta chave é necessaria em situações criticas para recuperar sua conta. Recomendamos que você gere a sua chave secreta agora mesmo clicando <a href="?ref=account.secretkey">aqui</a>.</p>';
}

if(!$account->getEmail())
{
	$module .= '
	<script type="text/javascript">
	fogAlert("Você ainda não possui um e-mail registrado em sua conta. Você deve registrar um e-mail valido em sua conta para aumentar a segurança de sua conta. Note que enquanto você não o fizer, caso você perda seus dados de login, <b>você não conseguirá recuperar sua conta</b>.");
	</script>
	<p><span id="notify">Atenção:</span> Caro jogador, sua conta ainda não possui um e-mail registrado e por isto não está segura. Recomendamos que você registre um e-mail clicando <a href="?ref=account.validateEmail">aqui</a>. Ao registrar um e-mail em sua conta também será liberado alguns recursos como possibilidade de gerar uma chave secreta e obter uma conta premium.</p>';	
}

if($invitesList)
{
	$module .= $invitesList;
}

if(isset($charDel))
{
	foreach($charDel as $name => $deletion)
	{
		$module .= '
		<p><span id="notify">Atenção:</span> O seu personagem <b>'.$name.'</b> está agendado para ser deletado do jogo no dia '.Core::formatDate($deletion).'. Para cancelar este operação clique <a href="?ref=character.undelete&name='.$name.'">aqui</a>.</p>';
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
		</tr>";
		
		if($bans->isBannished($account->getId()))
		{
			$ban = $bans->getBannishment($account->getId());
					
			if($ban['type'] == 3 OR $ban['type'] == 5)
			{
				$banstring .= "<font color='red'>";
				
				if($ban['type'] == 3)
				{
					$banstring .= "Banido por: <b>".Tools::getBanReason($ban['reason'])."</b><br>
							   	   Duração: Até ".Core::formatDate($ban['expires']).".";
				}
				elseif($ban['type'] == 5)	
				{
					$banstring .= "Deletado por: <b>".Tools::getBanReason($ban['reason'])."</b><br>
							   	   Duração: permanentemente.";		
				}			   	   				   	   
							   
				$banstring .= "</font>";
				
				$module .= "
				<tr>
					<td><b>Punição:</b></td> <td>{$banstring}</td>
				</tr>";			
			}
		}	
		
	$module .= "</table>
</p>

<p>";
			
if($account->getName() == $account->getId())
{
	if($account->getPremDays() == 0)
	{
		$module .= "
		<a class='buttonstd' href='?ref=account.changepassword'>Mudar Senha</a> <a class='buttonstd' href='?ref=account.changeemail'>Mudar E-mail</a> <a class='buttonstd' href='?ref=account.setname'>Configurar Nome</a>";	
	}
	else
	{
		$module .= "
		<a class='buttonstd' href='?ref=account.changepassword'>Mudar Senha</a> <a class='buttonstd' href='?ref=account.changeemail'>Mudar E-mail</a> <a class='buttonstd' href='?ref=account.setname'>Configurar Nome</a> <!-- <a class='buttonstd' href='?ref=account.tutortest'>Tutor Test</a> -->";	
	}
}
else
{			
	$module .= "
	<a class='buttonstd' href='?ref=account.changepassword'>Mudar Senha</a> 
	";
	
	if(!$account->getEmail())
	{
		$module .= "
		<a class='buttonstd' href='?ref=account.validateEmail'>Registrar E-mail</a>
		";		
	}
	else
	{
		$module .= "
		<a class='buttonstd' href='?ref=account.changeemail'>Mudar E-mail</a>
		";		
	}
	
	$module .= "
	<a class='buttonstd' href='?ref=account.changename'>Renomear</a>
	";
	
	
}

$module .= "
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