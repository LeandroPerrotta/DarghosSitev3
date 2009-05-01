<?php
if(isset($_POST['character_name']))
{
	$core->redirect("?ref=character.edit&name={$_POST['character_name']}");
}

$account = $core->loadClass("Account");
$account->load($_SESSION['login'][0], "password");

$list = $account->getCharacterList();

if($_GET['name'])
{
	if(in_array($_GET['name'], $list))
	{
		$character = $core->loadClass("character");
		$character->loadByName($_GET['name'], "name, comment, hide, online, sex, account_id");

		if($_POST)
		{
			if($account->get("password") != $strings->encrypt($_POST["account_password"]))
			{
				$error = "Confirma��o da senha falhou.";
			}			
			elseif($_POST["edit_action"] == "edit_information")
			{			
				if(strlen($_POST["character_comment"]) > 500)
				{
					$error = "O campo comentario deve possuir no maximo 500 caracteres.";
				}
				else
				{		
					$hide = ($_POST["character_hide"] == 1) ? "1" : "0";
					$character->set("comment", $_POST["character_comment"]);
					$character->set("hide", $hide);
					
					$character->save();
					
					$success = "
					<p>Caro jogador,</p>
					<p>A mudan�a das informa��es de seu personagem foi efetuada com exito!</p>
					<p>Tenha um bom jogo!</p>
					";
				}
			}
			elseif($_POST["edit_action"] == "edit_name")
			{	
				if(SHOW_SHOPFEATURES != 0)
				{
					$account = $character->loadAccount("premdays, lastday, type");
					
					$newname_character = $core->loadClass("character");
					
					if(!$_POST["character_newname"])
					{
						$error = "Preencha todos campos do formulario corretamente.";
					}
					elseif($account->get("type") > 2 AND $account->get("type") < 5)
					{
						$error = "Esta conta n�o possui permiss�o para acessar este recurso.";
					}				
					elseif(!$_POST["confirm_changename"])
					{
						$error = "Para modificar o nome de seu personagem � necessario aceitar e estar ciente destas mudan�as e os seus custos.";
					}			
					elseif(!$strings->canUseName($_POST["character_newname"]))
					{
						$error = "Este nome possui formata��o ilegal. Tente novamente com outro nome.";
					}
					elseif($newname_character->loadByName($_POST["character_newname"]))
					{
						$error = "Este nome j� est� em uso em nosso banco de dados. Tente novamente com outro nome.";
					}	
					elseif($character->get("online") != 0)
					{
						$error = "� nessario estar off-line no jogo para efetuar este recurso.";
					}			
					elseif($account->get("premdays") < PREMDAYS_TO_CHANGENAME)
					{
						$error = "Voc� n�o possui os ".PREMDAYS_TO_CHANGENAME." dias de conta premium necessarios para modificar o nome de seu personagem.";
					}
					else
					{		
						$character->set("name", $_POST["character_newname"]);
						$character->save();
						
						//remove��o dos premdays da conta do jogador
						$newpremdays = $account->get("premdays") - PREMDAYS_TO_CHANGENAME;
						
						$account->set("premdays", $newpremdays);
						$account->set("lastday", time());
						
						$account->save();				
						
						$db->query("INSERT INTO ".DB_WEBSITE_PREFIX."changelog (`type`,`player_id`,`value`,`time`) values ('name','{$character->get("id")}','{$_POST["character_newname"]}','".time()."')");
						
						$success = "
						<p>Caro jogador,</p>
						<p>A mudan�a de nome de seu personagem foi efetuada com exito!</p>
						<p>Tenha um bom jogo!</p>
						";
					}
				}			
			}
			elseif($_POST["edit_action"] == "edit_sex")
			{	
				if(SHOW_SHOPFEATURES != 0)
				{				
					$account = $character->loadAccount("premdays, lastday, type");	
					
					if(!$_POST["confirm_changesex"])
					{
						$error = "Para modificar o sexo de seu personagem � necessario aceitar e estar ciente destas mudan�as e os seus custos.";
					}	
					elseif($account->get("type") > 2 AND $account->get("type") < 5)
					{
						$error = "Esta conta n�o possui permiss�o para acessar este recurso.";
					}						
					elseif($character->get("online") != 0)
					{
						$error = "� nessario estar off-line no jogo para efetuar este recurso.";
					}			
					elseif($account->get("premdays") < PREMDAYS_TO_CHANGESEX)
					{
						$error = "Voc� n�o possui os ".PREMDAYS_TO_CHANGESEX." dias de conta premium necessarios para modificar o sexo de seu personagem.";
					}
					else
					{		
						$sexo = $_sex[$_POST['character_sex']];
						$character->set("sex", $sexo);
						$character->save();
						
						//remove��o dos premdays da conta do jogador
						$newpremdays = $account->get("premdays") - PREMDAYS_TO_CHANGESEX;
						
						$account->set("premdays", $newpremdays);
						$account->set("lastday", time());
						
						$account->save();		
		
						$db->query("INSERT INTO ".DB_WEBSITE_PREFIX."changelog (`type`,`player_id`,`value`,`time`) values ('sex','{$character->get("id")}','{$sexo}','".time()."')");
						
						$success = "
						<p>Caro jogador,</p>
						<p>A mudan�a de sexo de seu personagem foi efetuada com exito!</p>
						<p>Tenha um bom jogo!</p>
						";
					}		
				}		
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
			
			$sex_option = ($character->get("sex") == 1) ? '<option value="female">Feminino</option>' : '<option value="male">Masculino</option>';
			
			$editOptions .= '<option value="edit_information">Modificar Informa��es</option>';
			
			if(SHOW_SHOPFEATURES != 0)
			{
				$editOptions .= '<option value="edit_name">Modificar Nome</option>';
				$editOptions .= '<option value="edit_sex">Modificar Sexo</option>';
			}
			
			$module .=	'
			<form action="" method="post">
				<fieldset>
					
					<p>
						<label>Personagem:</label><br />
						<input disabled="disabled" size="40" type="text" value="'.$_GET['name'].'" />
					</p>

					<div class="autoaction" style="margin: 0px; padding: 0px;">
						<p>
							<label for="edit_action">A��o</label><br />
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
							<input '.(($character->get("hide") == "1") ? "checked=\"checked\"" : null).' name="character_hide" type="checkbox" value="1" /> Marque est� op��o para esconder este personagem.
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
							<input name="confirm_changename" type="checkbox" value="1" /> Eu estou ciente e aceito que a modifica��o de nome de meu personagem ir� ser feita sob um custo na qual ser� descontado 15 dias de minha conta premium.						
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
							<input name="confirm_changesex" type="checkbox" value="1" /> Eu estou ciente e aceito que a modifica��o de sexo de meu personagem ir� ser feita sob um custo na qual ser� descontado 10 dias de minha conta premium.
						</p>	
						
						<p>
							<font color="red"><b>Aten��o: </b></font>A mudan�a de sexo n�o transfere addons ou outfits que o personagem possua de um sexo para o outro, sendo necessario ent�o conseguir novamente os addons no novo sexo.
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
		$core->sendMessageBox("Erro!", $error);	
	}
}
else
{

$module .=	'
	<form action="" method="post">
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