<?php
use \Core\Consts;
$pageDb = new \Core\PagesDB();

$pageDb->load(Consts::PAGE_ABOUT);

if(!$_SESSION['login'])
{
	$pageDb->printContent();
}
if($_SESSION['login'])
{	
	$account = new \Framework\Account();
	$account->load($_SESSION['login'][0]);
	
	if($account->getGroup() >= t_Group::CommunityManager)
	{
		if($_POST)
		{
			$pageDb->setContent($_POST['Page']);
			$pageDb->save(Consts::PAGE_ABOUT);
			
			\Core\Main::sendMessageBox("Sucesso!", "A pagina foi editada com sucesso!");
			$pageDb->load(Consts::PAGE_ABOUT);
		}		
		
		$module .= "<br><form action='{$_SERVER['REQUEST_URI']}' method='post'>";
		
		$FCKEditor = \Core\Main::FCKEditor("Page");
		
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