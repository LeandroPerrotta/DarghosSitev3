<?
$post = $core->extractPost();
$get = $_GET['name'];

if($post or $get)
{	
	$name = ($post) ? $post[0] : $get;

	$character = $core->loadClass("character");
	
	if(!$character->loadByName($name, "name, account_id, level, sex, vocation, town_id, lastlogin, comment, hide"))
	{	
		$error = "Este personagem não existe.";
	}
	else
	{	
		$account = $core->loadClass("Account");
		$account->load($character->get("account_id"), "premdays, real_name, location, url");
		
		$houseid = $character->getHouse();
		$lastlogin = ($character->get("lastlogin")) ? $core->formatDate($character->get("lastlogin")) : "Nunca entrou.";
		
		$premium = ($account->get("premdays") != 0) ? "<font style='color: green; font-weight: bold;'>Conta Premium" : "Conta Gratuita";	
		$realname = ($account->get("real_name") != "") ? $account->get("real_name") : "não configurado";
		$location = ($account->get("location") != "") ? $account->get("location") : "não configurado";
		$url = ($account->get("url") != "") ? $account->get("url") : "não configurado";
		
		$deathlist = $character->loadLastDeaths();
		$list = $account->getCharacterList();
	
		$module .= "
		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th colspan='2'>Personagem</th>
			</tr>";		

			if($character->deletionStatus())
			{		
				$module .= "
				<tr>
					<td colspan='2'><font style='color: red; font-weight: bold;'>Este personagem está agendado para ser deletado no dia {$core->formatDate($character->deletionStatus())}.</font></td>
				</tr>";				
			}
		
			$module .= "
			<tr>
				<td width='15%'><b>Nome:</b></td> <td>{$character->get("name")}</td>
			</tr>
			
			<tr>
				<td><b>Level:</b></td> <td>{$character->get("level")}</td>
			</tr>	

			<tr>
				<td><b>Sexo:</b></td> <td>{$_sexid[$character->get("sex")]}</td>
			</tr>	

			<tr>
				<td><b>Vocação:</b></td> <td>{$_vocationid[$character->get("vocation")]}</td>
			</tr>	

			<tr>
				<td><b>Residencia:</b></td> <td>{$_townid[$character->get("town_id")]}</td>
			</tr>";	

			if($houseid)
			{
				$houses = $core->loadClass("Houses");
				$houses->load($houseid);				
				
				$module .= "
				<tr>
					<td><b>Casa</b></td> <td>{$houses->get("name")} ({$_townid[$houses->get("townid")]}) com pagamento no dia  {$core->formatDate($houses->get("paid"))}</td>
				</tr>";						
			}
			
			if($character->get("comment"))
			{
				$module .= "
				<tr>
					<td><b>Comentario</b></td> <td>".nl2br(strip_tags($character->get("comment")))."</td>
				</tr>";					
			}
			
			$module .= "
			<tr>
				<td><b>Último Login:</b></td> <td>{$lastlogin}</td>
			</tr>	
			
		</table>

		<table cellspacing='0' cellpadding='0' id='table'>
			<tr>
				<th colspan='2'>Informações da Conta</th>
			</tr>		

			<tr>
				<td width='15%'><b>Tipo de Conta:</b></td> <td>{$premium}</td>
			</tr>	
			
			<tr>
				<td><b>Nome Real:</b></td> <td>{$realname}</td>
			</tr>	

			<tr>
				<td><b>Location:</b></td> <td>{$location}</td>
			</tr>	

			<tr>
				<td><b>Website:</b></td> <td>{$url}</td>
			</tr>				
		</table>";

		if(is_array($deathlist))
		{
			$module .= "
			
			<table cellspacing='0' cellpadding='0' id='table'>
				<tr>
					<th>Mortes Recentes</th>
				</tr>					
			";
			
			foreach($deathlist as $i => $values)
			{
				$killer = ($values['killed_by'] != "-1") ? $values['killed_by'] : "field";
				$time = $core->formatDate($values['time']);
				
				if($values['is_player'] == "0")
				{
					$death = "Morto no nivel {$values['level']} por um {$killer} em {$time}.";
				}
				else
				{
					$death = "Morto no nivel {$values['level']} por <a href='?ref=character.view&name={$killer}'>{$killer}</a> em {$time}.";
				}
				
				$module .= "
					<tr>
						<td>{$death}</td>
					</tr>					
				";	
			}
			
			$module .= "
			</table>";		
		}
		
		if($character->get("hide") == 0)
		{
			$module .= "
			<table cellspacing='0' cellpadding='0' id='table'>
				<tr>
					<th colspan='3'>Outros Personagens</th>
				</tr>					
			";			
			
			foreach($list as $player_name)
			{
				$character_list = $core->loadClass("character");
				$character_list->loadByName($player_name, "name, level, online, hide");
				
				if($character_list->get("hide") == 0)
				{
					$character_status = ($character_list->get("online") == 1) ? "<font style='color: green; font-weight: bold;'>On-line</font>" : "<font style='color: red; font-weight: bold;'>Off-line</font>";
					
					$module .= "
						<tr>
							<td width='25%'>{$character_list->get("name")}</td> <td width='10%'>{$character_list->get("level")}</td> <td>{$character_status}</td>
						</tr>					
					";						
				}				
			}

			$module .= "
			</table>";		
		}
		
		
		$module .= "
		<p id='line1'></p>
		";
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