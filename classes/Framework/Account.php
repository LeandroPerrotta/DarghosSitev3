<?
namespace Framework;
use Core\Consts;
use \Core\Configs as g_Configs;
class Account
{
	const
		PLAYER_LIST_BY_ID = 0
		,PLAYER_LIST_BY_NAME = 1
		;
	
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
	
	private $_group;
	
	static function loadLogged()
	{
		if(!\Core\Main::isLogged())
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
		$this->db = \Core\Main::$DB;
	}
	
	function load($id, $fields = null)
	{
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
			$query_str = "SELECT `id`, `name`, `password`, `premend`, `email`, `blocked`, `warnings`, `vipend`, `lastexpbonus`, `balance` FROM `accounts` WHERE `id` = '{$id}'";
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			$query_str = "SELECT `id`, `name`, `password`, `salt`, `premdays`, `lastday`, `email`, `blocked`, `warnings`, `group_id`, `vipend`, `lastexpbonus`, `balance` FROM `accounts` WHERE `id` = '{$id}'";
			
		$query = $this->db->query($query_str);		
		
		if($query->numRows() != 0)
		{
			$this->data = $query->fetchAssocArray();
			
			$query = $this->db->query("SELECT `real_name`, `location`, `url`, `creation` FROM `".\Core\Tools::getSiteTable("accounts_personal")."` WHERE `account_id` = '{$id}'");
			
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
		$query = $this->db->query("SELECT `account_id` FROM `players` WHERE name = '".$name."' AND deleted = 0");
		
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
				$this->db->query("UPDATE ".\Core\Tools::getSiteTable("accounts_personal")." SET `real_name` = '{$this->real_name}', `location` = '{$this->location}', `url` = '{$this->url}', `creation` = '{$this->creation}' WHERE `account_id` = '{$this->data["id"]}'");
			else
				$this->db->query("INSERT INTO ".\Core\Tools::getSiteTable("accounts_personal")." (`account_id`, `creation`) VALUES('{$this->data["id"]}', '$this->creation')");
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
			
			$this->db->query("INSERT INTO ".\Core\Tools::getSiteTable("accounts_personal")." VALUES('{$this->data["id"]}', '{$this->real_name}', '{$this->location}', '{$this->url}', '$this->creation')");	
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
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
		{
			return $this->data['premend']; 
		}
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
		{
			return ($this->getPremDays() > 0) ? time() + ceil($this->getPremDays() * 60 * 60 * 24) : 0;
		}
	}
	
	function getEmail() { return $this->data['email']; }		
	
	function getBlocked() { return $this->data['blocked']; }

	function getWarnings() { return $this->data['warnings']; }
	
	function getBalance() { return $this->data['balance']; }
	
	function getVIPEnd() { return $this->data['vipend']; }
	
	function getLastExpBonus() { return $this->data['lastexpbonus']; }
	
	function getVIPDaysLeft() {
	    
	    if($this->data["vipend"] == 0)
	        return 0;
	    	
	    $leftDays = $this->data["vipend"] - time();
	    $leftDays = ($leftDays > 0) ? ceil($leftDays / 86400) : 0;
	    return $leftDays;	    
	}
	
	function getExpDaysLeft() {
	    
	    if($this->data["lastexpbonus"] == 0)
	        return 0;
	    	
	    $expEnd = $this->getExpEnd();
	    
	    if(time() > $expEnd)
	        return 0;
	    
	    return ceil(($expEnd - time()) / 86400);	    
	}
	
	function getExpEnd() {
	    return $this->data["lastexpbonus"] + (60 * 60 * 24 * 2); //we really might improve this...
	}

	/* Personal Infos */ 
	function getLocation() { return stripslashes($this->location); }
	
	function getUrl() { return stripslashes($this->url); }
	
	function getRealName() { return stripslashes($this->real_name); }	
			
	function getCreation() { return $this->creation; }	

	/* Handle Functions */
		
	function getPremDays()
	{
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
		{
			$pastDays = (time() - $this->data['lastday']) / 60 / 60 / 24;
			$newDays = $this->data['premdays'] - $pastDays;	
			return ($newDays > 0 ? ceil($newDays) : 0);
		}
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
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
		if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
		{
			return $this->data["group_id"];
		}
		elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
		{	
			$characters = $this->getCharacterList(self::PLAYER_LIST_BY_ID);
			
			$highGroup = 1;
			
			if(is_array($characters))
			{
				foreach($characters as $cid)
				{				
					$player = new \Framework\Player();
					$player->load($cid);
					
					if($player->getGroup() > $highGroup)
					{
						$highGroup = $player->getGroup();
					}
				}
			}
			
			return $highGroup;
		}
	}	
	
	function getAccess()
	{
		if(!$this->_group)
		{
			$group_id = $this->getGroup();
			$this->_group = \Framework\Group::LoadById($group_id);
		}
		
		return $this->_group->access;
	}
	
	function getCharacterList($returnValue = self::PLAYER_LIST_BY_NAME)
	{
		$toReturn = "name";
		
		if($returnValue == self::PLAYER_LIST_BY_ID)
			$toReturn = "id";
		
		$query = $this->db->query("SELECT {$toReturn} FROM players WHERE account_id = '".$this->data['id']."' AND deleted = 0");
		
		$list= array();
		
		if($query->numRows() != 0)
		{		
			while($fetch = $query->fetch())
			{
				$list[] = $fetch->{$toReturn};
			}			
		}

		return $list;
	}
	
	function getGuildLevel()
	{
		$char_list = $this->getCharacterList(self::PLAYER_LIST_BY_ID);
		$guild_level = 0;
		
		foreach($char_list as $player_id)
		{
			$player = new \Framework\Player();
			
			$player->load($player_id);
			
			if(!$player->LoadGuild())
				continue;
				
			if($player->GetGuildLevel() > $guild_level)
				$guild_level = $player->GetGuildLevel();
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
		$this->data['name'] = \Core\Strings::SQLInjection($name);
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
		$this->data['email'] = \Core\Strings::SQLInjection($email);
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
		$this->location =  \Core\Strings::SQLInjection($location);
	}
	
	function setUrl($url)
	{
		$this->url = \Core\Strings::SQLInjection($url);
	}
	
	function setRealName($real_name)
	{
		$this->real_name = \Core\Strings::SQLInjection($real_name);
	}
	
	function setCreation($creation)
	{
		$this->creation = $creation;
	}	
		
	function addBalance($balance){
	    $this->data['balance'] += $balance;
	    
	    if($this->data['balance'] < 0)
	        $this->data['balance'] = 0;
	}
	
	function addExpDays(){
	    
	    if($this->getExpDaysLeft() == 0){
	        $this->data['lastexpbonus'] = time();
	    }
	    else{
	        trigger_error("Trying to add more exp days on a account that already has exp days.");
	    }
	}
	
	function updatePremDays($premdays, $increment = true)
	{
		if($increment)
		{
			if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
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
			elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			{
				$this->data["premdays"] = $this->getPremDays() + $premdays;
				$this->data["lastday"] = time();
			}
		}
		else
		{
			if(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_OPENTIBIA)
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
			elseif(g_Configs::Get(g_Configs::eConf()->USE_DISTRO) == Consts::SERVER_DISTRO_TFS)
			{
				$newdays = $this->getPremDays() - $premdays;
				$this->data["premdays"] = ($newdays > 0) ? $newdays : 0;
				$this->data["lastday"] = time();
			}							
		}
		
		return true;
	}
	
	function balanceRequest($auth, $ref, $value){
	    $this->db->query("INSERT INTO ".\Core\Tools::getSiteTable("orders")." 
	            (`id`, `account_id`, `type`, `balance`, `server`, `generated_in`, `status`, `lastupdate_in`, `auth`, `email_vendor`) values
	            ('{$ref}', {$this->id}, 'PagSeguro', {$balance}, 1, UNIX_TIMESTAMP(), 2, 0, '{$auth}', 'platinum@darghos.com')");
	}
		
	function addEmailToChange($email)
	{
		$this->db->query("INSERT INTO ".\Core\Tools::getSiteTable("emailstochange")." (account_id, email, date) values('{$this->data['id']}', '{$email}', '".(time() + (60 * 60 * 24 * g_Configs::Get(g_Configs::eConf()->CHANGEEMAIL_WAIT_DAYS)))."')");	
	}
	
	function getEmailToChange()
	{
		$query = $this->db->query("SELECT * FROM ".\Core\Tools::getSiteTable("emailstochange")." WHERE account_id = '{$this->data['id']}' ORDER BY id DESC");
		
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
		$this->db->query("DELETE FROM ".\Core\Tools::getSiteTable("emailstochange")." WHERE account_id = '{$this->data['id']}'");
	}
		
	function setPasswordKey($key)
	{
		$this->db->query("INSERT INTO ".\Core\Tools::getSiteTable("changepasswordkeys")." (account_id, password_key, time) values('{$this->data['id']}', '{$key}', '".time()."')");
	}
	
	function checkChangePasswordKey($key)
	{
		$query = $this->db->query("SELECT * FROM ".\Core\Tools::getSiteTable("changepasswordkeys")." WHERE password_key = '".$key."'");
		
		if($query->numRows() != 0)
		{
			$this->load($query->fetch()->account_id);	
			$this->db->query("DELETE FROM ".\Core\Tools::getSiteTable("changepasswordkeys")." WHERE account_id = '{$this->data['id']}'");
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function getSecretKey()
	{
		$query = $this->db->query("SELECT * FROM ".\Core\Tools::getSiteTable("secretkeys")." WHERE account_id = '".$this->data['id']."'");
		
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
		$this->db->query("INSERT INTO ".\Core\Tools::getSiteTable("secretkeys")." (secret_key, lembrete, account_id) values('{$key}', '{$lembrete}', '{$this->data['id']}')");
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
		$query = $this->db->query("SELECT date FROM ".\Core\Tools::getSiteTable("premiumtest")." WHERE account_id = '".$this->data["id"]."'");
		
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
	
	function getHighCharacter($returnId = false)
	{
		$query = $this->db->query("SELECT id, name FROM players WHERE account_id = '{$this->data[id]}' AND deleted = 0 ORDER BY level DESC LIMIT 1");
		
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
		$query = $this->db->query("SELECT `level` FROM `players` WHERE `account_id` = '{$this->data[id]}' AND `deleted` = 0 ORDER BY `level` DESC LIMIT 1");
		
		if($query->numRows() != 0)
		{
			$result = $query->fetch();
			return $result->level;
		}
		
		return false;		
	}
	
	function getCharMinLevel()
	{
		$query = $this->db->query("SELECT level FROM players WHERE account_id = '{$this->data[id]}' AND deleted = 0 ORDER BY level DESC LIMIT 1");
		
		if($query->numRows() != 0)
		{
			$result = $query->fetch();
			return $result->level;
		}
		
		return false;	
	}	
	
	function checkPlayerInvite($player_id)
	{
		$character_list = $this->getCharacterList(self::PLAYER_LIST_BY_ID);
		
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
			`".\Core\Tools::getSiteTable("itemshop_log")."` `log` 
		LEFT JOIN
			`".\Core\Tools::getSiteTable("itemshop")."` `shop`
		ON
			`shop`.`id` = `log`.`shop_id`
		LEFT JOIN
			`players`
		ON
			`players`.`id` = `log`.`player_id`
		LEFT JOIN
			`".\Core\Tools::getSiteTable("itemshop_use_log")."` `use`
		ON
			`use`.`log_id` = `log`.`id`
		LEFT JOIN
			`players` `player_use`
		ON
			`player_use`.`id` = `use`.`player_id`			
		WHERE 
			`log`.`player_id` IN (SELECT `id` FROM `players` WHERE `account_id` = '{$this->getId()}' AND `deleted` = 0) 
			{$limit}
		ORDER BY 
			`log`.`date` DESC");
		
		$query instanceof \Core\Query;
		return $query;	
	}
	
	function addEmailValidate($email, $code)
	{
		$this->db->query("INSERT INTO `".\Core\Tools::getSiteTable("email_validating")."` VALUES ('{$this->getId()}', '{$email}', '{$code}', UNIX_TIMESTAMP())");
	}
	
	function activateEmailByCode($code)
	{
		$query = $this->db->query("SELECT `email` FROM `".\Core\Tools::getSiteTable("email_validating")."` WHERE `account_id` = {$this->getId()} AND `code` = '{$code}'");
		
		if($query->numRows() == 0)
			return false;
			
		$fetch = $query->fetch();	
		$this->setEmail($fetch->email);
		//$this->addPremiumTest();
		$this->save();
		$this->clearEmailCodes();
		
		return true;
	}
	
	function addPremiumTest(){
	    $this->db->query("INSERT INTO `".\Core\Tools::getSiteTable("premiumtest")."` VALUES ({$this->getId()}, UNIX_TIMESTAMP()) ");
	    $this->updatePremDays(10);
	}
	
	function clearEmailCodes()
	{
		$query = $this->db->query("DELETE FROM `".\Core\Tools::getSiteTable("email_validating")."` WHERE `account_id` = {$this->getId()}");
	}
	
}
?>