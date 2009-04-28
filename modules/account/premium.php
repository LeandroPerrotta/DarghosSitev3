<?php
$pageDb = $core->loadClass("pagesdb");

$pageDb->load(DBPAGES_PREMIUMFEATURES);

if(!$_SESSION['login'])
{
	$pageDb->printContent();
}
if($_SESSION['login'])
{	
	$account = $core->loadClass("account");
	$account->load($_SESSION['login'][0]);
	
	if($account->getGroup() >= 5)
	{
		if($_POST)
		{
			$pageDb->setContent($_POST['Page']);
			$pageDb->save(DBPAGES_PREMIUMFEATURES);
			
			$core->sendMessageBox("Sucesso!", "A pagina foi editada com sucesso!");
			$pageDb->load(DBPAGES_PREMIUMFEATURES);
		}		
		
		$module .= "<br><form action='' method='post'>";
		
		$FCKEditor = $core->FCKEditor("Page");
		
		$FCKEditor->BasePath = "libs/fckeditor/";
		$FCKEditor->Height = "800";
		$FCKEditor->Value = $pageDb->getContent();
		$module .= $FCKEditor->CreateHtml();
		
		$module .= "</form>";
	}
	else
		$pageDb->printContent();
}

$module .= "
<p>
	<a class='buttonstd' href='?ref=contribute.order'>Proximo</a>
</p>
";
?>