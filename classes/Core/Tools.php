<?
namespace Core;
class Tools
{		
	static function hasFlag($value, $flag)
	{
		return ($value & $flag);
	}
	
	function getBanReason($reason_id)
	{
		switch($reason_id)
		{
			case 0:	return "Nome ofencivo"; break;	
			case 1:	return "Nome com formato inválido"; break;	
			case 2:	return "Nome instuavel"; break;	
			case 3:	return "Nome que incentiva a quebra de regras"; break;	
			case 4:	return "Declaração ofenciva"; break;	
			case 5:	return "Spemando"; break;	
			case 6:	return "Propaganda ilegal"; break;	
			case 7:	return "Declaração publica fora de topico"; break;	
			case 8:	return "Declaração fora do idioma"; break;	
			case 9:	return "Incentivando a quebra de regras"; break;	
			case 10: return "Abuso de falhas ou problemas do jogo"; break;	
			case 11: return "Abuso de fraquezas do jogo"; break;	
			case 12: return "Uso de software não permitido para jogar"; break;	
			case 13: return "Pratica de hacking"; break;	
			case 14: return "Uso de multi-cliente"; break;	
			case 15: return "Compartilhando ou venda de conta"; break;	
			case 16: return "Ameaçando um Gamemaster"; break;	
			case 17: return "Falsa influencia sobre as regras"; break;	
			case 18: return "Denúncia falso a um Gamemaster"; break;	
			case 19: return "Atitude inaceitavel"; break;	
			case 20: return "Excesso injustificado de assassinatos"; break;	
			case 21: return "Pagamento inválido"; break;	
			case 22: return "Revelação de contudo não permitido"; break;	
			default: "Desconhecido"; break;
		}
		
		return true;
	}
	
	static function GetTicketTypeName($ticketType)
	{
		$ticketName = "";
		
		switch($ticketType)
		{
			case 1:
				$ticketName = "Website";
				break;
				
			case 2:
				$ticketName = "Jogo";
				break;

			case 3:
				$ticketName = "Premium";
				break;	
		}
		
		return $ticketName;
	}
	
	static function getPercentOf($value, $total , $precision = 0)
	{
		return round(($value / $total) * 100, $precision);
	}
	
	static function isSorcerer($vocation)
	{
		$sorcerer = 1;
		if($vocation == $sorcerer or $vocation == $sorcerer + 4 or $vocation == $sorcerer + 8)
			return true;
			
		return false;
	}
	
	static function isDruid($vocation)
	{
		$sorcerer = 2;
		if($vocation == $sorcerer or $vocation == $sorcerer + 4 or $vocation == $sorcerer + 8)
			return true;
			
		return false;
	}
	
	static function isPaladin($vocation)
	{
		$sorcerer = 3;
		if($vocation == $sorcerer or $vocation == $sorcerer + 4 or $vocation == $sorcerer + 8)
			return true;
			
		return false;
	}
	
	static function isKnight($vocation)
	{
		$sorcerer = 4;
		if($vocation == $sorcerer or $vocation == $sorcerer + 4 or $vocation == $sorcerer + 8)
			return true;
			
		return false;
	}
	
	static function ip_long2string($long)
	{
		$string = long2ip($long);
		$exp = explode(".", $string);
		return implode(".", array_reverse($exp));
	}
	
	static function getSiteTable($tablename)
	{
		return Configs::Get(Configs::eConf()->SQL_WEBSITE_TABLES_PREFIX).$tablename;
	}
}
?>