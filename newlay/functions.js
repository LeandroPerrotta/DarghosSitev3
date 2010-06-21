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

function requestSearchBox(value)
{
	if(value.length == 0){
		$("#player_suggestions").hide("slow");
	} else {
		$.post("../ajax/checkplayer.php", {inputValue: value},
		function(info){
			if(info.length > 0){
				$("#player_suggestions_list").html(info);
				$("#player_suggestions").show("slow");
			}
			else{
				$("#player_suggestions").hide("slow");
			}
		}
		);
	}
}

function fillSearchBox(value)
{
	$("#player_name").val(value);
	$("#player_suggestions").hide();
}

