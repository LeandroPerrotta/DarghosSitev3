<?

$emailmarketing = $core->getGlobalValue("emailmarketing");

if(!$emailmarketing)
{
	$core->setGlobalValue("emailmarketing", 1);
}
else
{
	$core->setGlobalValue("emailmarketing", (int)$emailmarketing + 1);
}

$core->redirect("index.php");	
?>