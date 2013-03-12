<?php
if($_POST)
{
	if(!$_POST["fastnews_post"])
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
	}
	else
	{
		\Core\Main::$DB->query("INSERT INTO ".\Core\Tools::getSiteTable("fastnews")." (`author`, `post`, `post_data`, `post_update`) VALUES ('{$_SESSION['login'][0]}', '{$_POST['fastnews_post']}', '".time()."', '".time()."')");
		
		$success = "
		<p>A notícia rapida foi postada com sucesso!</p>
		";		
	}
}

if($success)	
{
	\Core\Main::sendMessageBox("Sucesso!", $success);
}
else
{
	if($error)	
	{
		\Core\Main::sendMessageBox("Erro!", $error);
	}
	
	$module .=	'
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>			
			
			<p>
				<label for="fastnews_post">Post</label><br />
				<textarea name="fastnews_post" rows="10" wrap="physical" cols="55"></textarea>
			</p>	

			<div id="line1"></div>
			
			<p>
				<input class="button" type="submit" value="Enviar" />
			</p>			

		</fieldset>
	</form>';	
}
?>