<?php
if($_GET['name'])
{
	$account = $core->loadClass("Account");
	$account->load($_SESSION['login'][0], "password");
	
	$character_list = $account->getCharacterList(true);	
	
	$guild = $core->loadClass("guilds");
	
	if(!$guild->loadByName($_GET['name']))
	{
		$module .=	'
		
		<div id="error">
			<h2>Esta guilda n�o existe em nosso banco de dados.</h2>
		</div>
		
		';			
	}
	elseif($account->getGuildLevel($guild->get("name")) > 1)
	{
		$module .=	'
		
		<div id="error">
			<h2>Voc� n�o tem permiss�o para acessar est� pagina.</h2>
		</div>
		
		';		
	}	
	else
	{		
		$guild->loadRanks();
		$ranks = $guild->getRanks();			
		
		$post = $core->extractPost();
		if($post)
		{
			$haveLongRank = 0;
			$ranklist = array();
			$$rankToRemove = array();
			$isFirstNull = false;
			$orderOut = false;
			
			foreach($_POST as $field => $value)
			{
				if($field != "account_password")
				{
					if(strlen($value) > 35)
					{
						$haveLongRank++;
					}
					
					$separator = explode("_", $field);
					
					if($value)
					{
						if($isFirstNull)
						{
							$orderOut = true;
							break;
						}	
							
						$ranklist[$separator[1]] = $value;	
					}			
					else		
					{
						if(count($rankToRemove) == 0)
							$isFirstNull = true;
							
						$rankToRemove[] = $separator[1];	
					}	
				}
			}			
			
			if($account->get("password") != $strings->encrypt($post[6]))
			{
				$error = "Confirma��o da senha falhou.";
			}			
			elseif($orderOut)
			{
				$error = "Os ranks est�o em sequencia fora de ordem.";	
			}			
			elseif($haveLongRank != 0)
			{
				$error = "Os ranks devem possuir no maximo 35 caracteres.";	
			}
			elseif(count($ranklist) < 3)
			{
				$error = "� obrigatorio existir ao menos 3 ranks em sua guild.";	
			}			
			else
			{		
				$rankHaveCharacter = 0;
				
				foreach($rankToRemove as $ranklvl)
				{
					if($guild->ereaseRank($ranklvl) == 1)
						$rankHaveCharacter++;	
				}				
				
				if($rankHaveCharacter == 0)
				{
					foreach($ranklist as $ranklvl => $rankname)
					{
						$guild->setRank($rankname, $ranklvl);
					}					
					
					$success = "
					<p>Caro jogador,</p>
					<p>As altera��es nos Ranks de sua guild foi efetuado com sucesso!</p>
					<p>Tenha um bom jogo!</p>
					";
				}
				else
				{
					$error = "Um ou mais ranks removidos de sua guilda est� em uso por um ou mais membros. S� � permitido remover um ranks que nenhum membro est� a ultilizar.";	
				}
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
				
			$rank_n = 0;
				
			foreach($ranks as $rank_id => $values)
			{
				$rank_n++;
				
				$ranks_show .= "
					<p>
						{$rank_n} <input name='rank_{$rank_n}' size='40' type='text' value='{$values['name']}' />
					</p>				
				";
			}
			
			$rank_dif = 6 - $rank_n;
			
			if($rank_dif > 0)
			{
				for($i = 0; $i < $rank_dif; $i++)
				{		
					$rank_n++;
					
					$ranks_show .= "
						<p>
							{$rank_n} <input name='rank_{$rank_n}' size='40' type='text' value='' />
						</p>				
					";
				}				
			}
			
			$module .=	'
			<form action="" method="post">
				<fieldset>			
					
					'.$ranks_show.'
					
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

}		
?>