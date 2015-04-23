$(document).ready(function() {	
	
	$("#edit_item").click(function(){
		
		var shop_id = $("input[name='selected_item']:checked").val();
		
		if(!shop_id){
			alert("Primeiro selecione um item.");
			return;
		}
		
		location.href = "?ref=store.add&edit=" + shop_id;
	});
	
	$("#delete_item").click(function(){
		
		var shop_id = $("input[name='selected_item']:checked").val();
		
		if(!shop_id){
			alert("Primeiro selecione um item.");
			return;
		}
		
		fogDialog("Você tem certeza que deseja excluir este item?", "deleteItemCallback", shop_id);
		return true;
	});	
});

function deleteItemCallback(shop_id){
	sendPost("?ref=store.add&delete=true", { item_id: shop_id });
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