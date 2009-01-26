<?
$post = $core->extractPost();
if($post)
{	
	$character = $core->loadClass("character");

	if(!$post[0] or !$post[1] or !$post[2])
	{
		$error = "Preencha todos campos do formulario corretamente.";
	}
	elseif(!$strings->canUseName($post[0]))
	{
		$error = "Este nome possui formatação ilegal. Tente novamente com outro nome.";
	}
	elseif($character->loadByName($post[0]))
	{
		$error = "Este nome já está em uso em nosso banco de dados. Tente novamente com outro nome.";
	}
	else
	{
		if($post[1] == "male")
			$outfitType = 128;
		else
			$outfitType = 136;
	
		$character->set("name", $post[0]);
		$character->set("account_id", $_SESSION['login'][0]);
		$character->set("group_id", 1);
		$character->set("sex", $_sex[$post[1]]);
		$character->set("vocation", $_vocation[$post[2]]);
		$character->set("experience", "4200");
		$character->set("level", "8");
		$character->set("maglevel", "0");
		$character->set("health", "185");
		$character->set("healthmax", "185");
		$character->set("mana", "35");
		$character->set("manamax", "35");
		$character->set("looktype", $outfitType);
		$character->set("conditions", null);
		$character->set("guildnick", "");
		$character->set("comment", "");
		
		$character->save();
	
		$success = "
		<p>O personagem ".$post[0]." foi criado com sucesso!</p>
		<p>Para iniciar o jogo basta baixar o nosso cliente na seção de Downloads!</p>
		<p>Tenha uma boa jornada!</p>
		";
	}
}

if($success)	
{
	$module .=	'
		
	<div id="sucesso">
		<h2>'.$success.'</h2>
	</div>
	
	';
}
else
{
	if($error)	
	{
		$module .=	'
		
		<div id="error">
			<h2>'.$error.'</h2>
		</div>
		
		';
	}

$module .= '
<form action="" method="post">
	<fieldset>
		
		<p>
			<label for="player_name">Nome</label><br />
			<input name="player_name" size="40" type="text" value="" />
		</p>
		
		<p>
			<label for="player_sex">Sexo</label><br />
			<ul>				
				<li><input type="radio" name="player_sex" value="female" /> Feminino</li>
				<li><input type="radio" name="player_sex" value="male" /> Masculino</li>
			</ul>	
		</p>		
		
		<p>
			<label for="player_sex">Vocação</label><br />
			<ul>				
				<li><input type="radio" name="player_vocation" value="sorcerer" /> Sorcerer</li>
				<li><input type="radio" name="player_vocation" value="druid" /> Druid</li>
				<li><input type="radio" name="player_vocation" value="paladin" /> Paladin</li>
				<li><input type="radio" name="player_vocation" value="knight" /> Knight</li>
			</ul>
		</p>			
		
		<div id="line1"></div>
		
		<p>
			<input type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>