<?php 
use \Core\Configs;
use \Core\Main;

$file = $_GET["file"];

if($file && file_exists(Configs::Get(Configs::eConf()->WEBSITE_FOLDER_FILES) . $file))
{
	Main::redirect(Configs::Get(Configs::eConf()->WEBSITE_FOLDER_FILES) . $file, true, true);
	
	$module .= "
		<p>O seu download iniciará automaticamente em 5 segundos. Se por algum motivo o download não iniciar, clique <a href='".Configs::Get(Configs::eConf()->WEBSITE_FOLDER_FILES) . $file."'>aqui</a>.</p>
	";
}
else
	$module .= "
	<p>Este arquivo para download não disponivel.</p>
	";	

?>