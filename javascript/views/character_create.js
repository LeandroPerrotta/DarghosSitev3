$(document).ready(function() {	
	
	$("input").change(onChange);
});

function onChange()
{
	if($(this).attr("name") != "")
	{		
		$("." + $(this).attr("name")).children("div[class='" + $(this).val() + "']").slideDown();
		$("." + $(this).attr("name")).children("div[class!='" + $(this).val() + "']").hide();
	}	
}