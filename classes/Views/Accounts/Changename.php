<?php
namespace Views\Accounts;
use Framework\UI;
class Changename extends \Core\Views
{
	function __construct($data)
	{
		parent::__construct($data);
		
		$form = new UI\Form($this);
		
		global $pages;
		
		new UI\Label($form->GetFieldSet(), "Novo nome");
		$account_newname = new UI\Input($form->GetFieldSet());
		$account_newname->SetName("account_name");
		
		new UI\Label($form->GetFieldSet(), "Confirmar senha");
		$account_password = new UI\Input($form->GetFieldSet());
		$account_password->SetName("account_password");
		$account_password->IsPassword();
		
		$submit = new UI\Input($form->GetFieldSet());
		$submit->SetName("submit");			
		$submit->SetValue("Enviar");		
		$submit->IsButton();
		
		global $module;
		$module .= "
			<p>Bem vindo a seção de mudança de nome de conta. Este é um serviço especial oferecido para que você possa renomear a sua conta.</p>
			<p><b>Como este é um serviço especial, para cada mudança de nome terá um custo de R$ 3,00, que será descontado automaticamente do saldo de sua conta no final da operação.</b></p>	
			<p><b>Obs:</b></p>	
			<p> • O sistema é sensivel ao uso de letras maiusculas e minusculas, o que significa que MeuNovoNome é diferente de meunovonome que também é diferente de MEUNOVONOME.</p>		
		
			{$this->saveHTML($form)}
		";		
	}
}