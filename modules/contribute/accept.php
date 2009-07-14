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
			$error = "A confirma��o da senha est� invalida.";
		}
		elseif($post[1] != "1")
		{
			$error = "Para aceitar uma contribui��o � necessario estar de acordo com todas clausulas e termos de nosso contrato de servi�o.";
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
			<p>A sua contribui��o foi ativada com sucesso!</p>
			<p>Agora sua conta j� possui status de Contra Premium, o que lhe permitir� muitas novas possibilidades dentro do ".CONFIG_SITENAME."!</p>
			<p>Agrade�emos a preferencia e obrigado por contribuir conosco!</p>
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

	$contrato['premium'] = "Este � um documento informativo das clausulas e regras referente ao funcionamento, deveres e limita��es entre outros referente aos jogadores contribuintes com o ".CONFIG_SITENAME.". Leia abaixo todas clausulas e regras atentamente e, somente no caso de aceitar e seguir respeitando todos os termos, assinalar a caixa \"Eu li, e aceito as clausulas e regras de contribui��es.\" e assim dar continuidade ao sistema de contribui��o.

1. A estabilidade e mantimento do servidor no ar.
� A UltraxSoft e(ou) ".CONFIG_SITENAME." n�o tem a obriga��o de manter o servidor sempre ligado, podendo o mesmo ser desligado a qualquer momento e por qualquer motivo, sem pr�vio aviso, devolu��o de quantias em dinheiro ou danos morais.

2. Conectividade.
� A UltraxSoft e(ou) ".CONFIG_SITENAME." n�o s�o respons�veis por qualquer problema de conectividade entre o jogador e o \"game-server\", tanto por parte do jogador, provedor de internet ou \"datacenter\" (empresa que hospeda o nosso game-server).

3. Seguir regras sem exce��es.
� Caso voc� contribua com o servi�o voc� estar� sujeito a todas as regras do jogo, n�o possuindo nenhum direito ou vantagem extra dentro ou fora do jogo.

4. Vantagens da contribui��o.
� Caso voc� contribua com o servi�o, cabe a n�s decidirmos sobre as vantagens recebidas, podendo as mesmas serem retiradas a qualquer momento sem pr�vio aviso nem devolu��o em dinheiro.

5. Direitos autorais.
� O ".CONFIG_SITENAME." n�o ap�ia a modifica��o de \"softwares\" sem autoriza��o dos fabricantes, e n�o cobre nenhum tipo de dano a seu computador que os programas podem causar.

6. Recompensas dentro do jogo.
� Perdas de itens, contas, ou caracter�sticas de personagens somente ser�o devolvidos se o problema foi de causa interna em nossos \"game-servers\" e em forma de ponto de restaura��o (a��o que efetua uma volta no tempo todo o servidor para um momento ou dia aonde a problem�tica n�o havia acontecido), e somente caso a UltraxSoft assim julgue necess�rio, perdas causadas por qualqueis outras causas (como problemas de conex�o, desastres naturais, cuidados n�o eficientes com a sua conta (Hacking), entre outros) n�o s�o recompensados de maneira alguma.

7. Devolu��es e troca de destino de contribui��es.
� A devolu��o do dinheiro, ou mudan�a da conta na qual o contribuinte ir� receber os benef�cios, s� � ocorrida enquanto o contribuinte n�o aceita a libera��o do servi�o. Caso algum dos recursos seja solicitado pelo contribuinte, a mudan�a de conta para contribui��o tem um prazo de 5 a 30 dias ap�s solicitada para ser conclu�da e a devolu��o do dinheiro em um prazo de 30 a 90 dias ap�s solicitado. 

IMPORTANTE: Ap�s aceitar o servi�o, receber e come�ar a desfrutar dos beneficio em sua conta os recursos de mudan�a de conta e devolu��o do dinheiro n�o s�o mais poss�veis em hip�tese alguma.

A mudan�a deste documento pode ser efetuada sem aviso, ou pr�vio aviso, cabendo a voc� se manter atualizado �s regras e ao contrato.";

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
	
		<ul id="charactersview">
			<p>Pedido Numero: '.$contribute->get("id").'</p>
			<li><b>Personagem: </b> '.$contribute->get("target").'.</li>
			<li><b>Periodo: </b> Contribui��o de '.$contribute->get("period").' dias de Conta Premium.</li>
			
		</ul>	
		
		<p>
			<label for="account_password">Senha</label><br />
			<input name="account_password" size="40" type="password" value="" />
		</p>		
		
		<p>
			<label for="accept_terms">Clausulas e Termos de aceita��o de Contribui��es</label><br />
			<textarea rows="10" wrap="physical" cols="55" readonly="true">'.$contrato['premium'].'</textarea>
		</p>	

		<p>
			<input name="accept_terms" type="checkbox" value="1" /> Eu aceito com as clausulas e termos de contrato de contribui��es do '.CONFIG_SITENAME.'.
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