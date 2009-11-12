<?
if($_COOKIE["bk"] and $_COOKIE["bk"] == "premium")
{	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	$core->sendMessageBox("Concluido!", "Parabens! Com sua enorme sorte conseguiste obter o premio! Sua conta acaba de receber 24 horas de Conta Premium pelos 2 cliques! Até o proximo dia de sorte! Clique <a href='http://www.darghos.com.br/?ref={$_SESSION["to_page"]}'>aqui</a> para acessar a pagina que estava acessando.");		
	$account->activePremiumPrize();
	
	unset($_SESSION["to_page"]);
}
else
{
	$module .= '
	<script type="text/javascript">
		var b = false;
		
		if (typeof window.attachEvent != "undefined") {
			window.attachEvent("onbeforeunload", end);
		}
		else if (typeof window.addEventListener != "undefined") {
			window.addEventListener("beforeunload", end, false);
		}
		
		var el = document.getElementsByTagName("iframe");
		for (var i=0; i<el.length; i++) {
		
			el[i].onmouseover = mb;
			el[i].onmouseout = mfb;
		}	
		
		function mb()
		{
			b = true;
		}
		
		function mfb()
		{
			b = false;
		}	
		
		function end()
		{
			if(b)
			{
				createCookie("bk", "premium", 60 * 5);
			}
		}
	</script>
		
	';	
	
	$clickText = '
	<p>Certamente você não esperava estar vendo está pagina, porém certamente você não sabia que possuia uma tremenda <b>SORTE</b>!</p>
	<p>Você foi <b>escolhido</b>, entre dezenas de outros usuarios que estão navegando logados em suas contas neste instante, para participar de um sistema que garante um <b>otimo premio</b> extra para você jogador do Darghos!</p>
	<p>Porem para você receber o premio você precisa fazer uma tarefa que envolve nada mais que <b>2 cliques</b>, isso mesmo, apénas dois cliques!!</p>
	
	<p><h3>Mas aonde eu devo clicar?</h3></p>
	
	<p>Atenção, leia até o final!</p>
	
	<p>Está vendo os Anúncios que temos na parte superior do site e lateral esquerda? É bem simples, você deve apénas acessar qualquer um dos Anúncios que mais lhe interessar, após carregar a pagina do Anúnciante você deve apénas usar o botão de Voltar de seu Navegador para retornar a esta pagina e terá recebido seu premio, facil não? <b>Está esperando o que?! Sorte assim não acontece toda hora!</b></p>	
	';
	
	$core->sendMessageBox("Parabens!", $clickText, true, 100000); 
}
?>