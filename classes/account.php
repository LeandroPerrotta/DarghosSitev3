<?
class Account
{
	private $db, $guildLevel; 
	
	private $data = array(
/*		'id' => '',
		'name' => '',
		'password' => '',
		'premdays' => '',
		'lastday' => '',
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
		global $db_tenerian;
		$this->db = $db_tenerian;
	}
	
	/* proposito: despeja um numero de conta do banco de dados ao objeto pelo seu id
	 * uso: $objeto->load(id, fields[opcional])
	 * argumentos:
	 * 		id 					-> 	id da conta
	 * 		fields[opcional] 	-> 	campos a serem carregados (se não definido todos campos serão carregados)
	 */
	function load($id, $fields = null)
	{
		$query = $this->db->query("SELECT id, name, password, premdays, lastday, email, `key`, blocked, warnings, group_id, url, location, real_name, creation FROM accounts WHERE id = '".$id."'");		
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$this->data['id'] = $fetch->id;				
			$this->data['name'] = $fetch->name;	
			$this->data['password'] = $fetch->password;	
			$this->data['premdays'] = $fetch->premdays;	
			$this->data['lastday'] = $fetch->lastday;	
			$this->data['email'] = $fetch->email;	
			$this->data['key'] = $fetch->key;	
			$this->data['blocked'] = $fetch->blocked;	
			$this->data['warnings'] = $fetch->warnings;	
			$this->data['group_id'] = $fetch->group_id;	
			$this->data['url'] = $fetch->url;	
			$this->data['location'] = $fetch->location;	
			$this->data['real_name'] = $fetch->real_name;	
			$this->data['creation'] = $fetch->creation;	
			
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
	 * 		fields[opcional] 	-> 	campos a serem carregados (se não definido todos campos serão carregados)
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
	 * 		fields[opcional] 	-> 	campos a serem carregados (se não definido todos campos serão carregados)
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
	 * observações: ESTA FUNÇÃO ESTÁ DESCONTINUADA
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
			
			case "lastday":
				return $this->getLastDay();
			break;	
			
			case "email":
				return $this->getEmail();
			break;	
			
			case "key":
				return $this->getKey();
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
	 * 		$objeto->get´ValueName`()
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
		return $this->data['name'];
	}	
	
	function getPassword()
	{
		return $this->data['password'];
	}		

	function getPremDays()
	{
		return $this->data['premdays'];
	}		
			
	function getLastDay()
	{
		return $this->data['lastday'];
	}		

	function getEmail()
	{
		return $this->data['email'];
	}		
	
	function getKey()
	{
		return $this->data['key'];
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
		return $this->data['group_id'];
	}

	function getLocation()
	{
		return $this->data['location'];
	}

	function getUrl()
	{
		return $this->data['url'];
	}

	function getRealName()
	{
		return $this->data['real_name'];
	}
			
	function getCreation()
	{
		return $this->data['creation'];
	}		
	
	
	
	/* proposito: retorna uma array com os personagens pertecentes a account carregada (por nome ou id)
	 * uso: 
	 * 		$objeto->getCharacterList(returnid[opcional])
	 * exemplo:
	 * 		$objeto->getCharacterList()
	 * argumentos:
	 * 		returnid 			-> 	switch para o valor retornado, entre o nome (false) e id (true) do jogador, valor padrão é false (retorna o nome do jogador)
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
	 * observações: ESTA FUNÇÃO ESTÁ DESCONTINUADA
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
			
			case "lastday":
				return $this->setLastDay($value);
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
	 * 		$objeto->set´fieldName`(value)
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
		$this->data['name'] = $name;
	}
	
	function setPassword($password)
	{
		$this->data['password'] = $password;
	}
	
	function setPremDays($premdays)
	{
		$this->data['premdays'] = $premdays;
	}
	
	function setLastDay($lastday)
	{
		$this->data['lastday'] = $lastday;
	}
	
	function setEmail($email)
	{
		$this->data['email'] = $email;
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
		$this->data['location'] = $location;
	}
	
	function setUrl($url)
	{
		$this->data['url'] = $url;
	}
	
	function setRealName($real_name)
	{
		$this->data['real_name'] = $real_name;
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
	 * observações:
	 * 		se o campo id estiver vazio é criado uma nova conta, caso contrario o registro é atualizado
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
	
	
	
	/* proposito: atualiza a condição de premium da conta, baseado na ultima atualização (lastday)
	 * uso: 
	 * 		$objeto->updatePremDays()
	 * argumentos:
	 * 		n/a
	 */		
	function updatePremDays()
	{
		$daysToRemove = time() - $this->data['lastday'];
		
		if($daysToRemove >= $this->data['premdays'])
		{
			$this->data['premdays'] = 0;
		}
		else
		{
			$this->data['premdays'] -= $daysToRemove;
		}
		
		$this->data['lastday'] = time();
	}
	
	
	
	/* proposito: insere um novo e-mail a lista de contas com e-mail a modificar
	 * uso: 
	 * 		$objeto->addEmailToChange(email)
	 * argumentos:
	 * 		email		=>	novo e-mail que será adicionado a conta
	 */		
	function addEmailToChange($email)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."emailstochange (account_id, email, date) values('{$this->data['id']}', '{$email}', '".(time() + (60 * 60 * 24 * DAYS_TO_CHANGE_EMAIL))."')");	
	}
	
	
	
