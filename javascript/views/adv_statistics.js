function drawChart(){
	
	var post_data = {
		days: $("#chart_days_ago").val()
	}	
	
	$.post("?ref=adv.getstatisticsdata", post_data, function( server_data ){
		
		var chart = document.getElementById("chart_activePlayers").getContext("2d");
	
		var data = {
			labels : server_data["labels"],
			datasets : [
				{
					fillColor : "rgba(220,220,220,0.5)",
					strokeColor : "rgba(220,220,220,1)",
					pointColor : "rgba(220,220,220,1)",
					pointStrokeColor : "#fff",
					data : server_data["data"]
				}
			]
		}
			
		var activePlayersChart = new Chart(chart).Line(data, { scaleLineColor : "#363636", scaleGridLineColor : "#363636", scaleFontColor : "#f3df00", pointDotRadius : 1 });		
	}, "json");
}

$(document).ready(function() {	
	
	$("#chart_update").click(drawChart);	
	drawChart();
});