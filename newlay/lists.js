$(document).ready(function() {	
	//MENUS DE NAVEGAÇÃO
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
	
	//FASTNEWS		
	$("table[class='fastnews'] tr td span").click(function() {		
		var classe = $(this).hasClass("tooglePlus");
		
		if(classe)
		{
			$(this).removeClass("tooglePlus");
			$(this).addClass("toogleMinus");
			
			var cssDisplay = $(this).parent().children("span[class='fullFastNew']").css("display");
			var cssVisibility = $(this).parent().children("span[class='fullFastNew']").css("visibility");
			
			if(cssDisplay == "none" && cssVisibility == "hidden")
			{
				$(this).parent().children("span[class='fullFastNew']").css({display:"inline", visibility:"visible"});
			}
			
			$(this).parent().children("span[class='littleFastNew']").hide();
			$(this).parent().children("span[class='fullFastNew']").show();
		}
		else
		{
			$(this).removeClass("toogleMinus");
			$(this).addClass("tooglePlus");
			
			$(this).parent().children("span[class='fullFastNew']").hide();
			$(this).parent().children("span[class='littleFastNew']").show();			
		}	
	});		
	
	//SELECTBOX COM ALTERAÇÃO DINAMICA DE CONTEUDO
	$("fieldset div[class='autoaction']").nextAll("div[class!='viewable']").hide();	
	
	$("fieldset div[class='autoaction'] select").change(function() {
		var valueSelected = $("fieldset div[class='autoaction'] option:selected").val();

		$("fieldset div[class='autoaction']").nextAll('div[title!="' + valueSelected + '"]').hide();
		$("fieldset div[class='autoaction']").nextAll("div[title='" + valueSelected + "']").slideDown();
		
	});		
});