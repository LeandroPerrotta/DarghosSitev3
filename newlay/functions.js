xmlhttp = false;
avgPing = 0;

function init()
{
	try
	{
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch (e)
	{
		try
		{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");	
		}
		catch (e)
		{
			xmlhttp = false;
		}		
	}
	
	if(!xmlhttp && typeof XMLHttpRequest != "undefined")
	{
        try
        {
        	xmlhttp = new XMLHttpRequest();
        }
        catch (e)
        {
        	xmlhttp = false;
        }
	}	
}
	
//COOKIES
function createCookie(name,value,days) 
{
	if (days) 
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else 
		var expires = "";
	
	document.cookie = name+"="+value+expires+"; path=/";
}

function createCookieSec(name,value,seconds) 
{
	if (seconds) 
	{
		var date = new Date();
		date.setTime(date.getTime()+(seconds*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else 
		var expires = "";
	
	document.cookie = name+"="+value+expires+"; path=/";
}
 
function readCookie(name) 
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) 
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		
		if (c.indexOf(nameEQ) == 0) 
			return c.substring(nameEQ.length,c.length);
	}
	return null;
}
 
function eraseCookie(name) 
{
	createCookie(name,"",-1);
}	

//SET COOKIE TO MENUS
function setMenuCookie(menu)
{
	var cookie = readCookie("menudropdown_" + menu);
	
	if(cookie == null)
	{
		createCookie("menudropdown_" + menu, "true", 7);
	}	
	else
	{
		if(cookie == "true")
			createCookie("menudropdown_" + menu, "false", 7);
		else
			createCookie("menudropdown_" + menu, "true", 7);
	}
}

function usleep(microseconds) {
    // http://kevin.vanzonneveld.net
    // +   original by: Brett Zamir (http://brettz9.blogspot.com)
    // %        note 1: For study purposes. Current implementation could lock up the user's browser.
    // %        note 1: Consider using setTimeout() instead.
    // %        note 2: Note that this function's argument, contrary to the PHP name, does not
    // %        note 2: start being significant until 1,000 microseconds (1 millisecond)
    // *     example 1: usleep(2000000); // delays for 2 seconds
    // *     returns 1: true
 
    var start = new Date().getTime();
    while (new Date() < (start + microseconds/1000)) {}
    return true;
}

function pingTest()
{	
	var cookie = readCookie("pingtest");
	var needTest = true;
	
	if(cookie == null)
	{
		needTest = true;
	}	
	else
	{
		avgPing = cookie;
		needTest = false;
	}

	if(needTest)
	{
		init();
		var pings = new Array();
		
		
		if(xmlhttp == null)
		{
			alert("Este navegador não suporta tecnologia Ajax.");
		}	
				
		for(var i = 0; i < 10; i++)
		{
			var  _send = new Date();	
			xmlhttp.open("GET", "ajax/ping.php?value=" + _send.getTime(), false);	
			xmlhttp.send(null);
			
			if(xmlhttp.responseText == _send.getTime())
			{				
				var _receive = new Date();
				var _elapsedTime = (_receive.getTime() - _send.getTime());		

				pings[i] = _elapsedTime;				
			}	

			//usleep(100000);	
		}	
		
		var latencyTable = new Array(8);
		
		latencyTable["50"] = 0;
		latencyTable["100"] = 0;
		latencyTable["150"] = 0;
		latencyTable["200"] = 0;
		latencyTable["250"] = 0;
		latencyTable["300"] = 0;
		latencyTable["400"] = 0;
		latencyTable["500"] = 0;
		
		for(var x in pings)
		{
			//alert(x + ": " + pings[x]);
		
			if(pings[x] <= 50)
			{
				latencyTable["50"]++;
			}
			else if(pings[x] >= 51 && pings[x] <= 100)
			{
				latencyTable["100"]++;
			}		
			else if(pings[x] >= 101 && pings[x] <= 150)
			{
				latencyTable["150"]++;
			}
			else if(pings[x] >= 151 && pings[x] <= 200)
			{
				latencyTable["200"]++;
			}
			else if(pings[x] >= 201 && pings[x] <= 250)
			{
				latencyTable["250"]++;
			}
			else if(pings[x] >= 251 && pings[x] <= 300)
			{
				latencyTable["300"]++;
			}
			else if(pings[x] >= 301 && pings[x] <= 400)
			{
				latencyTable["400"]++;
			}
			else if(pings[x] >= 401)
			{
				latencyTable["500"]++;
			}		
		}
		
		var max = 0;
		var moda;
		for(var x in latencyTable)
		{	
			if(Math.max(latencyTable[x], max) > max)
				max = latencyTable[x];
				
			//alert(x + ": " + latencyTable[x]);	
		}
		
		for(var x in latencyTable)
		{	
			if(latencyTable[x] == max)
				moda = x;
		}	
		
		avgPing = moda;	
		
		createCookieSec("pingtest", avgPing, 60 * 5);
	}	
	
	sendPingResult();
	
	return avgPing;
}

function sendPingResult()
{
	init();
	
	if(xmlhttp == null)
	{
		alert("Este navegador não suporta tecnologia Ajax.");
	}	
	
	xmlhttp.open("GET", "ajax/ping.php?value=log&pingavg=" + avgPing, false);	
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