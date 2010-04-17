<?php
if(isset($_POST['character_name']))
{
	Core::redirect("?ref=character.itemshop&name={$_POST['character_name']}");
}

$account = new Account();
$account->load($_SESSION['login'][0]);

$list = $account->getCharacterList();

if($_GET['name'])
{
	if(in_array($_GET['name'], $list))
	{
		$character = new Character();
		$character->loadByName($_GET['name']);
		
		if($_POST)
		{
			$itemshop_list = new ItemShop_List();
			$query = $db->query("SELECT value FROM player_storage WHERE `key` = '".STORAGE_SHOPSYS_ID."' AND `player_id` = '{$character->get("id")}'");		
			$fetch = $query->fetch();
			
			if($account->getPassword() != Strings::encrypt($_POST["account_password"]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}	
			elseif($query->numRows() != 0 && $fetch->value == 0)
			{
				$error = Lang::Message(LMSG_REPORT);
			}		
			elseif($query->numRows() != 0 && $fetch->value > 0)
			{
				$error = Lang::Message(LMSG_ITEMSHOP_OLD_PURCHASE);
			}			
			elseif($character->getOnline() == 1)		
			{
				$error = Lang::Message(LMSG_CHARACTER_NEED_OFFLINE);
			}			
			elseif(!$itemshop_list->load($_POST["itemshop_id"]))		
			{
				$error = Lang::Message(LMSG_REPORT);
			}
			elseif($itemshop_list->get("cost") > $account->getPremDays())
			{
				$error = Lang::Message(LMSG_ITEMSHOP_COST, $itemshop_list->get("cost"));
			}
			elseif($account->get("type") > 2 AND $account->get("type") < 5)
			{
				$error = Lang::Message(LMSG_REPORT);
			}			
			else
			{
				$itemshop = new ItemShop();
				
				$itemshop->set("player_id", $character->get("id"));
				$itemshop->set("itemlist_id", $itemshop_list->get("id"));
				$itemshop->set("time", time());
				$itemshop->set("account_id", $_SESSION['login'][0]);
				
				$itemshop->save();
				
				$storage_query = $db->query("SELECT `key` FROM `player_storage` WHERE `player_id` = '{$character->getId()}' AND `key` = '".STORAGE_SHOPSYS_ID."'");
				
				if($storage_query->numRows() == 0)
					$db->query("INSERT INTO player_storage (`player_id`, `key`, `value`) values('{$character->get("id")}', '".STORAGE_SHOPSYS_ID."', '{$db->lastInsertId()}')");
				else
					$db->query("UPDATE `player_storage` SET `value` = '{$db->lastInsertId()}' WHERE `player_id` = '{$character->get("id")}' AND `key` = '".STORAGE_SHOPSYS_ID."'");
				
				$db->query("DELETE FROM player_storage WHERE `player_id` = '{$character->get("id")}' AND `key` = '".STORAGE_SHOPSYS_ITEM_ID."'");
				$db->query("DELETE FROM player_storage WHERE `player_id` = '{$character->get("id")}' AND `key` = '".STORAGE_SHOPSYS_ITEM_COUNT."'");
				
				$db->query("INSERT INTO player_storage (`player_id`, `key`, `value`) values('{$character->get("id")}', '".STORAGE_SHOPSYS_ITEM_ID."', '{$itemshop_list->get("item_id")}')");
				$db->query("INSERT INTO player_storage (`player_id`, `key`, `value`) values('{$character->get("id")}', '".STORAGE_SHOPSYS_ITEM_COUNT."', '{$itemshop_list->get("count")}')");
							
				$account->updatePremDays($itemshop_list->get("cost"), false /* false to decrement days */);
				
				$account->save();
				
				$success = Lang::Message(LMSG_ITEMSHOP_PURCHASE_SUCCESS, $itemshop_list->get("count"), $itemshop_list->get("name"), $itemshop_list->get("cost"));
			}					
		}
		
		if($success)	
		{
			Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $success);
		}
		else
		{
			if($error)	
			{
				Core::sendMessageBox(Lang::Message(LMSG_ERROR), $error);
			}
		
		$query = $db->query("SELECT * FROM ".DB_WEBSITE_PREFIX."itemshop_list WHERE actived = '1' ORDER BY time DESC");	
			
		$module .=	'
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset>

					<p>
						Seja bem vindo ao '.CONFIG_SITENAME.' Item Shop. Aqui você pode obter um item no jogo em troca de dias de sua conta premium. Após concluir a compra o item será criado na sua backpack principal no proximo log-in dentro do jogo, em um processo completamente automatico. Tenha um bom jogo!
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
					
					
					while($fetch = $query->fetch())
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
						<input class="button" type="submit" value="Enviar" />
					</p>
				</fieldset>
			</form>';	
		}
	}
	else
	{		
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), Lang::Message(LMSG_CHARACTER_NOT_FROM_YOUR_ACCOUNT));
	}
}
else
{

$module .=	'
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
			
			<p>
				<b>Obs:</b> Para visualizar o historico de compras de items de sua conta clique <a href="?ref=account.itemshop_log">aqui</a>.
			</p>
		
			<p>
				<label for="account_email">Personagem</label><br />
				<select name="player_name">
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
				<input class="button" type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';
}			
?>