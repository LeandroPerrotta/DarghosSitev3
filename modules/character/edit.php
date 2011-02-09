<?php
if(isset($_POST['character_name']))
{
	Core::redirect("?ref=character.edit&name={$_POST['character_name']}");
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
			if($account->get("password") != Strings::encrypt($_POST["account_password"]))
			{
				$error = Lang::Message(LMSG_WRONG_PASSWORD);
			}			
			elseif($_POST["edit_action"] == "edit_information")
			{			
				if(strlen($_POST["character_comment"]) > 500)
				{
					$error = Lang::Message(LMSG_CHARACTER_COMMENT_WRONG_SIZE);
				}
				else
				{		
					$hide = ($_POST["character_hide"] == 1) ? "1" : "0";
					$character->setComment($_POST["character_comment"]);
					$character->setHidden($hide);
					
					$character->save();
					
					$success = Lang::Message(LMSG_CHARACTER_COMMENT_CHANGED);
				}
			}
			elseif($_POST["edit_action"] == "edit_name")
			{	
				if(SHOW_SHOPFEATURES != 0)
				{
					$account = $character->loadAccount();
					
					$newname_character = new Character();
					
					if(!$_POST["character_newname"])
					{
						$error = Lang::Message(LMSG_FILL_FORM);
					}			
					elseif(!$_POST["confirm_changename"])
					{
						$error = Lang::Message(LMSG_CHARACTER_CHANGE_THING_CONFIRM);
					}			
					elseif(!Strings::canUseName($_POST["character_newname"]))
					{
						$error = Lang::Message(LMSG_WRONG_NAME);
					}
					elseif($newname_character->loadByName($_POST["character_newname"]))
					{
						$error = Lang::Message(LMSG_CHARACTER_NAME_ALREADY_USED);
					}	
					elseif($character->getOnline() == 1)	
					{
						$error = Lang::Message(LMSG_CHARACTER_NEED_OFFLINE);
					}			
					elseif($account->getPremDays() < PREMDAYS_TO_CHANGENAME)
					{
						$error = Lang::Message(LMSG_CHARACTER_PREMDAYS_COST, PREMDAYS_TO_CHANGENAME);
					}
					else
					{		
						$oldName = $character->getName();
						
						$character->set("name", $_POST["character_newname"]);
						$character->save();
								
						//remove premdays da conta do jogador
						$account->updatePremDays(PREMDAYS_TO_CHANGENAME, false /* false to decrement days */);						
						$account->save();				
						
						$db->query("INSERT INTO ".DB_WEBSITE_PREFIX."changelog (`type`,`player_id`,`value`,`time`) values ('name','{$character->get("id")}','{$_POST["character_newname"]}','".time()."')");
						
						$success = Lang::Message(LMSG_CHARACTER_NAME_CHANGED, $oldName, $_POST["character_newname"]);
					}
				}			
			}
			elseif($_POST["edit_action"] == "edit_sex")
			{	
				if(SHOW_SHOPFEATURES != 0)
				{				
					$account = $character->loadAccount();	
					
					if(!$_POST["confirm_changesex"])
					{
						$error = Lang::Message(LMSG_CHARACTER_CHANGE_THING_CONFIRM);
					}	
					elseif($account->get("type") > 2 AND $account->get("type") < 5)
					{
						$error = Lang::Message(LMSG_REPORT);
					}						
					elseif($character->getOnline() == 1)
					{
						$error = Lang::Message(LMSG_CHARACTER_NEED_OFFLINE);
					}			
					elseif($account->getPremDays() < PREMDAYS_TO_CHANGESEX)
					{
						$error = Lang::Message(LMSG_CHARACTER_CHANGESEX_COST, PREMDAYS_TO_CHANGESEX);
					}
					else
					{		
						$sexo = $_sex[$_POST['character_sex']];
						$character->set("sex", $sexo);
						$character->save();
						
						//remove premdays da conta do jogador
						$account->updatePremDays(PREMDAYS_TO_CHANGESEX, false /* false to decrement days */);	
						
						$account->save();		
		
						$db->query("INSERT INTO ".DB_WEBSITE_PREFIX."changelog (`type`,`player_id`,`value`,`time`) values ('sex','{$character->get("id")}','{$sexo}','".time()."')");
						
						$success = Lang::Message(LMSG_CHARACTER_SEX_CHANGED, $character->getName());
					}		
				}		
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
			
			$sex_option = ($character->get("sex") == 1) ? '<option value="female">Feminino</option>' : '<option value="male">Masculino</option>';
			
			$editOptions .= '<option value="edit_information">Modificar Informações</option>';
			
			if(SHOW_SHOPFEATURES != 0)
			{
				$editOptions .= '<option value="edit_name">Modificar Nome</option>';
				$editOptions .= '<option value="edit_sex">Modificar Sexo</option>';
			}
			
			$module .=	'
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset>
					
					<p>
						<label>Personagem:</label><br />
						<input disabled="disabled" size="40" type="text" value="'.$_GET['name'].'" />
					</p>

					<div class="autoaction" style="margin: 0px; padding: 0px;">
						<p>
							<label for="edit_action">Ação</label><br />
							<select name="edit_action">
								'.$editOptions.'
							</select>
						</p>
					</div>					
					
					<div title="edit_information" class="viewable" style="margin: 0px; padding: 0px;">
						<p>
							<label for="character_comment">Comentario</label><br />
							<textarea name="character_comment" rows="10" wrap="physical" cols="55">'.$character->get("comment").'</textarea>
							<em><br>Limpe para deletar.</em>
						</p>	
	
						<p>
							<input '.(($character->get("hide") == "1") ? "checked=\"checked\"" : null).' name="character_hide" type="checkbox" value="1" /> Marque esta opção para esconder este personagem.
						</p>
					</div>		

					';
			
				if(SHOW_SHOPFEATURES != 0)
				{
					
					$module .=	'
					<div title="edit_name" style="margin: 0px; padding: 0px;">
						<p>
							<h3>Mudar Nome</h3>						
						</p>
					
						<p>
							<input name="character_newname" size="40" type="text" value="" />
							<br /><em>Escreva o novo nome para seu personagem.</em>
						</p>
						
						<p>
							<input name="confirm_changename" type="checkbox" value="1" /> Eu estou ciente e aceito que a modificação de nome de meu personagem irá ser feita sob um custo na qual será descontado 15 dias de minha conta premium.						
						</p>	
					</div>
					
					<div title="edit_sex" style="margin: 0px; padding: 0px;">
						<p>
							<h3>Mudar Sexo</h3>						
						</p>					
					
						<p>
							<select name="character_sex">
								'.$sex_option.'
							</select><br />
							<em>Escolha o novo sexo para seu personagem.</em>							
						</p>
						
						<p>
							<input name="confirm_changesex" type="checkbox" value="1" /> Eu estou ciente e aceito que a modificação de sexo de meu personagem irá ser feita sob um custo na qual será descontado 10 dias de minha conta premium.
						</p>	
						
						<p>
							<font color="red"><b>Atenção: </b></font>A mudança de sexo não transfere addons ou outfits que o personagem possua de um sexo para o outro, sendo necessario então conseguir novamente os addons no novo sexo.
						</p>						
					</div>';	
				}				
				
					$module .=	'
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
		Core::sendMessageBox(Lang::Message(LMSG_ERROR), $error);	
	}
}
else
{

$module .=	'
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
			
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
				<input class="button" type="submit" value="Enviar" />
			</p>
		</fieldset>
	</form>';
}			
?>