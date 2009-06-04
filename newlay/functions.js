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

function pingTest()
{	
	init();
	
	if(xmlhttp == null)
	{
		alert("Este navegador não suporta tecnologia Ajax.");
	}	
			
	for(i = 0; i < 3; i++)
	{
		var  _send = new Date();	
		xmlhttp.open("GET", "ajax/ping.php?value=" + _send.getTime(), false);	
		xmlhttp.send(null);
		
		if(xmlhttp.responseText == _send.getTime())
		{	
			//alert("lol");
			
			var _receive = new Date();
			var _elapsedTime = (_receive.getTime() - _send.getTime()) / 2;			
			
			avgPing += _elapsedTime;
		}						
	}	
	
	avgPing = Math.ceil(avgPing / 3);	
	
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
	
	if(xmlhttp.responseText != "log")
	{	
		alert("Falha ao enviar relatório de ping.");
	}	
}