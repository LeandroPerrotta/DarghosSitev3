<?php
use \Core\Configs;

if(isset($_POST['character_name']))
{
	\Core\Main::redirect("?ref=character.edit&name={$_POST['character_name']}");
}

$account = new \Framework\Account();
$account->load($_SESSION['login'][0]);

$list = $account->getCharacterList();

if($_GET['name'])
{
	if(in_array($_GET['name'], $list))
	{
		$player = new \Framework\Player();
		$player->loadByName($_GET['name']);

		if($_POST)
		{			
			if($account->get("password") != \Core\Strings::encrypt($_POST["account_password"]))
			{
				$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
			}			
			elseif($_POST["edit_action"] == "edit_information")
			{			
				if(strlen($_POST["character_comment"]) > 500)
				{
					$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_COMMENT_WRONG_SIZE);
				}
				else
				{		
					$hide = ($_POST["character_hide"] == 1) ? "1" : "0";
					$player->setComment(strip_tags($_POST["character_comment"]));
					$player->setHidden($hide);
					
					$player->save();
					
					$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_COMMENT_CHANGED);
				}
			}
			elseif($_POST["edit_action"] == "edit_name")
			{	
				if(!Configs::Get(Configs::eConf()->DISABLE_ALL_PREMDAYS_FEATURES))
				{
					$account = $player->loadAccount();
					
					$newname_character = new \Framework\Player();
					
					if(!$_POST["character_newname"])
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->FILL_FORM);
					}			
					elseif(!$_POST["confirm_changename"])
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_CHANGE_THING_CONFIRM);
					}			
					elseif(!\Core\Strings::canUseName($_POST["character_newname"]))
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_NAME);
					}
					elseif($newname_character->loadByName($_POST["character_newname"]))
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NAME_ALREADY_USED);
					}	
					elseif($player->getOnline() == 1)	
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NEED_OFFLINE);
					}			
					elseif($account->getBalance() < Configs::Get(Configs::eConf()->PREMCOST_CHANGENAME))
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_CHANGENAME_COST, number_format(Configs::Get(Configs::eConf()->PREMCOST_CHANGENAME) / 100, 2));
					}
					else
					{		
						$oldName = $player->getName();
						
						$player->setName($_POST["character_newname"]);
						$player->save();
						
						//integration SMF onchangepassword
						$user = new \Framework\Forums\User();
						if($user->LoadByAccount($account->getId()))
						    $user->UpdatePlayerNameExternalForum($_POST["character_newname"]);
						//end						
								
						//remove creditos do jogador
						$account->addBalance(-Configs::Get(Configs::eConf()->PREMCOST_CHANGENAME));					
						$account->save();				
						
						\Core\Main::addChangeLog('name', $player->get("id"), $_POST["character_newname"]);
						$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NAME_CHANGED, $oldName, $_POST["character_newname"]);
					}
				}			
			}
			elseif($_POST["edit_action"] == "edit_sex")
			{	
				if(!Configs::Get(Configs::eConf()->DISABLE_ALL_PREMDAYS_FEATURES))
				{				
					$account = $player->loadAccount();	
					
					if(!$_POST["confirm_changesex"])
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_CHANGE_THING_CONFIRM);
					}	
					elseif($account->get("type") > 2 AND $account->get("type") < 5)
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
					}						
					elseif($player->getOnline() == 1)
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NEED_OFFLINE);
					}			
					elseif($account->getBalance() < Configs::Get(Configs::eConf()->PREMCOST_CHANGESEX))
					{
						$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_CHANGESEX_COST, number_format(Configs::Get(Configs::eConf()->PREMCOST_CHANGESEX) / 100, 2));
					}
					else
					{		
						$genre_id = t_Genre::GetByString($_POST['character_sex']);
						$player->set("sex", $genre_id);
						$player->save();
						
						//remove creditos da conta do jogador
						$account->addBalance(-Configs::Get(Configs::eConf()->PREMCOST_CHANGESEX));	
						$account->save();
		
						\Core\Main::addChangeLog('sex', $player->get("id"), $genre_id);
						$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_SEX_CHANGED, $player->getName());
					}		
				}		
			}
		}
		
		if($success)	
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $success);
		}
		else
		{
			if($error)	
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);
			}
			
			$sex_option = ($player->get("sex") == t_Genre::Male) ? '<option value="'.t_Genre::GetString(t_Genre::Female).'">Feminino</option>' : '<option value="'.t_Genre::GetString(t_Genre::Male).'">Masculino</option>';
			
			$editOptions .= '<option value="edit_information">Modificar Informa????es</option>';
			
			if(!Configs::Get(Configs::eConf()->DISABLE_ALL_PREMDAYS_FEATURES))
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
							<label for="edit_action">A????o</label><br />
							<select name="edit_action">
								'.$editOptions.'
							</select>
						</p>
					</div>					
					
					<div title="edit_information" class="viewable" style="margin: 0px; padding: 0px;">
						<p>
							<label for="character_comment">Comentario</label><br />
							<textarea name="character_comment" rows="10" wrap="physical" cols="55">'.$player->get("comment").'</textarea>
							<em><br>Limpe para deletar.</em>
						</p>	
	
						<p>
							<input '.(($player->get("hide") == "1") ? "checked=\"checked\"" : null).' name="character_hide" type="checkbox" value="1" /> Marque esta op????o para esconder este personagem.
						</p>
					</div>		

					';
			
				if(!Configs::Get(Configs::eConf()->DISABLE_ALL_PREMDAYS_FEATURES))
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
							<input name="confirm_changename" type="checkbox" value="1" /> Eu estou ciente e aceito que a modifica????o de nome de meu personagem ir?? ser feita sob o custo de R$ ' .number_format(Configs::Get(Configs::eConf()->PREMCOST_CHANGENAME) / 100, 2). ' creditos a serem descontados de minha conta.						
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
							<input name="confirm_changesex" type="checkbox" value="1" /> Eu estou ciente e aceito que a modifica????o de sexo de meu personagem ir?? ser feita sob o custo de R$ ' .number_format(Configs::Get(Configs::eConf()->PREMCOST_CHANGESEX) / 100, 2). ' creditos a serem descontados de minha conta.
						</p>	
						
						<p>
							<font color="red"><b>Aten????o: </b></font>A mudan??a de sexo n??o transfere addons ou outfits que o personagem possua de um sexo para o outro, sendo necessario ent??o conseguir novamente os addons no novo sexo.
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
		\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $error);	
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