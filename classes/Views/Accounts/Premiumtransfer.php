<?php
namespace Views\Accounts;
use Framework\UI;
class Premiumtransfer extends \Core\Views
{
	function __construct($data)
	{
		parent::__construct($data);
		
		$form = new UI\Form($this);
		
		global $pages;
		
		new UI\Label($form->GetFieldSet(), "Nome (da conta no UltraX)");
		$account_newname = new UI\Input($form->GetFieldSet());
		$account_newname->SetName("account_name");
		
		new UI\Label($form->GetFieldSet(), "Senha (da conta no UltraX)");
		$account_password = new UI\Input($form->GetFieldSet());
		$account_password->SetName("account_password");
		$account_password->IsPassword();
		
		new UI\Label($form->GetFieldSet(), "Confirmar senha (desta conta no Darghos)");
		$account_password = new UI\Input($form->GetFieldSet());
		$account_password->SetName("account_password_confirm");
		$account_password->IsPassword();
		
		$submit = new UI\Input($form->GetFieldSet());
		$submit->SetName("submit");			
		$submit->SetValue("Enviar");		
		$submit->IsButton();
		
		global $module;
		$module .= "
			<p>Através desta pagina você poderá transferir seus dias de Conta Premium desta conta no Darghos para uma conta sua no <a href='http://ultraxsoft.com/ot' target='_blank'>UltraX OT</a>.</p>
			<p>Preencha o formulario para concluir a transferência e boa diversão no UltraX!</p>	
			<p><b>Lembre-se que:</b></p>	
			<p> • Recurso somente disponivel a contas que possuam 8 ou mais dias de Conta Premium.</p>
			<p> • <b>Todos os dias de Conta Premium serão transferidos!</b></p>
			<p> • Ambas contas (essa é a do UltraX) precisam estar registradas no mesmo e-mail.</p>
		
			{$this->saveHTML($form)}
		";		
	}
}