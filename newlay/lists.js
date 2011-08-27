$(document).ready(function() {	
	//MENUS DE NAVEGAï¿½ï¿½O
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
	
	$("table[class='dropdowntable-2'] tr td[class!='always_viewable']").hide();
	
	$("table[class='dropdowntable-2'] tr th span").click(function() {		
		var plus = $(this).hasClass("tooglePlus");
		var minus = $(this).hasClass("toogleMinus");
		
		if(plus)
		{
			$(this).removeClass("tooglePlus");
			$(this).addClass("toogleMinus");
			
			$("table[class='dropdowntable-2'] tr td").show();
		}
		else if(minus)
		{
			$(this).removeClass("toogleMinus");
			$(this).addClass("tooglePlus");
			
			$("table[class='dropdowntable-2'] tr td").hide();
		}	
	});	
	
	//FASTNEWS
	function fastNewsDropDown()
	{
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
	}
	$("table[class='fastnews'] span[class='tooglePlus']").click(fastNewsDropDown);
	$("table[class='fastnews'] span[class='littleFastNew']").click(fastNewsDropDown);
	
	//SELECTBOX COM ALTERAï¿½ï¿½O DINAMICA DE CONTEUDO
	$("div[class='autoaction']").nextAll("div[class!='viewable']").hide();	
	
	$("div[class='autoaction'] select").change(onChange);		
	$("div[class='autoaction'] input[type='radio']").change(onChange);
});

function onChange()
{
	var valueSelected = $(this).val();
	
	//alert(valueSelected);
	
	$("fieldset div[class='autoaction']").nextAll('div[title!="' + valueSelected + '"]').hide();
	$("fieldset div[class='autoaction']").nextAll("div[title='" + valueSelected + "']").slideDown();	
}
