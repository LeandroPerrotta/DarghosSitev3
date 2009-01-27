<?
$post = $core->extractPost();
$get = $_GET['name'];

if($post or $get)
{	
	$name = ($post) ? $post[0] : $get;

	$character = $core->loadClass("character");
	
	if(!$character->loadByName($name, "name, level, sex, vocation, town_id, lastlogin"))
	{	
		$error = "Este personagem não existe.";
	}
	else
	{	
		$lastlogin = ($character->get("lastlogin")) ? $core->formatDate($character->get("lastlogin")) : "Nunca entrou.";
	
		$module .= '
		<ul id="pagelist">
			<p>Personagem:</p>
			
			<li><b>Nome:</b> '.$character->get("name").'</li>
			<li><b>Level:</b> '.$character->get("level").'</li>
			<li><b>Sexo:</b> '.$_sexid[$character->get("sex")].'</li>
			<li><b>Vocação:</b> '.$_vocationid[$character->get("vocation")].'</li>
			<li><b>Residencia:</b> '.$_townid[$character->get("town_id")].'</li>
			<li><b>Último Login:</b> '.$lastlogin.'</li>
		</ul>	
		
		<p id="line1"></p>
		';
	}
}


if($error)	
{
	$module .=	'
	
	<div id="error">
		<h2>'.$error.'</h2>
	</div>
	
	';
}

$module .= '
<form action="?ref=character.view" method="post">
	<fieldset>
		
		<p>
			<label for="player_name">Nome</label><br />
			<input name="player_name" size="40" type="text" value="" />
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';
?>