<?
define("ACCOUNT_CHARACTERLIST_BY_ID", true);
define("ACCOUNT_CHARACTERLIST_BY_NAME", false);
define("GROUP_PLAYERS", 			1);
define("GROUP_NON_PVP_PLAYERS", 	2);
define("GROUP_TUTOR", 				3);
define("GROUP_STUTOR", 				4);
define("GROUP_GAMEMASTER", 			5);
define("GROUP_COMMUNITYMANAGER", 	6);
define("GROUP_ADMINISTRATOR", 		7);

class Account
{
	private $db; 
	private static $logged;
	
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
	
	private $real_name = "", $location = "", $url = "", $creation, $load_personal = false;
	
	static function loadLogged()
	{
		if(!Core::isLogged())
			return false;
			
		if(!self::$logged)
		{
			self::$logged = new Account();			
			if(!self::$logged->load($_SESSION['login'][0]))
				return false;			
		}
			
		return self::$logged;
	}	
	
	function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	function load($id, $fields = null)
	{
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			$query_str = "SELECT `id`, `name`, `password`, `premend`, `email`, `blocked`, `warnings` FROM `accounts` WHERE `id` = '{$id}'";
		elseif(SERVER_DISTRO == DISTRO_TFS)
			$query_str = "SELECT `id`, `name`, `password`, `salt`, `premdays`, `lastday`, `email`, `blocked`, `warnings`, `group_id` FROM `accounts` WHERE `id` = '{$id}'";
			
		$query = $this->db->query($query_str);		
		
		if($query->numRows() != 0)
		{
			$this->data = $query->fetchAssocArray();
			
			$query = $this->db->query("SELECT `real_name`, `location`, `url`, `creation` FROM `".Tools::getSiteTable("accounts_personal")."` WHERE `account_id` = '{$id}'");
			
			if($query->numRows() != 0)
			{
				$fetch = $query->fetch();
				$this->load_personal = true;
				
				$this->real_name = $fetch->real_name;
				$this->location = $fetch->location;
				$this->url = $fetch->url;
				$this->creation = $fetch->creation;
			}
			
			return true;	
		}
		else
		{
			return false;
		}			
	}
	
