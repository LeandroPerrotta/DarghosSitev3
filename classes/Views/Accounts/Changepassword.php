<?php
namespace Views\Accounts;
use Framework\UI;
class Changepassword extends \Core\Views
{
	function __construct($data)
	{
		parent::__construct($data);
		
		$form = new UI\Form($this);
		
		global $pages;
		
		new UI\Label($form->GetFieldSet(), $pages["ACCOUNT.CHANGE_PASSWORD.NEW_PASSWORD"]);
		$password = new UI\Input($form->GetFieldSet());
		$password->SetName("account_password");
		$password->IsPassword();
		
		new UI\Label($form->GetFieldSet(), $pages["ACCOUNT.CHANGE_PASSWORD.NEW_PASSWORD_CONFIRM"]);
		$password_confirm = new UI\Input($form->GetFieldSet());
		$password_confirm->SetName("account_confirm_password");
		$password_confirm->IsPassword();
		
		new UI\Label($form->GetFieldSet(), $pages["ACCOUNT.CHANGE_PASSWORD.CURRENT_PASSWORD"]);
		$password_current = new UI\Input($form->GetFieldSet());
		$password_current->SetName("account_password_current");
		$password_current->IsPassword();
		
		$submit = new UI\Input($form->GetFieldSet());
		$submit->SetName("submit");			
		$submit->SetValue("Enviar");		
		$submit->IsButton();
		
		global $module;
		$module .= "
			{$this->saveHTML($form)}
		";		
	}
}