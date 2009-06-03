$(document).ready(function() {
	//MENUS DE NAVEGA��O
	$("#left ul li div").next("ul[class!='always_viewable']").hide();
	$("#left ul li div").next("ul[class='viewable']").show();
	
	$("#left ul li div span").click(function() {	

		var session = $(this).parent().attr("name");
		setMenuCookie(session);
		
		var classe = $(this).hasClass("tooglePlus");
		
		if(classe)
		{
			$(this).removeClass("tooglePlus");
			$(this).addClass("toogleMinus");
		}
		else
		{
			$(this).removeClass("toogleMinus");
			$(this).addClass("tooglePlus");
		}			
		
		$(this).parent().next("ul[class!='always_viewable']").slideToggle();
	});
	
	
	//MENUS DENTRO DE TABELAS
	$("table[class='dropdowntable'] tr td").children("div[class!='always_viewable']").hide();
	$("table[class='dropdowntable'] tr td").children("div[class='viewable']").show();
	
	$("table[class='dropdowntable'] tr td span").click(function() {		
		var classe = $(this).hasClass("tooglePlus");
		
		if(classe)
		{
			$(this).removeClass("tooglePlus");
			$(this).addClass("toogleMinus");
		}
		else
		{
			$(this).removeClass("toogleMinus");
			$(this).addClass("tooglePlus");
		}	
		
		$(this).parent().children("div[class!='always_viewable']").slideToggle();
	});	
	
	//SELECTBOX COM ALTERA��O DINAMICA DE CONTEUDO
	$("fieldset div[class='autoaction']").nextAll("div[class!='viewable']").hide();	
	
	$("fieldset div[class='autoaction'] select").change(function() {
		var valueSelected = $("fieldset div[class='autoaction'] option:selected").val();

		$("fieldset div[class='autoaction']").nextAll('div[title!="' + valueSelected + '"]').hide();
		$("fieldset div[class='autoaction']").nextAll("div[title='" + valueSelected + "']").slideDown();
		
	});	
	
	//PINGTEST
	init();
	if(xmlhttp == null)
	{
		alert("Este navegador n�o suporta tecnologia Ajax.");
	}	
	
	var _send = new Date();	
	xmlhttp.open("GET", "ajax/ping.php?value=" + _send.getTime(), true);
	xmlhttp.send(null);	
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{	
			var _receive = new Date();
			var _elapsedTime = (_receive.getTime() - _send.getTime()) / 2;
			$("span[class=ping]").replaceWith(_elapsedTime + " ms");
		}		
	}	
	
	
});