<?
class Tools
{
	/*function getVocationNameById($vocation_id)
	{
		switch($vocatio_id)
		{
			case 0:
				$vocation_name = "Sem Voca��o";
		}
	}*/
	
	function getBanReason($reason_id)
	{
		switch($reason_id)
		{
			case 0:	return "Nome ofencivo"; break;	
			case 1:	return "Nome com formato inv�lido"; break;	
			case 2:	return "Nome instuavel"; break;	
			case 3:	return "Nome que incentiva a quebra de regras"; break;	
			case 4:	return "Declara��o ofenciva"; break;	
			case 5:	return "Spamea��o"; break;	
			case 6:	return "Propaganda ilegal"; break;	
			case 7:	return "Declara��o publica fora de topico"; break;	
			case 8:	return "Declara��o fora do idioma"; break;	
			case 9:	return "Incentivando a quebra de regras"; break;	
			case 10: return "Abuso de falhas ou problemas do jogo"; break;	
			case 11: return "Abuso de fraquezas do jogo"; break;	
			case 12: return "Uso de software n�o permitido para jogar"; break;	
			case 13: return "Pratica de hacking"; break;	
			case 14: return "Uso de multi-cliente"; break;	
			case 15: return "Partilha��o ou venda de conta"; break;	
			case 16: return "Amea�ando um Gamemaster"; break;	
			case 17: return "Falsa influencia sobre as regras"; break;	
			case 18: return "Relat�rio falso a um Gamemaster"; break;	
			case 19: return "Atitude inaceitavel"; break;	
			case 20: return "Excesso injustificado de assassinatos"; break;	
			case 21: return "Pagamento inv�lido"; break;	
			case 22: return "Revela��o de conteudo n�o permitido"; break;	
			default: "Desconhecido"; break;
		}
		
		return true;
	}
}
?>