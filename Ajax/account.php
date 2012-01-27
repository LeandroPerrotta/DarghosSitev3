<?php
use Framework\Account;
class Ajax_account
{
	static function checkName()
	{		
		$name = $_POST["account_name"];
		$result = array();
		
		$result["response"] = RESPONSE_FIELD_VERIFY;
		
		if(empty($name) || $name == "" || $name == null || !$name)
		{
			$result["error"] = true;
			$result["text"] = "Digite o nome da conta!";
			return $result;
		}	
		
		if(strlen($name) < 6 || strlen($name) > 30)
		{
			$result["error"] = true;
			$result["text"] = "Deve possuir entre 6 a 30 caracteres.";		
			return $result;
		}
		
		if(preg_match("/[^A-Za-z0-9]/", $name))
		{
			$result["error"] = true;
			$result["text"] = "So é permitido caracteres númericos (0-9) e letras (a-Z).";		
			return $result;		
		}
		
		$account = new Account();
		
		if($account->loadByName($name))
		{
			$result["error"] = true;
			$result["text"] = "Nome de conta já existente. Tente outro nome.";		
			return $result;
		}
		
		$result["error"] = false;
		$result["text"] = "Sucesso";			
		
		return $result;
	}
	
	static function checkPassword()
	{
		$password = $_POST["account_password"];
		$confirm = $_POST["account_confirm_password"];
		
		$result = array();
		$result["response"] = RESPONSE_FIELD_VERIFY;
		$result["error"] = true;
		
		if(empty($password) || $password == "" || $password == null || !$password ||
			empty($confirm) || $confirm == "" || $confirm == null || !$confirm)
		{
			$result["text"] = "Digite a senha e a confirmação!";
			return $result;
		}
		
		if($password != $confirm)
		{
			$result["text"] = "Confirmação incorreta, tente novamente.";
			return $result;
		}	
		
		if(strlen($password) < 8 || strlen($password) > 30)
		{
			$result["text"] = "Deve possuir entre 8 a 30 caracteres.";		
			return $result;
		}
		
		if(preg_match("/[^A-Za-z0-9|!|@|#|$|%|&|*]/", $password))
		{
			$result["text"] = "Caracters invalidos, tente com outros caracteres.";		
			return $result;		
		}
		
		if(!preg_match("/[A-Za-z]/", $password) || !preg_match("/[0-9]/", $password))
		{
			$result["text"] = "Obrigatorio o uso de letras e numeros.";		
			return $result;			
		}
		
		$result["error"] = false;
		$result["text"] = "Sucesso";			
		
		return $result;
	}
	
	static function checkEmail()
	{
		$email = $_POST["account_email"];
		
		$result = array();
		$result["response"] = RESPONSE_FIELD_VERIFY;
		$result["error"] = true;
		
		if(empty($email) || $email == "" || $email == null)
		{
			$result["text"] = "Digite seu endereço de e-mail.";		
			return $result;		
		}
		
		if(!\Core\Strings::validEmail($email))
		{
			$result["text"] = "Este não parece ser um formato de e-mail valido.";		
			return $result;
		}
		
		$account = new Account();
		
		if($account->loadByEmail($email))
		{
			$result["text"] = "Este e-mail já esta em uso.";		
			return $result;
		}
		
		$result["error"] = false;
		$result["text"] = "Sucesso";			
		
		return $result;	
	}
	
	static function create()
	{
		$name = $_POST["account_name"];
		$password = $_POST["account_password"];
		$confirm = $_POST["account_confirm_password"];
		
		$result = array();
		$result["response"] = RESPONSE_NEXT_STEP;	
		
		$nameCheck = self::checkName($name);
		
		if($nameCheck["error"])
		{
			$result["error"] = true;
			return $result;
		}
		
		$passwordCheck = self::checkPassword($password, $confirm);
		
		if($passwordCheck["error"])
		{
			$result["error"] = true;
			return $result;
		}
		
		$account = new Account();
		
		$account->setCreation(time());
		$account->setName($name);
		$account->setPassword(\Core\Strings::encrypt($password));
		$account->save();
		
		$_SESSION["login"][] = $account->getId();
		$_SESSION["login"][] = $account->getPassword();
		
		$result["error"] = false;
		return $result;
	}
	
	static function registerEmail()
	{
		$email = $_POST["account_email"];
		
		$result = array();
		$result["response"] = RESPONSE_NEXT_STEP;	
		
		$emailCheck = self::checkEmail($email);	
		
		if($emailCheck["error"])
		{
			$result["error"] = true;
			return $result;
		}	
		
		$account = Account::loadLogged();
		$code = \Core\Strings::randKey(12, 1, "lower+number");
		
		if(!\Core\Emails::send($email, \Core\Emails::EMSG_VALIDATE_EMAIL, array($code)))
		{
			$result["error"] = true;
			return $result;
		}	
		
		$account->addEmailValidate($email, $code);
		
		$result["error"] = false;
		return $result;	
	}
}