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
    		elseif(!$_POST["confirm_changesex"])
    		{
    			$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_CHANGE_THING_CONFIRM);
    		}					
			elseif($player->getOnline() == 1)
			{
				$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NEED_OFFLINE);
			}			
			elseif(!in_array($_POST["character_vocation"], array("knight", "warrior"))){
			    $error = tr("Vocação não permitida.");
			}
			else
			{
			    $error = false;
			    $player->canChangeVocation($_POST["character_vocation"], $error);
			    
			    if(!$error){
			        
			        $player->doChangeVocation($_POST["character_vocation"]);
			        $player->save();
			        
			        $success = tr("Vocação modificada com sucesso! Divirta-se agora com sua nova vocação!");
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
			
			$vocation_options = '<option value="warrior">Warrior (Guerreiro)</option><option value="knight">Knight (Cavaleiro)</option>';
			
			$module .=	'
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset>
					
			        <p>Caso você seja um Knight você pode mudar sua vocação para Warrior, a nova vocação do Darghos, e a experimentar. Existe a opção de você cancelar a mudança a voltar a ser um Knight dentro de 48h do momento que você efetuou a mudança, apos isto, não poderá mais reverter a mudança.</p>
			        
					<p>
						<label>Personagem:</label><br />
						<input disabled="disabled" size="40" type="text" value="'.$_GET['name'].'" />
					</p>
						        
			        <h3>Transformar personagem em Warrior</h3>						
					</p>					
				
					<p>
						<select name="character_vocation">
							'.$vocation_options.'
						</select><br />
						<em>Escolha a vocação desejada.</em>							
					</p>
					
					<p>
						<input name="confirm_changesex" type="checkbox" value="1" /> Eu estou ciente e aceito que poderei cancelar esta modificação dentro de 48h mas que apos tal periodo isto não será mais possivel.
					</p>';						        			
				
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