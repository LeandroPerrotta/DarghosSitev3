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
			
		if($post[2] == "sorcerer")
		{
			$itemsChar = array(
				//Inventario
				array(SLOT_HEAD, 101, 2480, 1),
				array(SLOT_BACKPACK, 102, 1988, 1),
				array(SLOT_ARMOR, 103, 2464, 1),
				array(SLOT_RIGHTHAND, 104, 2530, 1),
				array(SLOT_LEFTHAND, 105, 2190, 1),
				array(SLOT_LEGS, 106, 2468, 1),
				array(SLOT_FEET, 107, 2643, 1),
				array(SLOT_AMMO, 108, 2120, 1),
				
				//backpack
				array(102, 109, 2666, 2),
			);				
		}
		elseif($post[2] == "druid")
		{
			$itemsChar = array(
				//Inventario
				array(SLOT_HEAD, 101, 2480, 1),
				array(SLOT_BACKPACK, 102, 1988, 1),
				array(SLOT_ARMOR, 103, 2464, 1),
				array(SLOT_RIGHTHAND, 104, 2530, 1),
				array(SLOT_LEFTHAND, 105, 2182, 1),
				array(SLOT_LEGS, 106, 2468, 1),
				array(SLOT_FEET, 107, 2643, 1),
				array(SLOT_AMMO, 108, 2120, 1),
				
				//backpack
				array(102, 109, 2666, 2),
			);						
		}
		elseif($post[2] == "paladin")
		{
			$itemsChar = array(
				//Inventario
				array(SLOT_HEAD, 101, 2480, 1),
				array(SLOT_BACKPACK, 102, 1988, 1),
				array(SLOT_ARMOR, 103, 2464, 1),
				array(SLOT_RIGHTHAND, 104, 2530, 1),
				array(SLOT_LEFTHAND, 105, 2389, 5),
				array(SLOT_LEGS, 106, 2468, 1),
				array(SLOT_FEET, 107, 2643, 1),
				array(SLOT_AMMO, 108, 2120, 1),
				
				//backpack
				array(102, 109, 2666, 2),
			);						
		}
		elseif($post[2] == "knight")
		{
			$itemsChar = array(
				//Inventario
				array(SLOT_HEAD, 101, 2480, 1),
				array(SLOT_BACKPACK, 102, 1988, 1),
				array(SLOT_ARMOR, 103, 2464, 1),
				array(SLOT_RIGHTHAND, 104, 2530, 1),
				array(SLOT_LEFTHAND, 105, 2412, 1),
				array(SLOT_LEGS, 106, 2468, 1),
				array(SLOT_FEET, 107, 2643, 1),
				array(SLOT_AMMO, 108, 2120, 1),
				
				//backpack
				array(102, 109, 2666, 2),
				array(102, 110, 2388, 1),
				array(102, 111, 2398, 1),
			);					
		}		
	
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
		$character->set("cap", "470");
		$character->set("looktype", $outfitType);
		$character->set("conditions", null);
		$character->set("guildnick", "");
		$character->set("comment", "");
		
		$character->save();
	
		$character->loadByName($post[0]);
	
		foreach($itemsChar as $item)
		{
			$character->addItem($item[0], $item[1], $item[2], $item[3]);
		}	
	
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
			<ul id="pagelist">				
				<li><input type="radio" name="player_sex" value="female" /> Feminino</li>
				<li><input type="radio" name="player_sex" value="male" /> Masculino</li>
			</ul>	
		</p>		
		
		<p>
			<label for="player_sex">Vocação</label><br />
			<ul id="pagelist">				
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