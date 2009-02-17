<?php
if(isset($_POST['character_name']))
{
	$core->redirect("?ref=character.itemshop&name={$_POST['character_name']}");
}

$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "password, premdays, lastday");

$list = $account->getCharacterList();

if($_GET['name'])
{
	if(in_array($_GET['name'], $list))
	{
		$character = $core->loadClass("character");
		$character->loadByName($_GET['name'], "name, online");
		
		$post = $core->extractPost();
		if($post)
		{
			$itemshop_list = $core->loadClass("itemshop_list");
			$query = $db_tenerian->query("SELECT id FROM ".DB_WEBSITE_PREFIX."itemshop WHERE player_id = '{$character->get("id")}' AND received = '0'");
			
			if($account->get("password") != $strings->encrypt($post[1]))
			{
				$error = "Confirmação da senha falhou.";
			}	
			elseif($query->numRows() != 0)
			{
				$error = "Você deve efetuar um login no jogo para receber o item de sua ultima compra antes de efetuar uma nova compra.";
			}
			elseif($character->get("online") != 0)		
			{
				$error = "É nessario estar off-line no jogo para efetuar a compra de um item.";
			}			
			elseif(!$itemshop_list->load($post[0]))		
			{
				$error = "Este item não existe.";
			}
			elseif($itemshop_list->get("cost") > $account->get("premdays"))
			{
				$error = "Você não possui os {$itemshop_list->get("cost")} dias de conta premium necessarios para obter este item.";
			}
			else
			{
				$itemshop = $core->loadClass("itemshop");
				
				$itemshop->set("player_id", $character->get("id"));
				$itemshop->set("itemlist_id", $itemshop_list->get("id"));
				$itemshop->set("time", time());
				$itemshop->set("account_id", $_SESSION['login'][0]);
				
				$itemshop->save();
				
				$db_tenerian->query("INSERT INTO player_storage (`player_id`, `key`, `value`) values('{$character->get("id")}', '".STORAGE_ID_ITEMSHOP."', '{$db_tenerian->lastInsertId()}')");
				
				$newpremdays = $account->get("premdays") - $itemshop_list->get("cost");
				
				$account->set("premdays", $newpremdays);
				$account->set("lastday", time());
				
				$account->save();
				
				$success = "
				<p>Caro jogador,</p>
				<p>A compra de {$itemshop_list->get("count")}x {$itemshop_list->get("name")} por {$itemshop_list->get("cost")} dias de sua conta premium foi efetuada com sucesso!</p>
				<p>O seu item estará em sua backpack principal no proximo log-in.</p>
				<p>Tenha um bom jogo!</p>
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
		
		$query = $db_tenerian->query("SELECT * FROM ".DB_WEBSITE_PREFIX."itemshop_list WHERE actived = '1' ORDER BY time DESC");	
			
		$module .=	'
			<form action="" method="post">
				<fieldset>

					<p>
						Seja bem vindo ao Darghos Item Shop. Aqui você pode obter um item no jogo em troca de dias de sua conta premium. Após concluir a compra o item será criado na sua backpack principal no proximo log-in dentro do jogo, em um processo completamente automatico. Tenha um bom jogo!
					</p>				
				
					<p>
						<label for="character_name">Personagem a receber o Item</label><br />
						<input disabled="disabled" size="40" type="text" value="'.$_GET['name'].'" />
					</p>
					
					<p>
					
					<table cellspacing="0" cellpadding="0" id="table">
						<tr>
							<th width="3%">&nbsp </th> <th width="3%">&nbsp </th> <th width="30%">Item </th> <th width="45%">Descrição </th> <th>Preço </th>
						</tr>					
					';
					
					
					if($fetch = $query->fetch())
					{
						$module .=	'
							<tr>
								<td><input name="itemshop_id" type="radio" value="'.$fetch->id.'"> <td><img src="'.$fetch->url.'"></img></td> <td>'.$fetch->count.'x '.$fetch->name.'</td> <td>'.$fetch->description.'</td> <td>'.$fetch->cost.' dias</td>
							</tr>							
						';
					}
		
					$module .=	'
					</table>
					</p>
					
					<p>
						<label for="account_password">Senha</label><br />
						<input name="account_password" size="40" type="password" value="" />
					</p>						
					
					<div id="line1"></div>
					
					<p>
						<input type="submit" value="Enviar" />
					</p>
				</fieldset>
			</form>';	
		}
	}
	else
	{			
		$module .=	'
		
		<div id="error">
			<h2>Este personagem não existe ou não é de sua conta.</h2>
		</div>
		
		';		
	}
}
else
{

$module .=	'
	<form action="" method="post">
		<fieldset>
			
			<p>
				<b>Obs:</b> Para visualizar o historico de compras de items de sua conta clique <a href="?ref=account.itemshop_log">aqui</a>.
			</p>
		
			<p>
				<label for="account_email">Personagem</label><br />
				<select name="character_name">
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
			
			<div id="line1"></div>
			
			<p>
				<input type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';
}			
?>