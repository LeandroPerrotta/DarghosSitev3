var step = 1;

var RESPONSE_FIELD_VERIFY = 1;
var RESPONSE_NEXT_STEP = 2;

$(document).ready(function() {	
	$("#btNext").click(function() {
		
		if(step == 1)
		{
			createAccount();
		}
		else if(step == 2)
		{
			var needRegister = $("input[name='email_check']:checked").val();
			
			if(needRegister == 1)
				registerEmail();
			else
				nextStep();
		}
		else if(step == 3)
		{
			if(createCharacter())
			{
				$("#btNext").hide();
			}
		}
	});
	
	$("input").click(onChange);
	$("select").change(onChange);
	
	$("input[name='account_name']").blur(checkAccountName);
	$("input[name='account_confirm_password']").blur(checkPassword);
	$("input[name='account_email']").blur(checkEmail);
	$("input[name='character_name']").blur(checkCharacterName);
});

function checkCharacterName()
{
	var characterName = $("input[name='character_name']").val();
	notifyLoading($("input[name='character_name']"));
	sendPost({action: "player_checkName", character_name: characterName}, $("input[name='character_name']"));	
}

function checkEmail()
{
	var accountEmail = $("input[name='account_email']").val();
	notifyLoading($("input[name='account_email']"));
	sendPost({action: "account_checkEmail", account_email: accountEmail}, $("input[name='account_email']"));
}

function checkAccountName()
{
	var accountName = $("input[name='account_name']").val();
	notifyLoading($("input[name='account_name']"));
	sendPost({action: "account_checkName", account_name: accountName}, $("input[name='account_name']"));
}

function checkPassword()
{
	var password = $("input[name='account_password']").val();
	var confirm = $("input[name='account_confirm_password']").val();
	notifyLoading($("input[name='account_confirm_password']"));
	sendPost({action: "account_checkPassword", account_password: password, account_confirm_password: confirm}, $("input[name='account_confirm_password']"));
}

function createAccount()
{
	var accountName = $("input[name='account_name']");
	var password = $("input[name='account_password']");
	var confirm = $("input[name='account_confirm_password']");
	
	if(accountName.next("#notify-correct").length == 1 && confirm.next("#notify-correct").length == 1)
	{
		$("#account_name").text(accountName.val());
		fogLoading();
		sendPost({action: "account_create", account_name: accountName.val(), account_password: password.val(), account_confirm_password: confirm.val()}, $("input[name='account_confirm_password']"));
		return true;
	}
	
	fogAlert("Por favor, para criar a sua conta preencha todos os campos corretamente antes de prosseguir.");
	return false;
}

function registerEmail()
{
	var accountEmail = $("input[name='account_email']");
	if(accountEmail.next("#notify-correct").length == 1)
	{	
		$("#account_email").text(accountEmail.val());
		fogLoading();
		sendPost({action: "account_registerEmail", account_email: accountEmail.val()}, $("input[name='account_email']"));
		return true;		
	}

	fogAlert("Por favor, para registrar o e-mail em sua conta preencha todos os campos corretamente antes de prosseguir.");
	return false;	
}

function createCharacter()
{
	var characterName = $("input[name='character_name']");
	var characterWorld = $("select[name='character_world']");
	var characterGenre = $("select[name='character_genre']");
	var characterVocation = $("select[name='character_vocation']");	
	var characterTown = $("select[name='character_town']");	
	
	if(characterVocation.val() == "")
	{	
		fogAlert("Por favor, para criar seu primeiro personagem escolha uma vocação de sua preferencia antes de prosseguir.");
		return false;
	}
	
	if(characterWorld.val() == "")
	{	
		fogAlert("Por favor, para criar seu primeiro personagem escolha um mundo de sua preferencia antes de prosseguir.");
		return false;
	}	
	
	if(characterWorld.val() == 1 && characterTown.val() == "")
	{
		fogAlert("Por favor, para criar seu primeiro personagem escolha uma cidade de sua preferencia antes de prosseguir.");
		return false;		
	}
	
	if(characterName.next("#notify-correct").length == 1)
	{
		$("#character_name").text(characterName.val());
		fogLoading();
		sendPost({action: "player_create", character_name: characterName.val(), character_world: characterWorld.val(), character_town: characterTown.val(), character_genre: characterGenre.val(), character_vocation: characterVocation.val()}, $("input[name='account_email']"));
		return true;			
	}
	
	fogAlert("Por favor, para seu primeiro personagem preencha todos os campos corretamente antes de prosseguir.");
	return false;		
}

function sendPost(data, handler)
{	
	$.post("ajax.php", data, 
		function(info){
				
			if(info.response == RESPONSE_FIELD_VERIFY)
			{
				if(info.error)
				{
					notify(info.text, handler);
				}
				else
				{	
					notifyCorrect(handler);	
				}
			}
			else if(info.response == RESPONSE_NEXT_STEP)
			{
				onFogLoaded();
				
				if(!info.error)
				{			
					nextStep();
				}
				else
				{
					fogAlert("Um erro interno ocorreu, um log foi gerado ao administrador. Tente novamente mais tarde.");
				}
			}
		}, "json"
	);	
}

function nextStep()
{
	$("#step_by_step").children("div[class='" + step + "']").hide();					
	step++;			
	$("#step_by_step").children("div[class='" + step + "']").slideDown();			
}

function onChange()
{
	if($(this).attr("name") != "")
	{
		if($(this).attr("name") == "email_check")
		{			
			if($(this).val() == 1)
				$("p.email_check").show();
			else
				$("p.email_check").hide();
		}
		
		$("." + $(this).attr("name")).children("div[class='" + $(this).val() + "']").slideDown();
		$("." + $(this).attr("name")).children("div[class!='" + $(this).val() + "']").hide();
	}	
}

function removeNotify(handler)
{
	if(handler.next("#notify").length != 0)	
		handler.next("#notify").remove();	
	
	if(handler.next("#notify-correct").length != 0)	
		handler.next("#notify-correct").remove();	
	
	if(handler.next("#notify-loading").length != 0)	
		handler.next("#notify-loading").remove();	
}

function notify(string, handler)
{
	removeNotify(handler);
	handler.after("<span id='notify'>" + string + "</span>");
}

function notifyCorrect(handler)
{
	removeNotify(handler);	
	handler.after("<span id='notify-correct'></span>");
}

function notifyLoading(handler)
{
	removeNotify(handler);	
	handler.after("<span id='notify-loading'></span>");	
}