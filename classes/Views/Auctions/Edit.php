<?php
namespace Views\Auctions;
use Framework\Auctions;
use Framework\Player;
use Framework\UI;
class Edit extends \Core\Views
{
	function __construct($data)
	{
		parent::__construct($data);
		
		$form = new UI\Form($this);
		
		new UI\Label($form->GetFieldSet(), "Titulo:");
		$title = new UI\Input($form->GetFieldSet());
		$title->SetName("auction_title");
		if($data[$title->GetName()]);
			$title->SetValue($data[$title->GetName()]);
			
		new UI\Label($form->GetFieldSet(), "Descrição:");
		$description = new UI\TextArea($form->GetFieldSet());
		$description->SetName("auction_description");
		if($data[$description->GetName()]);
			$description->SetText($data[$description->GetName()]);		
		
		new UI\Label($form->GetFieldSet(), "Lançe minimo:");
		$min_bid = new UI\Input($form->GetFieldSet());
		$min_bid->SetName("auction_min_bid");
		$min_bid->IsOnlyNumeric();
		if($data[$min_bid->GetName()]);
			$min_bid->SetValue($data[$min_bid->GetName()]);		
		
		new UI\Label($form->GetFieldSet(), "Começa em:");
		$begin = new UI\Input($form->GetFieldSet());
		$begin->IsDatepick();
		$begin->SetName("auction_begin");
		if($data[$begin->GetName()]);
			$begin->SetValue($data[$begin->GetName()]);		
		
		new UI\Label($form->GetFieldSet(), "Termina em:");
		$end = new UI\Input($form->GetFieldSet());
		$end->IsDatepick();
		$end->SetName("auction_end");
		if($data[$end->GetName()]);
			$end->SetValue($data[$end->GetName()]);		
		
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