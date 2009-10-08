<?
$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0]);

if($account->canSeeAdPage())
{

	if($_COOKIE["bk"] and $_COOKIE["bk"] == "premium")
	{
		$core->sendMessageBox("Concluido!", "Parabens, foram creditados em sua conta 24 horas gratuitamente! Re-faça o login no jogo para ela entrar em Vigor!");		
		$account->activePremiumPrize();
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
				
		<img src="'.$layoutDir.'images/premprize.png" border="0"/>	
		';	
	}
}
else
{
	$core->sendMessageBox("Erro!", "Pagina não encontrada");
}
?>