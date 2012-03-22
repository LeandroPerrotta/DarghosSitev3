<?
use \Core\Configs;
$contribute = new \Framework\Contribute();

if(\Core\Strings::SQLInjection($_GET['id']) and $contribute->load($_GET['id'], "id, target, period, target_account, status, generated_in") and $contribute->get("target_account") == $_SESSION['login'][0] and $contribute->get("status") == 1)
{
	if($_POST)
	{
		$chkAccount = new \Framework\Account();
		$chkAccount->load($_SESSION['login'][0]);		
		
		$premium = \Framework\Contribute::getPremiumInfoByPeriod($contribute->get("period"), $contribute->get("generated_in"));
		
		$error = NULL;
		$callbackFunc = "\Framework\Contribute::{$premium["onAccept"]}";
		
		if(\Core\Strings::encrypt($_POST["account_password"]) != $_SESSION['login'][1])
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
		}
		elseif($_POST["accept_terms"] != "1")
		{
			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CONTR_TERMS);
		}
		elseif($premium["onAccept"] && !call_user_func_array($callbackFunc, array($contribute, &$error)))
		{
			//
		}
		else
		{
			$account = new \Framework\Account();
			$account->load($contribute->get("target_account"));
			
			$premdays = $contribute->get("period");			   
			$account->updatePremDays($premdays);
			
			$account->save();
			
			$contribute->set("status", 2);
			$contribute->set("lastupdate_in", time());
			$contribute->save();
		
			$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CONTR_ACTIVATED, Configs::Get(Configs::eConf()->WEBSITE_NAME));
		}
	}

	if($success)	
	{
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $success);
	}
	else
	{
		if($error)	
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
		}

	$contrato['premium'] = "Este é um documento informativo das clausulas e regras referente ao funcionamento, deveres e limitações entre outros referente aos jogadores contribuintes com o ".Configs::Get(Configs::eConf()->WEBSITE_NAME).". Leia abaixo todas clausulas e regras atentamente e, somente no caso de aceitar e seguir respeitando todos os termos, assinalar a caixa \"Eu li, e aceito as clausulas e regras de contribuições.\" e assim dar continuidade ao sistema de contribuição.

1. A estabilidade e mantimento do servidor no ar.
- A UltraxSoft e(ou) ".Configs::Get(Configs::eConf()->WEBSITE_NAME)." não tem a obrigação de manter o servidor sempre ligado, podendo o mesmo ser desligado a qualquer momento e por qualquer motivo, sem prévio aviso, devolução de quantias em dinheiro ou danos morais.

2. Conectividade.
- A UltraxSoft e(ou) ".Configs::Get(Configs::eConf()->WEBSITE_NAME)." não são responsáveis por qualquer problema de conectividade entre o jogador e o \"game-server\", tanto por parte do jogador, provedor de internet ou \"datacenter\" (empresa que hospeda o nosso game-server).

3. Seguir regras sem exceções.
- Caso você contribua com o serviço você estará sujeito a todas as regras do jogo, não possuindo nenhum direito ou vantagem extra dentro ou fora do jogo.

4. Vantagens da contribuição.
- Caso você contribua com o serviço, cabe a nós decidirmos sobre as vantagens recebidas, podendo as mesmas serem retiradas a qualquer momento sem prévio aviso nem devolução em dinheiro.

5. Direitos autorais.
- O ".Configs::Get(Configs::eConf()->WEBSITE_NAME)." não apóia a modificações de \"softwares\" sem autorização dos fabricantes ou desenvolvedores, e não cobre nenhum tipo de dano a seu computador que os programas podem causar.

6. Recompensas dentro do jogo.
- Perdas de itens, contas, ou características de personagens somente serão devolvidos se o problema foi de causa interna em nossos \"game-servers\" e em forma de ponto de restauração (efetuamos uma volta no tempo todo o servidor para um momento ou dia aonde a problemática não havia acontecido), e somente caso a UltraxSoft assim julgue necessário, perdas causadas por qualqueis outras causas (como problemas de conexão, desastres naturais, cuidados não eficientes com a sua conta (Hacking), entre outros) não são recompensados de maneira alguma.

7. Devoluções e troca de destino de contribuições.
- A devolução do dinheiro, ou mudança da conta na qual o contribuinte irá receber os benefécios, só é permitida enquanto o contribuinte não aceita a liberação do serviço. Caso algum dos recursos seja solicitado pelo contribuinte, a mudançaa de conta para contribuição tem um prazo de 5 a 30 dias após solicitada para ser concluída e a devolução do dinheiro em um prazo de 30 a 90 dias após solicitado. 

IMPORTANTE: Após aceitar o serviço, receber e começar a desfrutar dos beneficio em sua conta os recursos de mudança de conta e devolução do dinheiro não são mais possíveis em hipótese alguma.

A mudança deste documento pode ser efetuada sem aviso, ou prévio aviso, cabendo a você se manter atualizado as regras e ao contrato.";

$premium = \Framework\Contribute::getPremiumInfoByPeriod($contribute->get("period"), $contribute->get("generated_in"));

$character_name = "";

if(is_numeric($contribute->get("target")))
{
	$player = new \Framework\Player();
	$player->load($contribute->get("target"));
	$character_name = $player->getName();
}
else
	$character_name = $contribute->get("target");

$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
	
		<ul id="charactersview">
			<p>Pedido Numero: '.$contribute->get("id").'</p>
			<li><b>Personagem: </b> '.$character_name.'.</li>
			<li><b>Descrição: </b> '.$premium["text"].'.</li>
			
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
			<input name="accept_terms" type="checkbox" value="1" /> Eu aceito com as clausulas e termos de contrato de contribuições do '.Configs::Get(Configs::eConf()->WEBSITE_NAME).'.
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