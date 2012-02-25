<?php
namespace Views\Auctions;
use Framework\Auctions;
use Framework\Player;
use Framework\UI;
class Additem extends \Core\Views
{
	function __construct($data)
	{
		parent::__construct($data);
		
		$form = new UI\Form($this);
		
		new UI\Label($form->GetFieldSet(), "Item ID:");
		$itemtype = new UI\Input($form->GetFieldSet());
		$itemtype->SetName("itemtype");
		$itemtype->SetId("search_value");
		$itemtype->EventOnKeyUp("requestSearchBoxItemName(this.value)");
		
		$focus = $form->GetFieldSet();
		$focus instanceof \DOMElement;
		
		$div = new \DOMElement("div");
		$focus->appendChild($div);
		$div->setAttribute("id", "search_suggestions");
		
		$child = new \DOMElement("div");
		$div->appendChild($child);
		$child->setAttribute("id", "search_suggestions_list");
		
		new UI\Label($form->GetFieldSet(), "Quantidade:");
		$count = new UI\Input($form->GetFieldSet());
		$count->SetName("item_count");
		$count->IsOnlyNumeric();
		
		new UI\Label($form->GetFieldSet(), "ActionID:");
		$action_id = new UI\Input($form->GetFieldSet());
		$action_id->SetName("item_actionid");
		$action_id->IsOnlyNumeric();
		
		$submit = new UI\Input($form->GetFieldSet());
		$submit->SetName("submit");			
		$submit->SetValue("Enviar");		
		$submit->IsButton();
		
		global $module;
		
		if($this->data["show_success"])
		{
			\Core\Main::sendMessageBox("Sucesso!", "O seu novo item para o leilão foi adicionado com sucesso!");
		}
		
		$module = "
			{$this->saveHTML($form)}
		";		
	}
}