	/* proposito: obtem uma array com as mudanças de e-mails agendadas para a conta, caso não hajá nenhuma mudança agendada retorna false
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
	
	
	
	/* proposito: cancela todas mudanças de e-mail agendada a conta
	 * uso: 
	 * 		$objeto->cancelEmailToChange()
	 * argumentos:
	 * 		n/a
	 */	
	function cancelEmailToChange()
	{
		$this->db->query("DELETE FROM ".DB_WEBSITE_PREFIX."emailstochange WHERE account_id = '{$this->data['id']}'");
	}
	
	
	/* proposito: insere uma nova chave de mudança de password a conta (chave de confirmação enviada ao e-mail do jogador no ato de gerar nova senha pelo Lost Interface)
	 * uso: 
	 * 		$objeto->setPasswordKey(key)
	 * argumentos:
	 * 		key			=>	nova chave de confirmação de mudança
	 */		
	function setPasswordKey($key)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."changepasswordkeys (account_id, password_key, time) values('{$this->data['id']}', '{$key}', '".time()."')");
	}
	
	

	/* proposito: carrega uma conta pelo numero da chave de mudança de password, se a chave não existir retorna false
	 * uso: 
	 * 		$objeto->checkChangePasswordKey(key)
	 * argumentos:
	 * 		key			=>	chave de confirmação de mudança
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
	
	
	
	/* proposito: retorna uma array contendo a chave secreta da conta e seu lembrete, se a conta não possui uma é retornado false
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
	 * 		key			=>	nova chave para recuperação avançada da conta
	 * 		lembrete	=>	lembrete da chave, se for uma chave gerada pelo sistema este deve ser vazio
	 */		
	function setSecretKey($key, $lembrete)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."secretkeys (secret_key, lembrete, account_id) values('{$key}', '{$lembrete}', '{$this->data['id']}')");
	}	
	
	
	
	/* proposito: retorna uma array contendo o status de banimento da conta, se a mesma não estiver banida é retornado false
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

	
	
	/* proposito: verifica se de cada personagem da conta é membro com a guild desejada, se verdadeiro retorna o maior rank entre os personagens, se nenhum personagem estiver na guild é retornado false
	 * uso: 
	 * 		$objeto->getGuildLevel(guild_name)
	 * argumentos:
	 * 		guild_name		=>	nome da guild desejada para verificação
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
	

}
?>