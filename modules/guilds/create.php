<?
$account = $core->loadClass("Account");
$account->load($_SESSION["login"][0], "password");

$list = $account->getCharacterList();

if($_POST)
{	
	$guild = $core->loadClass("guilds");
	$character = $core->loadClass("Character");
	$character->loadByName($_POST["guild_owner"], "rank_id, guild_join_date");
		
	if(!in_array($_POST["guild_owner"], $list))
	{
		$error = "Este personagem não pertence a sua conta.";
	}
	elseif($account->get("password") != $strings->encrypt($_POST["account_password"]))
	{
		$error = "Confirmação da senha falhou.";
	}	
	elseif($guild->loadByName($_POST["guild_name"]))
	{
		$error = "Já existe uma guilda em nosso banco de dados registrada com este nome.";
	}	
	elseif($account->isGuildHighMember())
	{
		$error = "Só é permitido possuir 1 lider ou vice-lider por conta.";
	}	
	elseif(!$strings->canUseName($_POST["guild_name"]))
	{
		$error = "Este nome possui formatação ilegal. Tente novamente com outro nome.";
	}
	elseif($character->loadGuild())
	{
		$error = "Seu personagem ja possui guild, é nescessario sair da mesma para criar outra.";
	}
	else
	{
		
		$guild->set("name", $_POST["guild_name"]);
		$guild->set("ownerid", $character->get("id"));
		$guild->set("motd", "");
		$guild->set("image", "default_logo.gif");
		$guild->set("creationdata", time());
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
		<p>A guilda ".$_POST["guild_name"]." foi criada com sucesso!</p>
		<p>Inicialmente a sua guilda está em estagio de formação, e você deve nomear ao minimo ".GUILDS_VICELEADERS_NEEDED." vice-lideres em ".GUILDS_FORMATION_DAYS." dias para que sua guilda seja formada! Caso contrario a guilda será automaticamente desbandada.</p>
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
				<input class="button" type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';

}
?>	
