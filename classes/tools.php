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
		0 => "None",
		1 => "Sorcerer",
		2 => "Druid",
		3 => "Paladin",
		4 => "Knight",
		5 => "Master Sorcerer",
		6 => "Elder Druid",
		7 => "Royal Paladin",
		8 => "Elite Knight",
		9 => "Warmaster Sorcerer",
		10 => "Warden Druid",
		11 => "Holy Paladin",
		12 => "Berserk Knight",
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
		$this->_vocation_id = array_search($name, $this->_vocation_names);
	}
	
	function Get()
	{
		return $this->_vocation_id;
	}
	
	function GetByName()
	{
		return $this->_vocation_names[$this->_vocation_id];
	}
}


class Tools
{		
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
}
?>