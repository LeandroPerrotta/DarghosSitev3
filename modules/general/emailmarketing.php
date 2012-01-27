<?

$emailmarketing = \Core\Main::getGlobalValue("emailmarketing");

if(!$emailmarketing)
{
	\Core\Main::setGlobalValue("emailmarketing", 1);
}
else
{
	\Core\Main::setGlobalValue("emailmarketing", (int)$emailmarketing + 1);
}

\Core\Main::redirect("index.php");	
?>