<?php
$pageDb = new PagesDB();

$pageDb->load(DBPAGES_ABOUT);

if(!$_SESSION['login'])
{
	$pageDb->printContent();
}
if($_SESSION['login'])
{	
	$account = new Account();
	$account->load($_SESSION['login'][0]);
	
	if($account->getGroup() >= GROUP_COMMUNITYMANAGER)
	{
		if($_POST)
		{
			$pageDb->setContent($_POST['Page']);
			$pageDb->save(DBPAGES_ABOUT);
			
			Core::sendMessageBox("Sucesso!", "A pagina foi editada com sucesso!");
			$pageDb->load(DBPAGES_ABOUT);
		}		
		
		$module .= "<br><form action='{$_SERVER['REQUEST_URI']}' method='post'>";
		
		$FCKEditor = Core::FCKEditor("Page");
		
		$FCKEditor->BasePath = "libs/fckeditor/";
		$FCKEditor->Height = "800";
		$FCKEditor->Value = $pageDb->getContent();
		$module .= $FCKEditor->CreateHtml();
		
		$module .= "</form>";
	}
	else
		$pageDb->printContent();
}
?>