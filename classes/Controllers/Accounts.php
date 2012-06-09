<?php 
namespace Controllers;

use Core\Configs;
use Core\Consts;
use Framework\Account as AccountModel;
use Views\Accounts as AccountViews;

class Accounts
{
	function Checkname($isAjax = true, $name = NULL)
	{
		\Core\Main::$isAjax = $isAjax;
		$name = ($name) ? $name : $_POST["account_name"];
		$result = array();
	
		$result["response"] = Consts::AJAX_RESPONSE_FIELD_VERIFY;
	
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
	
		$account = new AccountModel();
	
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
	
	function Checkpassword($isAjax = true)
	{
		\Core\Main::$isAjax = $isAjax;
		$password = $_POST["account_password"];
		$confirm = $_POST["account_confirm_password"];
	
		$result = array();
		$result["response"] = Consts::AJAX_RESPONSE_FIELD_VERIFY;
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
	
	function Checkemail()
	{
		\Core\Main::$isAjax = true;
		$email = $_POST["account_email"];
	
		$result = array();
		$result["response"] = Consts::AJAX_RESPONSE_FIELD_VERIFY;
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
	
		$account = new AccountModel();
	
		if($account->loadByEmail($email))
		{
			$result["text"] = "Este e-mail já esta em uso.";
			return $result;
		}
	
		$result["error"] = false;
		$result["text"] = "Sucesso";
	
		return $result;
	}
	
	function Create()
	{
		\Core\Main::$isAjax = true;
		$name = $_POST["account_name"];
		$password = $_POST["account_password"];
		$confirm = $_POST["account_confirm_password"];
	
		$result = array();
		$result["response"] = Consts::AJAX_RESPONSE_NEXT_STEP;
	
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
	
		$account = new AccountModel();
	
		$account->setCreation(time());
		$account->setName($name);
		$account->setPassword(\Core\Strings::encrypt($password));
		$account->save();
	
		$_SESSION["login"][] = $account->getId();
		$_SESSION["login"][] = $account->getPassword();
	
		$result["error"] = false;
		return $result;
	}
	
	function Registeremail()
	{
		\Core\Main::$isAjax = true;
		$email = $_POST["account_email"];
	
		$result = array();
		$result["response"] = Consts::AJAX_RESPONSE_NEXT_STEP;
	
		$emailCheck = self::checkEmail($email);
	
		if($emailCheck["error"])
		{
			$result["error"] = true;
			return $result;
		}
	
		$account = AccountModel::loadLogged();
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
	
	function Changepassword()
	{
		$data = array();
		$showView = true;
		
		$logged = AccountModel::loadLogged();
		if(!$logged)
			return false;
		
		if($_POST)
		{			
			$data["message"] = array();
			$data["message"]["title"] = "Falha!";
			
			$checkPassword = $this->Checkpassword(false);
			
			if($logged->getPassword() != \Core\Strings::encrypt($_POST["account_password_current"]))
			{
				$data["message"]["body"] = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			}
			elseif($checkPassword["error"])
			{
				$data["message"]["body"] = $checkPassword["text"];
			}
			elseif(\Core\Strings::encrypt($_POST["account_password"]) == $logged->getPassword())
			{
				$data["message"]["body"] = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHANGEPASS_SAME_PASSWORD);
			}
			else
			{
				$logged->setPassword(\Core\Strings::encrypt($_POST["account_password"]));
				$logged->save();
			
				$_SESSION["login"] = array();
			
				$_SESSION["login"][] = $logged->getId();
				$_SESSION["login"][] = $logged->getPassword();
			
				$data["message"]["title"] = "Sucesso!";
				$data["message"]["body"] = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_PASSWORD_CHANGED);
				$showView = false;
			}		
		}
		
		if($data["message"])
			\Core\Main::sendMessageBox($data["message"]["title"], $data["message"]["body"]);	
		
		if($showView)
			$view = new AccountViews\Changepassword($data);
		
		return true;
	}
	
	function Changename()
	{
		$data = array();
		$showView = true;
		
		$logged = AccountModel::loadLogged();
		if(!$logged)
			return false;
		
		if($_POST)
		{
			$data["message"] = array();
			$data["message"]["title"] = "Falha!";
			
			$checkName = $this->Checkname(false);
				
			if($logged->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
			{
				$data["message"]["body"] = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			}
			elseif($logged->getPremDays() < 15)
			{
				$data["message"]["body"] = \Core\Lang::Message(\Core\Lang::$e_Msgs->OPERATION_NEED_PREMDAYS, 15);
			}
			elseif($checkName["error"])
			{
				$data["message"]["body"] = $checkName["text"];
			}
			else
			{
		
				$logged->setName($_POST["account_name"]);
				$logged->updatePremDays(15, false);
				$logged->save();
				
				\Core\Main::addChangeLog('acc_rename', $logged->getId(), $_POST["account_name"]);
					
				$data["message"]["title"] = "Sucesso!";
				$data["message"]["body"] = \Core\Lang::Message(\Core\Lang::$e_Msgs->ACCOUNT_CHANGENAME_SUCCESS, $_POST["account_name"]);
				$showView = false;
			}
		}
		
		if($data["message"])
			\Core\Main::sendMessageBox($data["message"]["title"], $data["message"]["body"]);
		
		if($showView)
			$view = new AccountViews\Changename($data);
		
		return true;		
	}
}
?>