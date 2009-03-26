$(document).ready(function() {
	$("#left ul li strong").next("ul[class!='always_viewable']").hide();
	
	$("#left ul li strong").click(function() {
		$(this).next("ul[class!='always_viewable']").slideToggle();
	});
});