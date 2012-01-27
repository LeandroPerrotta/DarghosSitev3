<?php
$account = new \Framework\Account();
$account->load($_SESSION['login'][0]);

$list = $account->getCharacterList();

if($_POST)
{
	$player = new \Framework\Player();
	$player->loadByName($_POST["player_name"]);
	
	if($account->getPassword() != \Core\Strings::encrypt($_POST["account_password"]))
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->WRONG_PASSWORD);
	}	
	elseif($player->deletionStatus())
	{
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_ALREADY_TO_DELETE);
	}
	elseif(!in_array($_POST["player_name"], $list))
	{	
		$error = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_FROM_YOUR_ACCOUNT);
	}
	else
	{
		$player->addToDeletion();
		
		$success = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_DELETION_SCHEDULED, $_POST["player_name"], \Core\Main::formatDate($player->deletionStatus()));	
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

$module .=	'
	<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
			
			<p>Selecione abaixo qual personagem de sua conta você deseja agendar uma exclusão. Este agendamento leva 30 dias e pode ser cancelado a qualquer momento dentro deste periodo. Note que após passado o periodo de 30 dias é impossivel cancelar a exclusão, recuperar o personagem ou qualquer um de seus pertences.</p>		
		
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
?>