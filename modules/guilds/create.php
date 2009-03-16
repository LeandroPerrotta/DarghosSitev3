<?
$account = $core->loadClass("Account");
$account->load($_SESSION["login"][0], "password");

$list = $account->getCharacterList();

if($_POST)
{	
	$guild = $core->loadClass("guilds");
	
	$post = $core->extractPost();
	if(!in_array($post[1], $list))
	{
		$error = "Este personagem não pertence a sua conta.";
	}
	elseif($account->get("password") != $strings->encrypt($post[2]))
	{
		$error = "Confirmação da senha falhou.";
	}	
	elseif($guild->loadByName($post[0]))
	{
		$error = "Já existe uma guilda em nosso banco de dados registrada com este nome.";
	}	
	elseif($account->isGuildHighMember())
	{
		$error = "Só é permitido possuir 1 lider ou vice-lider por conta.";
	}	
	elseif(!$strings->canUseName($post[0]))
	{
		$error = "Este nome possui formatação ilegal. Tente novamente com outro nome.";
	}	
	else
	{
		$character = $core->loadClass("Character");
		$character->loadByName($post[1], "rank_id, guild_join_date");
		
		$guild->set("name", $post[0]);
		$guild->set("ownerid", $character->get("id"));
		$guild->set("image", "default_logo.gif");
		$guild->set("creation", time());
		$guild->set("formationTime", time() + 60 * 60 * 24 * GUILDS_FORMATION_DAYS);
		
		$guild->save();
		
		$guild->setRank("Lider", 1);
		$guild->setRank("Vice-Lider", 2);
		$guild->setRank("Membro", 3);
		
		$guild->loadRanks();
		$ranks = $guild->getRanks();
		
		foreach($ranks as $rank_id => $values)
		{
			if($values['level'] == 1)
				$leader_id = $rank_id;
		}
		
		$character->set("guild_join_date", time());
		$character->set("rank_id", $leader_id);
		$character->save();
		
		$success = "
		<p>A guilda ".$post[0]." foi criada com sucesso!</p>
		<p>Inicialmente a sua guilda está em estagio de formação, e você deve nomear ao minimo ".GUILDS_VICELEADERS_NEEDED." vice-lideres em ".GUILDS_FORMATION_DAYS." dias para que sua guilda seja formada! Caso contrario a guilda será automaticamente desbandada.</p>
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


$module .=	'
	<form action="" method="post">
		<fieldset>
			
			<p>
				<label for="guild_name">Nome da Guild</label><br />
				<input id="guild_name" name="guild_name" size="40" type="text" value="" />
			</p>
			
			<p>
				<label for="guild_owner">Personagem Lider</label><br />
				<select name="guild_owner">
					';

				if(is_array($list))
				{	
					foreach($list as $pid)
					{
						$module .=	'<option value="'.$pid.'">'.$pid.'</option>';
					}
				}

			$module .=	'
				</select>
			</p>			
			
			<p>
				<label for="account_password">Senha</label><br />
				<input id="account_password" name="account_password" size="40" type="password" value="" />
			</p>			
			
			<div id="line1"></div>
			
			<p>
				<input type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';

}
?>	
