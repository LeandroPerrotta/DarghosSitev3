<?php
namespace Views\Auctions;
use Framework\Auctions;
use Framework\Player;
use Framework\UI;
class Create extends \Core\Views
{
	function __construct($data)
	{
		parent::__construct($data);
		
		$form = new UI\Form($this);
		
		new UI\Label($form->GetFieldSet(), "Titulo:");
		$title = new UI\Input($form->GetFieldSet());
		$title->SetName("auction_title");
		
		new UI\Label($form->GetFieldSet(), "Descrição:");
		$description = new UI\TextArea($form->GetFieldSet());
		$description->SetName("auction_description");
		
		new UI\Label($form->GetFieldSet(), "Lançe minimo:");
		$min_bid = new UI\Input($form->GetFieldSet());
		$min_bid->SetName("auction_min_bid");
		$min_bid->IsOnlyNumeric();
		
		new UI\Label($form->GetFieldSet(), "Começa em:");
		$begin = new UI\Input($form->GetFieldSet());
		$begin->IsDatepick();
		$begin->SetName("auction_begin");
		
		new UI\Label($form->GetFieldSet(), "Termina em:");
		$end = new UI\Input($form->GetFieldSet());
		$end->IsDatepick();
		$end->SetName("auction_end");
		
		$submit = new UI\Input($form->GetFieldSet());
		$submit->SetName("submit");			
		$submit->SetValue("Enviar");		
		$submit->IsButton();
		
		global $module;
		
		if($this->data["created"])
		{
			\Core\Main::sendMessageBox("Sucesso!", "O novo leilão foi criado com sucesso!");
		}
		
		$module = "
			{$this->saveHTML($form)}
		";		
	}
}