<?
if($_COOKIE["bk"] and $_COOKIE["bk"] == "premium")
{	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	Core::sendMessageBox("Concluido!", "Parabens! Com sua enorme sorte conseguiste obter o premio! Sua conta acaba de receber 24 horas de Conta Premium pelos 2 cliques! At� o proximo dia de sorte! Clique <a href='http://www.darghos.com.br/?ref={$_SESSION["to_page"]}'>aqui</a> para acessar a pagina que estava acessando.");		
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
	<p>Certamente voc� n�o esperava estar vendo est� pagina, por�m certamente voc� n�o sabia que possuia uma tremenda <b>SORTE</b>!</p>
	<p>Voc� foi <b>escolhido</b>, entre dezenas de outros usuarios que est�o navegando logados em suas contas neste instante, para participar de um sistema que garante um <b>otimo premio</b> extra para voc� jogador do Darghos!</p>
	<p>Porem para voc� receber o premio voc� precisa fazer uma tarefa que envolve nada mais que <b>2 cliques</b>, isso mesmo, ap�nas dois cliques!!</p>
	
	<p><h3>Mas aonde eu devo clicar?</h3></p>
	
	<p>Aten��o, leia at� o final!</p>
	
	<p>Est� vendo os An�ncios que temos na parte superior do site e lateral esquerda? � bem simples, voc� deve ap�nas acessar qualquer um dos An�ncios que mais lhe interessar, ap�s carregar a pagina do An�nciante voc� deve ap�nas usar o bot�o de Voltar de seu Navegador para retornar a esta pagina e ter� recebido seu premio, facil n�o? <b>Est� esperando o que?! Sorte assim n�o acontece toda hora!</b></p>	
	';
	
	Core::sendMessageBox("Parabens!", $clickText, true, 100000); 
}
?>