<?
class t_Group
{
	private $_group;
	private $_groupNames = array(
		1 => "Jogador",
		2 => "Tutor",
		3 => "Senior Tutor",
		4 => "Game Master",
		5 => "Community Manager",
		6 => "Administrador"
	);
	
	function t_Group($group = null)
	{
		if($group)	
			$this->Set($group);
	}
	
	function Set($group)
	{
		$this->_group = $group;
	}
	
	function SetByName($name)
	{
		$this->_group = array_search($name, $this->_groupNames);
	}
	
	function Get()
	{
		return $this->_group;
	}
	
	function GetByName()
	{
		return $this->_groupNames[$this->_group];
	}	
}

class t_Sex
{
	private $_sex;
	private $_sex_names = array(
		0 => "female",
		1 => "male"
	);
	
	function t_Sex($sex = null)
	{
		if($sex)	
			$this->Set($sex);
	}
	
	function Set($sex)
	{
		$this->_sex = $sex;
	}
	
	function SetByName($name)
	{
		$this->_sex = array_search($name, $this->_sex_names);
	}
	
	function Get()
	{
		return $this->_sex;
	}
	
	function GetByName()
	{
		return $this->_sex_names[$this->_sex];
	}	
}

class t_Vocation
{
	private $_vocation_id;
	private $_vocation_names = array(
		0 => array("name" => "none", "abrev" => "n"),
		1 => array("name" => "sorcerer", "abrev" => "s"),
		2 => array("name" => "druid", "abrev" => "d"),
		3 => array("name" => "paladin", "abrev" => "p"),
		4 => array("name" => "knight", "abrev" => "k"),
		5 => array("name" => "master sorcerer", "abrev" => "ms"),
		6 => array("name" => "elder druid", "abrev" => "ed"),
		7 => array("name" => "royal paladin", "abrev" => "rp"),
		8 => array("name" => "elite knight", "abrev" => "ek")/*,
		9 => "Warmaster Sorcerer",
		10 => "Warden Druid",
		11 => "Holy Paladin",
		12 => "Berserk Warrior",*/
	);
	
	function t_Vocation($vocation_id = null)
	{
		if($vocation_id)
			$this->Set($vocation_id);
	}
	
	function Set($vocation_id)
	{
		$this->_vocation_id = $vocation_id;
	}
	
	function SetByName($name)
	{
		$this->_vocation_id = array_search(strtolower($name), $this->_vocation_names["name"]);
	}
	
	function Get()
	{
		return $this->_vocation_id;
	}
	
	function GetByName()
	{
		return $this->_vocation_names[$this->_vocation_id]["name"];
	}
	
	function GetByAbrev()
	{
		return $this->_vocation_names[$this->_vocation_id]["abrev"];
	}
}

class t_ForumBans
{
	private $_type;
	private $_type_str = array(
		0 => "24 horas",
		1 => "7 dias",
		2 => "30 dias",
		3 => "indeterminado"
	);
	
	function t_ForumBans($_type = null)
	{
		if($_type)
			$this->Set($_type);
	}
	
	function Set($_type)
	{
		$this->_type = $_type;
	}
	
	function Get()
	{
		return $this->_type;
	}
	
	function GetByName()
	{
		return $this->_type_str[$this->_type];
	}
}


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
	
	static function getPercentOf($value, $total)
	{
		return round(($value / $total) * 100);
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
	
	static function getSiteTable($tablename)
	{
		return DB_WEBSITE_PREFIX.$tablename;
	}
}
?>