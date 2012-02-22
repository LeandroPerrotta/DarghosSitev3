var startTime = new Array();
var endTime = new Array();
var pings = new Array();
var minPing = 9999;
var pingTrie = 0;
var pongsTrie = 0;
var delta = 7;

function pong()
{
	pongsTrie++;
	
	var date = new Date();
	endTime[pongsTrie] = date.getTime();
	
	pings[pongsTrie] = (endTime[pongsTrie] - startTime[pongsTrie]) - delta;	
	
	if(pongsTrie < 4)
	{
		runPing();
	}	
	else
	{
		//alert(pings.length);
		
		pingStatistics();

		sendPingResult();
		drawPing();		
		
		createCookieSec("pingtest", minPing, 60 * 5);
	}		
}

function runPing()
{
	pingTrie++;
	var ip_address = "darghos.com.br";
	
    var proxy = new Ext.data.ScriptTagProxy({url:'http://'+ip_address+'/pong.js', timeout: 6000});
  
    var date = new Date();
    startTime[pingTrie] = date.getTime();
    
    proxy.load('','',function (){ }, this);  
}

function pingStatistics()
{
	var latencyTable = new Array();
	
	latencyTable[100] = 0;
	latencyTable[200] = 0;
	latencyTable[300] = 0;
	latencyTable[400] = 0;
	latencyTable[401] = 0;
	
	var tableAvg = 0;
	var tableModa;
	
	//alert(pings.length);
	
	for(var x in pings)
	{				
		if(pings[x] < 101)
			latencyTable[100]++;
		else if(pings[x] < 201)
			latencyTable[200]++;
		else if(pings[x] < 301)
			latencyTable[300]++;
		else if(pings[x] < 401)
			latencyTable[400]++;	
		else
			latencyTable[401]++;
	}	
	
	for(var x in latencyTable)
	{
		if(Math.max(latencyTable[x], tableAvg) > tableAvg)
			tableAvg = latencyTable[x];
	}	
	
	//alert(tableAvg);
	
	for(var x in latencyTable)
	{
		if(latencyTable[x] == tableAvg)
			tableModa = x;	
	}	
	
	for(var x in pings)
	{
		if(pings[x] < (tableModa + 1))
		{
			if(Math.min(pings[x], minPing) < minPing)
			{
				minPing = pings[x];
			}			
		}	
	}	
}

function drawPing()
{
	$(document).ready(function() {
		$("span[class=ping]").replaceWith(minPing + " ms");	
	});		
}

function sendPingResult()
{
	init();
	
	if(xmlhttp == null)
	{
		alert("Este navegador não suporta tecnologia Ajax.");
	}	
	
	xmlhttp.open("GET", "Ajax/ping.php?value=log&pingavg=" + minPing, false);	
	xmlhttp.send(null);	
	
	if(xmlhttp.responseText == "mysql_error")
	{	
		alert("Falha ao enviar relatório de ping: Mysql.");
	}	
	else if(xmlhttp.responseText != "log" && xmlhttp.responseText != "mysql_error")
	{
		alert("Falha ao enviar relatório de ping: Desconhecido.");
	}
}

var cookie = readCookie("pingtest");
var needTest = true;

if(cookie == null)
{
	runPing();
}	
else
{
	minPing = cookie;
	drawPing();
}