	function loadByEmail($email)
	{
		$query = $this->db->query("SELECT id FROM accounts WHERE email = '".$email."'");
		
		if($query->numRows() != 0 && $this->load($query->fetch()->id))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function loadByName($name)
	{
		$query = $this->db->query("SELECT id FROM accounts WHERE name = '".$name."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->id);
			return true;
		}
		else
		{
			return false;
		}
	}		
	
	function loadByCharacterName($name)
	{
		$query = $this->db->query("SELECT `account_id` FROM `players` WHERE name = '".$name."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->account_id);
			return true;
		}

		return false;		
	}

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
			
			if($this->load_personal)
				$this->db->query("UPDATE ".Tools::getSiteTable("accounts_personal")." SET `real_name` = '{$this->real_name}', `location` = '{$this->location}', `url` = '{$this->url}', `creation` = '{$this->creation}' WHERE `account_id` = '{$this->data["id"]}'");
			else
				$this->db->query("INSERT INTO ".Tools::getSiteTable("accounts_personal")." (`account_id`, `creation`) VALUES('{$this->data["id"]}', '$this->creation')");
		}
		//new account
		else
		{
			$insert_fields = "";
			$insert_values = "";
			
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
			$this->data["id"] = $this->db->lastInsertId();
			
			$this->db->query("INSERT INTO ".Tools::getSiteTable("accounts_personal")." VALUES('{$this->data["id"]}', '{$this->real_name}', '{$this->location}', '{$this->url}', '$this->creation')");	
		}
	}	
	
	/* GETS */
	
	/* Função obsoleta, use get{FIELDNAME}() ao invez de get($FIELDNAME)*/
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
	
	/* Informações internas da conta */
	function getId() { return $this->data['id']; }	
	
	function getName(){ return stripslashes($this->data['name']); }	
	
	function getPassword() { return $this->data['password']; }			
	
	function getPremEnd() 
	{ 
		if(SERVER_DISTRO == DISTRO_OPENTIBIA)
		{
			return $this->data['premend']; 
		}
		elseif(SERVER_DISTRO == DISTRO_TFS)
		{
			return ($this->getPremDays() > 0) ? ceil(time() + ($this->getPremDays() * 60 * 60 * 24)) : 0;
		}
	}
	
	function getEmail() { return $this->data['email']; }		
	
	function getBlocked() { return $this->data['blocked']; }

	function getWarnings() { return $this->data['warnings']; }


	/* Personal Infos */ 
	function getLocation() { return stripslashes($this->location); }
	
	function getUrl() { return stripslashes($this->url); }
	
	function getRealName() { return stripslashes($this->real_name); }	
			
	function getCreation() { return $this->creation; }	

	/* Handle Functions */
	/*
	function getLastAdClick() { return $this->data['lastAdClick']; }
	
	
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
	*/
	
	function getPremDays()
	{
		if(SERVER_DISTRO == DISTRO_TFS)
		{
			$pastDays = (time() - $this->data['lastday']) / 60 / 60 / 24;
			$newDays = $this->data['premdays'] - $pastDays;	
			return ($newDays > 0 ? ceil($newDays) : 0);
		}
		elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
		{
			if($this->data["premend"] == 0)
				return 0;
			
			$leftDays = $this->data["premend"] - time();
			$leftDays = ($leftDays > 0) ? ceil($leftDays / 86400) : 0;	
			return $leftDays;			
		}
	}	
	
	function getGroup()
	{
		if(SERVER_DISTRO == DISTRO_TFS)
		{
			return $this->data["group_id"];
		}
		elseif(SERVER_DISTRO == DISTRO_OPENTIBIA)
		{	
			$characters = $this->getCharacterList(ACCOUNT_CHARACTERLIST_BY_ID);
			
			$highGroup = 1;
			
			if(is_array($characters))
			{
				foreach($characters as $cid)
				{				
					$character = new Character();
					$character->load($cid);
					
					if($character->getGroup() > $highGroup)
					{
						$highGroup = $character->getGroup();
					}
				}
			}
			
			return $highGroup;
		}
	}	
	
	function getCharacterList($returnValue = ACCOUNT_CHARACTERLIST_BY_NAME)
	{
		$toReturn = "name";
		
		if($returnValue)
			$toReturn = "id";
		
		$query = $this->db->query("SELECT {$toReturn} FROM players WHERE account_id = '".$this->data['id']."'");
		
		$list= array();
		
		if($query->numRows() != 0)
		{		
			while($fetch = $query->fetch())
			{
				$list[] = $fetch->$toReturn;
			}			
		}

		return $list;
	}
	
	function getGuildLevel()
	{
		$char_list = $this->getCharacterList(ACCOUNT_CHARACTERLIST_BY_ID);
		$guild_level = 0;
		
		foreach($char_list as $player_id)
		{
			$character = new Character();
			
			$character->load($player_id);
			
			if(!$character->LoadGuild())
				continue;
				
			if($character->GetGuildLevel() > $guild_level)
				$guild_level = $character->GetGuildLevel();
		}
		
		return $guild_level;
	}
	
	
	/* SETS */
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
			
			case "premend":
				return $this->setPremEnd($value);
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
	
	function setId($id)
	{
		$this->data['id'] = $id;
	}
	
	function setName($name)
	{
		$this->data['name'] = Strings::SQLInjection($name);
	}
	
	function setPassword($password)
	{
		$this->data['password'] = $password;
	}
	
	function setPremEnd($premend)
	{
		$this->data['premend'] = $premend;
	}
	
	function setEmail($email)
	{
		$this->data['email'] = Strings::SQLInjection($email);
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
		$this->location =  Strings::SQLInjection($location);
	}
	
	function setUrl($url)
	{
		$this->url = Strings::SQLInjection($url);
	}
	
	function setRealName($real_name)
	{
		$this->real_name = Strings::SQLInjection($real_name);
	}
	
	function setCreation($creation)
	{
		$this->creation = $creation;
	}	
		
	function updatePremDays($premdays, $increment = true)
	{
		if($increment)
		{
			if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			{
				$daysnow = $this->getPremDays();
				
				if($daysnow == 0)
				{
					$daysnew = time() + (60 * 60 * 24 * $premdays);
					$this->setPremEnd($daysnew);
				}
				else
				{
					$daysnew = time() + (60 * 60 * 24 * ($premdays + $daysnow));	
					$this->setPremEnd($daysnew);
				}
			}
			elseif(SERVER_DISTRO == DISTRO_TFS)
			{
				$this->data["premdays"] = $this->getPremDays() + $premdays;
				$this->data["lastday"] = time();
			}
		}
		else
		{
			if(SERVER_DISTRO == DISTRO_OPENTIBIA)
			{			
				$daysnow = $this->getPremDays();
				
				if($daysnow == 0)
				{
					return false;
				}
				else
				{
					$daysnew = $this->data["premend"] - (60 * 60 * 24 * $premdays);	
					$this->setPremEnd($daysnew);
				}
			}
			elseif(SERVER_DISTRO == DISTRO_TFS)
			{
				$newdays = $this->getPremDays() - $premdays;
				$this->data["premdays"] = ($newdays > 0) ? $newdays : 0;
				$this->data["lastday"] = time();
			}							
		}
		
		return true;
	}
		
	function addEmailToChange($email)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."emailstochange (account_id, email, date) values('{$this->data['id']}', '{$email}', '".(time() + (60 * 60 * 24 * DAYS_TO_CHANGE_EMAIL))."')");	
	}
	
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
	
	function cancelEmailToChange()
	{
		$this->db->query("DELETE FROM ".DB_WEBSITE_PREFIX."emailstochange WHERE account_id = '{$this->data['id']}'");
	}
		
	function setPasswordKey($key)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."changepasswordkeys (account_id, password_key, time) values('{$this->data['id']}', '{$key}', '".time()."')");
	}
	
	function checkChangePasswordKey($key)
	{
		$query = $this->db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."changepasswordkeys WHERE password_key = '".$key."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->account_id);	
			$this->db->query("DELETE FROM ".DB_WEBSITE_PREFIX."changepasswordkeys WHERE account_id = '{$this->data['id']}'");
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
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

	function setSecretKey($key, $lembrete)
	{
		$this->db->query("INSERT INTO ".DB_WEBSITE_PREFIX."secretkeys (secret_key, lembrete, account_id) values('{$key}', '{$lembrete}', '{$this->data['id']}')");
	}	
	
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
	
	function getHighLevel()
	{
		$query = $this->db->query("SELECT `level` FROM `players` WHERE `account_id` = '{$this->data[id]}' ORDER BY `level` DESC LIMIT 1");
		
		if($query->numRows() != 0)
		{
			$result = $query->fetch();
			return $result->level;
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
	
	function checkPlayerInvite($player_id)
	{
		$character_list = $this->getCharacterList(ACCOUNT_CHARACTERLIST_BY_ID);
		
		if(in_array($player_id, $character_list))
			return true;
			
		return false;	
	}
	
	function getItemShopPurchasesQuery($daysago = nil)
	{	
		if($daysago)
		{
			$limit = " AND `log`.`date` >= UNIX_TIMESTAMP() - (60 * 60 * 24 * {$daysago})";
		}
		
		$query = $this->db->query("
		SELECT 
			`log`.`id`,
			`log`.`date`,
			`players`.`name` as `player_name`,
			`shop`.`name`,
			`shop`.`price`,
			`use`.`date` as `use_date`,
			`player_use`.`name` as `player_use`,
			`player_use`.`id` as `id_use`			
		FROM 
			`".Tools::getSiteTable("itemshop_log")."` `log` 
		LEFT JOIN
			`".Tools::getSiteTable("itemshop")."` `shop`
		ON
			`shop`.`id` = `log`.`shop_id`
		LEFT JOIN
			`players`
		ON
			`players`.`id` = `log`.`player_id`
		LEFT JOIN
			`".Tools::getSiteTable("itemshop_use_log")."` `use`
		ON
			`use`.`log_id` = `log`.`id`
		LEFT JOIN
			`players` `player_use`
		ON
			`player_use`.`id` = `use`.`player_id`			
		WHERE 
			`log`.`player_id` IN (SELECT `id` FROM `players` WHERE `account_id` = '{$this->getId()}') 
			{$limit}
		ORDER BY 
			`log`.`date` DESC");
		
		$query instanceof Query;
		return $query;	
	}
	
	function addEmailValidate($email, $code)
	{
		$this->db->query("INSERT INTO `".Tools::getSiteTable("email_validating")."` VALUES ('{$this->getId()}', '{$email}', '{$code}', UNIX_TIMESTAMP())");
	}
	
	function activateEmailByCode($code)
	{
		$query = $this->db->query("SELECT `email` FROM `".Tools::getSiteTable("email_validating")."` WHERE `account_id` = {$this->getId()} AND `code` = '{$code}'");
		
		if($query->numRows() == 0)
			return false;
			
		$fetch = $query->fetch();	
		$this->setEmail($fetch->email);
		$this->save();
		$this->clearEmailCodes();
		
		return true;
	}
	
	function clearEmailCodes()
	{
		$query = $this->db->query("DELETE FROM `".Tools::getSiteTable("email_validating")."` WHERE `account_id` = {$this->getId()}");
	}
	
}
?>