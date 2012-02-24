$(document).ready(function() {	
	$("#bid_value").blur(checkBid);
	$("#submit").click(onSubmit);
	$("#delete").click(onDelete);
});

var params;

function checkBid()
{
	var value = $("#bid_value").val();
	var minValue = $("#bid_value").attr("minvalue");
	
	if(Number(value) >= Number(minValue))
		return true;
	
	$("#bid_value").val(minValue);	
	return false;
}

function onSubmit()
{
	var player = $("#bid_player").val();
	var bid = $("#bid_value").val();
	
	if(player == "")
	{
		fogAlert("Por favor, selecione o personagem de sua conta que será registrado como autor do lançe e caso você venha a vencer o leilão irá receber os items no jogo.");
		return false;
	}
	
	fogDialog("Você tem certeza que deseja efetuar este lançe de " + bid + " dias de conta premium de seu personagem " + player + "?", submit);
	return true;
}

function onDelete()
{	
	fogDialog("Você tem certeza que deseja excluir este leilão?", deleteCallback);
	return true;
}

function onDeleteItem(auction_id, pos)
{	
	params = {auction_id: auction_id, pos: pos};
	fogDialog("Você tem certeza que deseja excluir este item?", deleteItemCallback);
	return true;
}

function deleteCallback()
{
	var auction = $("#bid_auction").val();
	var data = {
			id: auction
	};	
	
	sendPost("index.php?ref=auctions.delete", data);
}

function deleteItemCallback()
{	
	sendPost("index.php?ref=auctions.deleteitem", params);
}

function submit()
{
	var player = $("#bid_player").val();
	var bid = $("#bid_value").val();
	var auction = $("#bid_auction").val();
	
	var data = {
		bid_player: player
		,bid_value: bid
		,bid_auction: auction
	};
	
	sendPost("index.php?ref=auctions.makebid", data);
}

function sendPost(url, data, handler)
{	
	$.post(url, data, 
		function(info){			
			onFogLoaded();
			
			if(info.msg)
			{			
				var success = !info.error;
				fogAlert(info.msg, success);
			}
			else
			{
				fogAlert("Um erro interno ocorreu, um log foi gerado ao administrador. Tente novamente mais tarde.");
			}
		}, "json"
	);	
}