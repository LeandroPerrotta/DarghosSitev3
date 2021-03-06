<?
class Account
{
	private $db, $guildLevel; 
	
	private $data = array(
/*		'id' => '',
		'name' => '',
		'password' => '',
		'premend' => '',
		'email' => '',
		'key' => '',
		'blocked' => '',
		'warnings' => '',
		'group_id' => '',
		'url' => '',
		'location' => '',
		'real_name' => '',
		'creation' => ''	*/
	);
	
	function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	/* proposito: despeja um numero de conta do banco de dados ao objeto pelo seu id
	 * uso: $objeto->load(id, fields[opcional])
	 * argumentos:
	 * 		id 					-> 	id da conta
	 * 		fields[opcional] 	-> 	campos a serem carregados (se n?o definido todos campos ser?o carregados)
	 */
	function load($id, $fields = null)
	{
		$query = $this->db->query("SELECT id, name, password, premend, email, blocked, warnings, url, location, real_name, creation, lastAdClick FROM accounts WHERE id = '".$id."'");		
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$this->data['id'] = $fetch->id;				
			$this->data['name'] = addslashes($fetch->name);	
			$this->data['password'] = $fetch->password;	
			$this->data['premend'] = $fetch->premend;	
			$this->data['email'] = $fetch->email;	
			$this->data['blocked'] = $fetch->blocked;	
			$this->data['warnings'] = $fetch->warnings;	
			$this->data['url'] = addslashes($fetch->url);	
			$this->data['location'] = addslashes($fetch->location);	
			$this->data['real_name'] = addslashes($fetch->real_name);	
			$this->data['creation'] = $fetch->creation;	
			$this->data['lastAdClick'] = $fetch->lastAdClick;
			
			return true;	
		}
		else
		{
			return false;
		}			
	}
	
	
	
	/* proposito: linka um load atravez de um account email
	 * uso: $objeto->loadByEmail(email, fields[opcional])
	 * argumentos:
	 * 		email 				-> 	email da conta
	 * 		fields[opcional] 	-> 	campos a serem carregados (se n?o definido todos campos ser?o carregados)
	 */	
	function loadByEmail($email, $fields = null)
	{
		$query = $this->db->query("SELECT id FROM accounts WHERE email = '".$email."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->id, $fields);
			return true;
		}
		else
		{
			return false;
		}
	}

	
	
	/* proposito: linka um load atravez de um account name
	 * uso: $objeto->loadByName(name, fields[opcional])
	 * argumentos:
	 * 		name 				-> 	name da conta
	 * 		fields[opcional] 	-> 	campos a serem carregados (se n?o definido todos campos ser?o carregados)
	 */
	function loadByName($name, $fields = null)
	{
		$query = $this->db->query("SELECT id FROM accounts WHERE name = '".$name."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->id, $fields);
			return true;
		}
		else
		{
			return false;
		}
	}		

	
	
	/* proposito: linka um get atravez de um field
	 * uso: $objeto->get(string field)
	 * argumentos:
	 * 		field 				-> 	field a ser carregado
	 * observa??es: ESTA FUN??O EST? DESCONTINUADA
	 */
	function get($field)
	{
		switch($field)
		{
			case "id":
				return $this->getId();
			break;	
			
			case "name":
				return $this->getName();
			break;	
			
			case "password":
				return $this->getPassword();
			break;	
			
			case "premdays":
				return $this->getPremDays();
			break;	
			
			case "email":
				return $this->getEmail();
			break;	
			
			case "blocked":
				return $this->getBlocked();
			break;	
			
			case "warnings":
				return $this->getWarnings();
			break;	
			
			case "group_id":
				return $this->getGroup();
			break;	
			
			case "url":
				return $this->getUrl();
			break;	
			
			case "location":
				return $this->getLocation();
			break;	
			
			case "real_name":
				return $this->getRealName();
			break;	
			
			case "creation":
				return $this->getCreation();
			break;			
		}
	}	
	
	/* proposito: grupo de gets para obter determinado valor de um field
	 * uso: 
	 * 		$objeto->get?ValueName`()
	 * exemplo:
	 * 		$objeto->getId(), getPassword()
	 * argumentos:
	 * 		n/a
	 */
	function getId()
	{
		return $this->data['id'];
	}	

	function getName()
	{
		return stripslashes($this->data['name']);
	}	
	
	function getPassword()
	{
		return $this->data['password'];
	}		

	function getPremDays()
	{
		$leftDays = $this->data["premend"] - time();
		
		$leftDays = ($leftDays > 0) ? ceil($leftDays / 86400) : 0;
		
		return $leftDays;
	}			

	function getEmail()
	{
		return $this->data['email'];
	}		

	function getBlocked()
	{
		return $this->data['blocked'];
	}

	function getWarnings()
	{
		return $this->data['warnings'];
	}

	function getGroup()
	{
		$characters = $this->getCharacterList(true);
		
		$highGroup = 1;
		
		if(is_array($characters))
		{
			foreach($characters as $cid)
			{
				global $core;
				
				$character = $core->loadClass("character");
				$character->load($cid);
				
				if($character->getGroup() > $highGroup)
				{
					$highGroup = $character->getGroup();
				}
			}
		}
		
		return $highGroup;
	}

	function getLocation()
	{
		return stripslashes($this->data['location']);
	}

	function getUrl()
	{
		return stripslashes($this->data['url']);
	}

	function getRealName()
	{
		return stripslashes($this->data['real_name']);
	}
			
	function getCreation()
	{
		return $this->data['creation'];
	}	

	function getLastAdClick()
	{
		return $this->data['lastAdClick'];
	}	
	
	function canClickAdPage()
	{
		if(($this->data['lastAdClick'] + 60 * 60 * 24) < time())
		{
			return true;
		}
		else 
		{
			return false;
		}		
	}
	
	
	
	/* proposito: retorna uma array com os personagens pertecentes a account carregada (por nome ou id)
	 * uso: 
	 * 		$objeto->getCharacterList(returnid[opcional])
	 * exemplo:
	 * 		$objeto->getCharacterList()
	 * argumentos:
	 * 		returnid 			-> 	switch para o valor retornado, entre o nome (false) e id (true) do jogador, valor padr?o ? false (retorna o nome do jogador)
	 */	
	function getCharacterList($returnId = false)
	{
		$toReturn = "name";
		
		if($returnId)
			$toReturn = "id";
		
		$query = $this->db->query("SELECT {$toReturn} FROM players WHERE account_id = '".$this->data['id']."'");
		
		if($query->numRows() != 0)
		{
			$list= array();
		
			while($fetch = $query->fetch())
			{
				$list[] = $fetch->$toReturn;
			}
			
			return $list;
		}
		else
			return false;
	}
	
	
	
	/* proposito: linka um set atravez de um field
	 * uso: $objeto->set(field, value)
	 * argumentos:
	 * 		field 				-> 	campo a ser alterado
	 * 		value			 	-> 	novo valor do campo
	 * observa??es: ESTA FUN??O EST? DESCONTINUADA
	 */	
	function set($field, $value)
	{
		switch($field)
		{
			case "id":
				return $this->setId($value);
			break;	
			
			case "name":
				return $this->setName($value);
			break;	
			
			case "password":
				return $this->setPassword($value);
			break;	
			
			case "premdays":
				return $this->setPremDays($value);
			break;		
			
			case "email":
				return $this->setEmail($value);
			break;	
			
			case "key":
				return $this->setKey($value);
			break;	
			
			case "blocked":
				return $this->setBlocked($value);
			break;	
			
			case "warnings":
				return $this->setWarnings($value);
			break;	
			
			case "group_id":
				return $this->setGroup($value);
			break;	
			
			case "url":
				return $this->setUrl($value);
			break;	
			
			case "location":
				return $this->setLocation($value);
			break;	
			
			case "real_name":
				return $this->setRealName($value);
			break;

			case "creation":
				return $this->setCreation($value);
			break;				
		}
	}	

	

	/* proposito: grupo de sets para modificar os dados da account instanciada
	 * uso: 
	 * 		$objeto->set?fieldName`(value)
	 * exemplo:
	 * 		$objeto->setId(1), setPassword("123456")
	 * argumentos:
	 * 		n/a
	 */	
	function setId($id)
	{
		$this->data['id'] = $id;
	}
	
	function setName($name)
	{
		global $strings;
		$this->data['name'] = $strings->SQLInjection($name);
	}
	
	function setPassword($password)
	{
		$this->data['password'] = $password;
	}
	
	function setPremDays($premdays)
	{
		$this->data['premend'] = $premdays;
	}
	
	function setEmail($email)
	{
		global $strings;
		$this->data['email'] = $strings->SQLInjection($email);
	}
	
	
	function setKey($key)
	{
		$this->data['key'] = $key;
	}
	
	function setBlocked($blocked)
	{
		$this->data['blocked'] = $blocked;
	}
	
	function setWarnings($warnings)
	{
		$this->data['warnings'] = $warnings;
	}
	
	function setGroup($group_id)
	{
		$this->data['group_id'] = $group_id;
	}
	
	function setLocation($location)
	{
		global $strings;
		$this->data['location'] =  $strings->SQLInjection($location);
	}
	
	function setUrl($url)
	{
		global $strings;
		$this->data['url'] = $strings->SQLInjection($url);
	}
	
	function setRealName($real_name)
	{
		global $strings;
		$this->data['real_name'] = $strings->SQLInjection($real_name);
	}
	
	function setCreation($creation)
	{
		$this->data['creation'] = $creation;
	}	

	
	
	/* proposito: salva ou cria uma account no bando de dados
	 * uso: 
	 * 		$objeto->save()
	 * argumentos:
	 * 		n/a
	 * observa??es:
	 * 		se o campo id estiver vazio ? criado uma nova conta, caso contrario o registro ? atualizado
	 */		
	function save()
	{
		$i = 0;
		
		//update
		if(isset($this->data['id']))
		{
			foreach($this->data as $field => $value)
			{
				$i++;
				
				if($i == count($this->data))
				{
					$update .= "`".$field."` = '".$value."'";
				}
				else
				{
					$update .= "`".$field."` = '".$value."', ";
				}			
			}
			
			$this->db->query("UPDATE accounts SET $update WHERE id = '".$this->data['id']."'");
		}
		//new account
		else
		{
			foreach($this->data as $field => $value)
			{
				$i++;
				
				if($i == count($this->data))
				{
					$insert_fields .= "".$field."";
					$insert_values .= "'".$value."'";
				}
				else
				{
					$insert_fields .= "".$field.", ";
					$insert_values .= "'".$value."', ";
				}			
			}

			$this->db->query("INSERT INTO accounts ($insert_fields) values($insert_values)");			
		}
	}
	
	
	
	/* proposito: atualiza a condi??o de premium da conta, baseado na ultima atualiza??o (lastday)
	 * uso: 
	 * 		$objeto->updatePremDays()
	 * argumentos:
	 * 		n/a
	 */		
	function updatePremDays($premdays, $increment = true)
	{
		if($increment)
		{
			$daysnow = $this->getPremDays();
			
			if($daysnow == 0)
			{
				$daysnew = time() + (60 * 60 * 24 * $premdays);
				$this->setPremDays($daysnew);
			}
			else
			{
				$daysnew = time() + (60 * 60 * 24 * ($premdays + $daysnow));	
				$this->setPremDays($daysnew);
			}
		}
		else
		{
			$daysnow = $this->getPremDays();
			
			if($daysnow == 0)
			{
				return false;
			}
			else
			{
				$daysnew = $this->data["premend"] - (60 * 60 * 24 * $premdays);	
				$this->setPremDays($daysnew);
			}			
		}
		
		return true;
	}
	
	
	
	/* proposito: insere um novo e-mail a lista de contas com e-mail a modificar
	 * uso: 
	 * 		$objeto->addEmailToChange(email)
	 * argumentos:
	 * 		email		=>	novo e-mail que ser? adicionado a conta
	 */		
	function addEmailToChange($email)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."emailstochange (account_id, email, date) values('{$this->data['id']}', '{$email}', '".(time() + (60 * 60 * 24 * DAYS_TO_CHANGE_EMAIL))."')");	
	}
	
	
	
	/* proposito: obtem uma array com as mudan?as de e-mails agendadas para a conta, caso n?o haj? nenhuma mudan?a agendada retorna false
	 * uso: 
	 * 		$objeto->getEmailToChange()
	 * argumentos:
	 * 		n/a
	 */	
	function getEmailToChange()
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."emailstochange WHERE account_id = '{$this->data['id']}' ORDER BY id DESC");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$email = array(
				"email" => "{$fetch->email}",
				"date" => "{$fetch->date}"
			);
			
			return $email;
		}
		else
			return false;
	}
	
	
	
	/* proposito: cancela todas mudan?as de e-mail agendada a conta
	 * uso: 
	 * 		$objeto->cancelEmailToChange()
	 * argumentos:
	 * 		n/a
	 */	
	function cancelEmailToChange()
	{
		$this->db->query("DELETE FROM ".DB_WEBSITE_PREFIX."emailstochange WHERE account_id = '{$this->data['id']}'");
	}
	
	
	/* proposito: insere uma nova chave de mudan?a de password a conta (chave de confirma??o enviada ao e-mail do jogador no ato de gerar nova senha pelo Lost Interface)
	 * uso: 
	 * 		$objeto->setPasswordKey(key)
	 * argumentos:
	 * 		key			=>	nova chave de confirma??o de mudan?a
	 */		
	function setPasswordKey($key)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."changepasswordkeys (account_id, password_key, time) values('{$this->data['id']}', '{$key}', '".time()."')");
	}
	
	

	/* proposito: carrega uma conta pelo numero da chave de mudan?a de password, se a chave n?o existir retorna false
	 * uso: 
	 * 		$objeto->checkChangePasswordKey(key)
	 * argumentos:
	 * 		key			=>	chave de confirma??o de mudan?a
	 */	
	function checkChangePasswordKey($key)
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."changepasswordkeys WHERE password_key = '".$key."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->account_id, "password, email");	
			$this->db->query("DELETE FROM ".DB_WEBSITE_PREFIX."changepasswordkeys WHERE account_id = '{$this->data['id']}'");
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	
	/* proposito: retorna uma array contendo a chave secreta da conta e seu lembrete, se a conta n?o possui uma ? retornado false
	 * uso: 
	 * 		$objeto->getSecretKey(key)
	 * argumentos:
	 * 		n/a
	 */	
	function getSecretKey()
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."secretkeys WHERE account_id = '".$this->data['id']."'");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$secretKey["key"] =$fetch->secret_key;
			$secretKey["lembrete"] = $fetch->lembrete;
			
			return $secretKey;
		}
		else
			return false;
	}

	
	
	/* proposito: configura uma nova chave secreta e lembrete para sua conta
	 * uso: 
	 * 		$objeto->setSecretKey(key, lembrete)
	 * argumentos:
	 * 		key			=>	nova chave para recupera??o avan?ada da conta
	 * 		lembrete	=>	lembrete da chave, se for uma chave gerada pelo sistema este deve ser vazio
	 */		
	function setSecretKey($key, $lembrete)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."secretkeys (secret_key, lembrete, account_id) values('{$key}', '{$lembrete}', '{$this->data['id']}')");
	}	
	
	
	
	/* proposito: retorna uma array contendo o status de banimento da conta, se a mesma n?o estiver banida ? retornado false
	 * uso: 
	 * 		$objeto->getBans()
	 * argumentos:
	 * 		n/a
	 */	
	function getBans()
	{
		/*$query = $this->db->query("SELECT * FROM `bans` WHERE (`account` = '{$this->data["id"]}')");
		$fetch = $query->fetch();
		
		if($query->numRows() != 0)
		{
			$bans = array();
			$bans["type"] = $fetch->type;
			$bans["time"] = $fetch->time;
			$bans["reason"] = $fetch->reason_id;
			$bans["action"] = $fetch->action_id;
			
			return $bans;
		}
		else*/
			return false;
	}
	
	
	
	/* proposito: retorna true caso a conta possua algum personagem lider ou vice leader, false caso contrario
	 * uso: 
	 * 		$objeto->isGuildHighMember()
	 * argumentos:
	 * 		n/a
	 */	
	function isGuildHighMember()
	{
		$charsList = $this->getCharacterList(true); //true to get characterslist by character id
		
		global $core;
		
		foreach($charsList as $player_id)
		{
			$character = $core->loadClass("Character");
			$character->load($player_id, "rank_id");
			
			if(!$character->loadGuild())
			{		
				continue;
			}		
					
			if($character->getGuildInfo("rank_level") <= 2)
			{
				return true;
			}	
		}
		
		return false;
	}

	
	
	/* proposito: verifica se de cada personagem da conta ? membro com a guild desejada, se verdadeiro retorna o maior rank entre os personagens, se nenhum personagem estiver na guild ? retornado false
	 * uso: 
	 * 		$objeto->getGuildLevel(guild_name)
	 * argumentos:
	 * 		guild_name		=>	nome da guild desejada para verifica??o
	 */		
	function getGuildLevel($guild_name)
	{
		$charsList = $this->getCharacterList(true); //true to get characterslist by character id
		
		global $core;
		
		$access = array();
		$guildLoad = false;
		
		foreach($charsList as $player_id)
		{
			$character = $core->loadClass("Character");
			$character->load($player_id, "rank_id");
			
			if(!$character->loadGuild())	
				continue;	
			else
				$guildLoad = true;		
				
			if($character->getGuildInfo("name") == $guild_name)
			{
				$access[] = $character->getGuildInfo("rank_level");
			}	
		}
		
		sort($access);
		
		if($guildLoad)
			return $access[0];
		else
			return false;	
	}	
	
	function checkPremiumTest()
	{
		$query = $this->db->query("SELECT date FROM ".DB_WEBSITE_PREFIX."premiumtest WHERE account_id = '".$this->data["id"]."'");
		
		if($query->numRows() == 1)
		{
			return $query->fetch()->date;
		}
		elseif($query->numRows() == 0)
		{
			return false;
		}
		else
		{
			die("Premtest rows must be 0 or 1.");
		}	
	}
	
	function activePremiumTest()
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."premiumtest VALUES ('{$this->data["id"]}', '".time()."')");
		
		if($this->getPremDays() == 0)
		{
			$this->updatePremDays(PREMTEST_DAYS);
		}
		
		$this->save();		
	}
	
	function activePremiumPrize()
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."adpage VALUES ('{$this->data["id"]}', '".time()."', '".$_SERVER['REMOTE_ADDR']."')");
	
		if($this->getLastAdClick() == 0)
			$this->updatePremDays(2);
		else	
			$this->updatePremDays(1);
			
		$this->data["lastAdClick"] = time();
		
		setcookie ("bk", false);
		
		$this->save();
	}
	
	function getHighCharacter($returnId = false)
	{
		$query = $this->db->query("SELECT id, name FROM players WHERE account_id = '{$this->data[id]}' ORDER BY level DESC LIMIT 1");
		
		if($query->numRows() != 0)
		{
			$result = $query->fetch();
			
			if(!$returnId)
				return $result->name;
			else
				return $result->id;	
		}
		
		return false;	
	}
	
	function getCharMinLevel()
	{
		$query = $this->db->query("SELECT level FROM players WHERE account_id = '{$this->data[id]}' ORDER BY level DESC LIMIT 1");
		
		if($query->numRows() != 0)
		{
			$result = $query->fetch();
			return $result->level;
		}
		
		return false;	
	}	
}
?>