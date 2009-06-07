<?
$post = $core->extractPost();

$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "premdays");	

if($post)
{	
	$character = $core->loadClass("character");

	if(!$post[0] or !$post[1] or !$post[2] or !$post[3])
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
	
		foreach($_townid as $city_id => $city_name)
		{
			if(strtolower($city_name) == $post[3])
				$city = $city_id;
		}
		
		if($city)
		{
			if(($city == 2 or $city == 5 or $city == 7) and $account->getPremDays() == 0)
			{
				$city = 1;
			}
		}
		else
			$city = 1;
		
		$character->setName($post[0]);
		$character->setAccountId($_SESSION['login'][0]);
		$character->setGroup(1);
		$character->setSex($_sex[$post[1]]);
		$character->setVocation($_vocation[$post[2]]);
		$character->setExperience(4200);
		$character->setLevel(8);
		$character->setMagLevel(0);
		$character->setHealth(185);
		$character->setMana(35);
		$character->setCap(470);
		$character->setTownId($city);
		$character->setLookType($outfitType);
		$character->setConditions(null);
		$character->setGuildNick("");
		$character->setRankId(0);
		$character->setDescription("");
		$character->setCreation(time());
		
		$character->save();
	
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
	$core->sendMessageBox("Sucesso!", $success);
}
else
{
	if($error)	
	{
		$core->sendMessageBox("Erro!", $error);
	}
	
$module .= '
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
	<fieldset>
		
		<p>
			<label for="player_name">Nome</label><br />
			<input name="player_name" size="40" type="text" value="" />
		</p>
		
		<p>
			<label for="player_sex">Sexo</label><br />			
				<input type="radio" name="player_sex" value="female" /> Feminino<br>
				<input type="radio" name="player_sex" value="male" /> Masculino<br>
		</p>		
		
		<p>
			<label for="player_sex">Vocação</label><br />			
				<input type="radio" name="player_vocation" value="sorcerer" /> Sorcerer<br>
				<input type="radio" name="player_vocation" value="druid" /> Druid<br>
				<input type="radio" name="player_vocation" value="paladin" /> Paladin<br>
				<input type="radio" name="player_vocation" value="knight" /> Knight<br>
		</p>	

		<p>
			<label for="player_city">Residencia</label><br />';

				foreach($_townid as $city_id => $values)
				{
					if($values['canCreate'] == 1)
					{
						if($values['premium'] == 1 and $account->getPremDays() != 0)
							$module .= '<input type="radio" name="player_city" value="'.$values['name'].'" /> '.$values['name'].'<br>';
						elseif($values['premium'] == 0)
							$module .= '<input type="radio" name="player_city" value="'.$values['name'].'" /> '.$values['name'].'<br>';	
					}	
				}			
				
			$module .= '
		</p>		
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>