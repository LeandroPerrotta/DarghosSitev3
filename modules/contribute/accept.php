<?
$contribute = $core->loadClass("Contribute");
$post = $core->extractPost();

if($strings->SQLInjection($_GET['id']) and $contribute->load($_GET['id'], "id, target, period, target_account, status") and $contribute->get("target_account") == $_SESSION['login'][0] and $contribute->get("status") == 1)
{
	if($post)
	{
		$chkAccount = $core->loadClass("Account");
		$chkAccount->load($_SESSION['login'][0]);		
		
		if($strings->encrypt($post[0]) != $_SESSION['login'][1])
		{
			$error = "A confirmação da senha está invalida.";
		}
		elseif($post[1] != "1")
		{
			$error = "Para aceitar uma contribuição é necessario estar de acordo com todas clausulas e termos de nosso contrato de serviço.";
		}
		else
		{
			$account = $core->loadClass("Account");
			$account->load($contribute->get("target_account"));
			
			$account->updatePremDays($contribute->get("period"));
			
			$account->save();
			
			$contribute->set("status", 2);
			$contribute->set("lastupdate_in", time());
			$contribute->save();
		
			$success = "
			<p>Caro jogador,</p>
			<p>A sua contribuição foi ativada com sucesso!</p>
			<p>Agora sua conta já possui status de Contra Premium, o que lhe permitirá muitas novas possibilidades dentro do ".CONFIG_SITENAME."!</p>
			<p>Agradeçemos a preferencia e obrigado por contribuir conosco!</p>
			<p>Tenha um bom jogo!<br>Equipe UltraxSoft.</p>
			";
		}
	}

	if($success)	
	{
		$core->sendMessageBox("Sucesso!", $success);
	}
	else
	{
		if($error)	
		{
			$core->sendMessageBox("Erro!", $error);
		}

	$contrato['premium'] = "Este é um documento informativo das clausulas e regras referente ao funcionamento, deveres e limitações entre outros referente aos jogadores contribuintes com o ".CONFIG_SITENAME.". Leia abaixo todas clausulas e regras atentamente e, somente no caso de aceitar e seguir respeitando todos os termos, assinalar a caixa \"Eu li, e aceito as clausulas e regras de contribuições.\" e assim dar continuidade ao sistema de contribuição.

1. A estabilidade e mantimento do servidor no ar.
• A UltraxSoft e(ou) ".CONFIG_SITENAME." não tem a obrigação de manter o servidor sempre ligado, podendo o mesmo ser desligado a qualquer momento e por qualquer motivo, sem prévio aviso, devolução de quantias em dinheiro ou danos morais.

2. Conectividade.
• A UltraxSoft e(ou) ".CONFIG_SITENAME." não são responsáveis por qualquer problema de conectividade entre o jogador e o \"game-server\", tanto por parte do jogador, provedor de internet ou \"datacenter\" (empresa que hospeda o nosso game-server).

3. Seguir regras sem exceções.
• Caso você contribua com o serviço você estará sujeito a todas as regras do jogo, não possuindo nenhum direito ou vantagem extra dentro ou fora do jogo.

4. Vantagens da contribuição.
• Caso você contribua com o serviço, cabe a nós decidirmos sobre as vantagens recebidas, podendo as mesmas serem retiradas a qualquer momento sem prévio aviso nem devolução em dinheiro.

5. Direitos autorais.
• O ".CONFIG_SITENAME." não apóia a modificação de \"softwares\" sem autorização dos fabricantes, e não cobre nenhum tipo de dano a seu computador que os programas podem causar.

6. Recompensas dentro do jogo.
• Perdas de itens, contas, ou características de personagens somente serão devolvidos se o problema foi de causa interna em nossos \"game-servers\" e em forma de ponto de restauração (ação que efetua uma volta no tempo todo o servidor para um momento ou dia aonde a problemática não havia acontecido), e somente caso a UltraxSoft assim julgue necessário, perdas causadas por qualqueis outras causas (como problemas de conexão, desastres naturais, cuidados não eficientes com a sua conta (Hacking), entre outros) não são recompensados de maneira alguma.

7. Devoluções e troca de destino de contribuições.
• A devolução do dinheiro, ou mudança da conta na qual o contribuinte irá receber os benefícios, só é ocorrida enquanto o contribuinte não aceita a liberação do serviço. Caso algum dos recursos seja solicitado pelo contribuinte, a mudança de conta para contribuição tem um prazo de 5 a 30 dias após solicitada para ser concluída e a devolução do dinheiro em um prazo de 30 a 90 dias após solicitado. 

IMPORTANTE: Após aceitar o serviço, receber e começar a desfrutar dos beneficio em sua conta os recursos de mudança de conta e devolução do dinheiro não são mais possíveis em hipótese alguma.

A mudança deste documento pode ser efetuada sem aviso, ou prévio aviso, cabendo a você se manter atualizado às regras e ao contrato.";

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
	
		<ul id="charactersview">
			<p>Pedido Numero: '.$contribute->get("id").'</p>
			<li><b>Personagem: </b> '.$contribute->get("target").'.</li>
			<li><b>Periodo: </b> Contribuição de '.$contribute->get("period").' dias de Conta Premium.</li>
			
		</ul>	
		
		<p>
			<label for="account_password">Senha</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>		
		
		<p>
			<label for="accept_terms">Clausulas e Termos de aceitação de Contribuições</label><br />
			<textarea rows="10" wrap="physical" cols="55" readonly="true">'.$contrato['premium'].'</textarea>
		</p>	

		<p>
			<input name="accept_terms" type="checkbox" value="1" /> Eu aceito com as clausulas e termos de contrato de contribuições do '.CONFIG_SITENAME.'.
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Aceitar" />
		</p>
	</fieldset>
</form>	
	';
	}
}
?>