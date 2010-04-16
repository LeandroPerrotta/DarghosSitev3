<?

$emailmarketing = Core::getGlobalValue("emailmarketing");

if(!$emailmarketing)
{
	Core::setGlobalValue("emailmarketing", 1);
}
else
{
	Core::setGlobalValue("emailmarketing", (int)$emailmarketing + 1);
}

Core::redirect("index.php");	
?>