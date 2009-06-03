var xmlhttp = false;
	
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
		xmlhttp = new XMLHttpRequest();
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

function sendPing()
{
	var pings = new Array(), time = new Date();	
	
	pings[0] = 0;
	pings[1] = 0;
	pings[2] = 0;
	
	var pingavg = 0;
	var x;
	
	for(x in pings)
	{			
		var ping;
		
		init();
		if(xmlhttp == null)
		{
			alert("Este navegador não suporta tecnologia Ajax.");
		}	
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readystate == 4)
			{	
				//ping = xmlhttp.responseText;		
				//alert(xmlhttp.responseText);	
				//document.getElementById("ping").innerHTML = xmlhttp.responseText;
				$("span").empty();
				$("span[class=ping]").replaceWith(xmlhttp.responseText);
			}	
		}
		
		xmlhttp.open("GET", "ajax.php?script=ping&value=" + time.getTime(), true);
		xmlhttp.send(null);		
		
		pingavg = pingavg + ping;
	}
	
	pingavg = pingavg / 3;
	//document.write("Ping médio: " + pingavg);
}