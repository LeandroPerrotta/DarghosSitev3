<?
$post = $core->extractPost();

$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "premdays");	

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
		$character->setTownId(6);
		$character->setLookType($outfitType);
		$character->setConditions(null);
		$character->setGuildNick("");
		$character->setRankId(0);
		$character->setDescription("");
		$character->setCreation(time());
		
		$character->save();
	
		$success = "
		<p>O personagem ".$post[0]." foi criado com sucesso!</p>
		<p>Para começar a jogar clique <a href='?ref=general.howplay'>aqui</a> e siga as instruções.</p>
		<p>A sua aventura se inicia em Island of Peace, esta ilha funciona como um aprendizado com vários tipos de criaturas, NPCs, quests, academia de treino e muito mais, alem que não é possivel atacar outros jogadores. Quando você atingir o nivel 60 estará preparado para sair da ilha usando o Barco e explorar aos outros continentes do Darghos. É importante informar que você pode sair a qualquer momento da ilha independente do nivel, porem, uma vez fora, é impossivel retornar a ilha.</p>
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
		
		<div id="line1"></div>
		
		<p>
			<input class="button" type="submit" value="Enviar" />
		</p>
	</fieldset>
</form>';

}
?>