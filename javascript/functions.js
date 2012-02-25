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

function requestSearchBoxPlayer(value)
{
	requestSearchBox("players.search", value);
}

function requestSearchBoxItemName(value)
{
	requestSearchBox("misc.searchitembyname", value, 3);
}

function requestSearchBox(url, value, minlength)
{	
	minlength = (!minlength) ? 1 : minlength;
	
	if(value.length < minlength){
		$("#search_suggestions").hide("slow");
	} else {
		$.post("index.php?ref=" + url, {value: value},
			function(info){
				if(info.length > 0){
					$("#search_suggestions_list").html(info);
					$("#search_suggestions").show("slow");
				}
				else{
					$("#search_suggestions").hide("slow");
				}
			}
			,"html"
		);
	}
}

function fillSearchBox(value)
{
	$("#search_value").val(value);
	$("#search_suggestions").hide();
}

function requestItemInfo(e, itemtype)
{	
	$.post("index.php?ref=misc.iteminfo", {itemtype: $(this).attr("id")},
		function(info){
			if($("#iteminfo").length == 0)
			{
				$("body").append("<div id='iteminfo' style='left: " + (e.pageX + 3) + "px; top: " + (e.pageY + 3) + "px'>" + info + "</div>");
			}
			else{
				$("#iteminfo").html(info);
				$("#iteminfo").show();
			}
		}
		,"html"
	);
}

function ereaseItemInfo()
{
	$("#iteminfo").hide();
}

function requestRebornPlayer(value)
{	
	$.post("index.php?ref=players.reborn", {inputValue: value},
		function(info){
		
			if(Number(info) == 1){
				return 1;
			}
			else{
				return 0;
			}
		}
	);
}

function showFogScreen()
{
	$("#fog-screen").css("width", $("#wrapper").width());
	$("#fog-screen").css("height", $("#wrapper").height());
	$("#fog-screen").show();
}

function fogLoading()
{
	showFogScreen();
	
	var _html = "<div id='fog-loading'></div>";
	$("#fog-screen").html(_html);
}

function onFogLoaded()
{
	$("#fog-screen").children("#fog-loading").remove();
	$("#fog-screen").hide();
}

function fogAlert(message, success)
{
	var msg = "Atenção:";
	var id = "fog-alert-title-error";
	var reflesh = "";
	
	if(success)
	{
		msg = "Sucesso!";
		id = "fog-alert-title-success";
		reflesh = true;
	}
	
	showFogScreen();
	
	var _html = "<div id='fog-alert'> <div id='" + id + "'><span></span><h3>" + msg + "</h3></div> <span id='fog-alert-content'>" + message + " </span> <p class='line'></p> <a class='buttonstd' style='float: right;' onclick='onFogAlertClick(" + reflesh + ")'> <span>Ok</span></a></div>";
	$("#fog-screen").html(_html);
}

function fogDialog(message, callback, args)
{
	showFogScreen();
	
	var _html = "<div id='fog-alert'> <div id='fog-alert-title-error'><span></span><h3>Atenção:</h3></div> <span id='fog-alert-content'>" + message + " </span> <p class='line'></p> <a class='buttonstd' style='float: right;' onclick='onFogConfirm(" + callback + ")'> <span>Sim</span></a> <a class='buttonstd' style='float: right;' onclick='onFogAlertClick()'> <span>Não</span></a></div>";
	$("#fog-screen").html(_html);
}

function onFogConfirm(callback)
{
	callback();
	onFogAlertClick();
}

function onFogAlertClick(reflesh)
{
	if(reflesh)
	{
		location.reload(true);
	}
	else{
		$("#fog-screen").children("#fog-alert").remove();
		$("#fog-screen").hide();
	}
}

function filterOnlyNumbers(element)
{
	if(!element.value.match(/^[\-\+]?[\d\,]*\.?[\d]*$/))
	{
		element.value = "";
	}
}

function countCharacters(limit)
{	
	var value = $("#user_post").val();
	//alert(value);
	var string = new String(value);
	
	$("#charactersLeft").empty();
	
	if(string.length < limit)
	{
		$("#charactersLeft").append("Restam " + (limit - string.length) + " caracteres.");
	}
	else
	{
		$("#charactersLeft").append("<span style='color: red;'>Texto muito grande, delete caracteres para que fique com " + limit + " ou menos.</span>");
	}
}