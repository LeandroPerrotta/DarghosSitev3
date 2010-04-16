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
	
	function setPremDays($premdays)
	{
		$this->data['premend'] = $premdays;
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
		$this->data['location'] =  Strings::SQLInjection($location);
	}
	
	function setUrl($url)
	{
		$this->data['url'] = Strings::SQLInjection($url);
	}
	
	function setRealName($real_name)
	{
		$this->data['real_name'] = Strings::SQLInjection($real_name);
	}
	
	function setCreation($creation)
	{
		$this->data['creation'] = $creation;
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
			$this->load($query->fetch()->account_id, "password, email");	
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
	
	function isGuildHighMember()
	{
		$charsList = $this->getCharacterList(true); //true to get characterslist by character id
		
		foreach($charsList as $player_id)
		{
			$character = new Character();
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
	
	function getGuildLevel($guild_name)
	{
		$charsList = $this->getCharacterList(true); //true to get characterslist by character id
		
		$access = array();
		$guildLoad = false;
		
		foreach($charsList as $player_id)
		{
			$character = new Character();
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