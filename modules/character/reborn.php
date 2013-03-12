<?php
use \Core\Configs;
class View
{
	//html fields
	private $_password, $_invites, $_character_list;	
	
	//variables
	private $_message, $_newVoc;	

	//interações
	
	/*
	 * Estrutura:
	 * numero[
 	 * 		texto,
     * 		opções[...] 		
	 * 		]
	 * ]
	 * */
	private $_talk = array();
	
	//custom variables
	private $loggedAcc, $player;	
	
	function View()
	{
		if(!$_GET['name'])
			return false;
			
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}
			
		$this->_password = new \Framework\HTML\Input();
		$this->_password->SetName("account_password");
		$this->_password->IsPassword();		
		
		if($_POST)
		{
			if(!$this->Post())
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			}
			else
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
				return true;
			}
		}
		
		$this->Draw();
			
		return true;		
	}
	
	function Prepare()
	{
		$this->loggedAcc = new \Framework\Account();
		
		if(!$this->loggedAcc->load($_SESSION['login'][0]))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->NEED_LOGIN);
			return false;			
		}
		
		$this->player = new \Framework\Player();
		
		if(!$this->player->loadByName($_GET["name"]))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_WRONG);
			return false;
		}
		
		if($this->player->getAccountId() != $this->loggedAcc->getId())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->CHARACTER_NOT_FROM_YOUR_ACCOUNT);
			return false;
		}
		
		switch($this->player->getVocation())
		{
			case 5: $this->_newVoc = "Warmaster Sorcerer"; break;
			case 6: $this->_newVoc = "Warden Druid"; break;
			case 7: $this->_newVoc = "Holy Paladin"; break;
			case 8: $this->_newVoc = "Berzerker Knight"; break;
		}
		
		$this->_talk[1] = array(
			"text" => "Este é meu santuario, em boa parte de meu tempo estou meditando para fortalecer minha alma, tem sorte de conseguir falar comigo. Vejo que tu es fisicamente forte mas sua alma continua fraca.",
			"options" => array(
				array("text" => "Perguntar sobre as almas.", "topic" => "2"),
				array("text" => "Ir embora.", "topic" => "leave")
			)
		);
		
		if($this->player->getLevel() < Configs::Get(Configs::eConf()->FIRST_REBORN_LEVEL))
			$this->_talk[2] = array(
				"text" => "Como se sabe, todos possuem uma alma. Eu tenho o poder de fortificar almas, entretanto para isto é necessario um terrivel sacrificio e você não possui força fisica sulficiente para aguentar isto. Você precisa se fortalecer fisicamente até o level ".Configs::Get(Configs::eConf()->FIRST_REBORN_LEVEL)." para poder fortalecer sua alma.",
				"options" => array(
					array("text" => "Ir embora.", "topic" => "leave")
				)			
			);
		elseif($this->player->getVocation() <= 4)
			$this->_talk[2] = array(
				"text" => "Vejo que você sequer possui a promoção recebida do Rei de Aracura. Você ainda não é digno de fortificar sua alma!",
				"options" => array(
					array("text" => "Ir embora.", "topic" => "leave")
				)			
			);
		elseif($this->player->getVocation() > 8)
			$this->_talk[2] = array(
				"text" => "Você já fortificou sua alma! Não é necessario, por enquanto, fortificar novamente sua alma!",
				"options" => array(
					array("text" => "Ir embora.", "topic" => "leave")
				)			
			);		
		else
			$this->_talk[2] = array(
				"text" => "Como se sabe, todos possuem uma alma. Eu tenho o poder de fortificar almas, assim elas seriam melhorada, elevadas a um nivel superior. Apos fortificada sua alma você se transformaria em um {$this->_newVoc}. Porem para isto é necessario um terrivel sacrificio.",
				"options" => array(
					array("text" => "Perguntar sobre o sacrificio.", "topic" => "3"),
					array("text" => "Interessante, mas não quero saber de sacrificios, até mais!", "topic" => "leave")
				)
			);		
			
		$this->_talk[3] = array(
			"text" => "Sim, para isto precisariamos firmar um pacto. Para fortificar sua alma é necessario um processo de transição que só pode ser atingido por um sacrificio! E este sacrificio é a morte! Mas não é uma morte qualquer... Estamos falando da <b>morte da alma</b>!",
			"options" => array(
				array("text" => "Morte da alma? O que é uma morte da alma?", "topic" => "4"),
				array("text" => "Isto me parece muito sombrio, prefiro ir embora.", "topic" => "leave")
			)
		);			
			
		$this->_talk[4] = array(
			"text" => "Quer saber sobre a morte da alma? Então vou lher contar sobre isto... Para fortificar sua alma é preciso purificar-la, fazendo desaparecer todas suas imperfeições, e isto só pode ser obtido atraves de sua propria morte! Quando sua alma morre ela é purificada e pode ser fortificada entretanto você perde toda sua força fisica! Você volta a condição de quando tudo começou em Island of Peace lembra-se? Este é o preço a pagar pela fortificação da alma! A vantagem é que quando você recuperar sua força fisica atual com a alma fortificada você será extremamente poderoso! E então, quer fazer este pacto? Pela tua cara espantada vejo que és um covarde!",
			"options" => array(
				array("text" => "Não sou covarde! Eu topo selar o pacto e purificar minha alma a sacrificando!", "topic" => "5"),
				array("text" => "O que? Level 8 de novo? NUNCA! Vou me embora ao invez de escutar as loucuras deste velho louco!", "topic" => "leave")
			)
		);		

		$this->_talk[5] = array(
			"text" => "Tua coragem me supreende! Esta é uma poção aniquiladora de almas, ela leva secúlos para ser preparada, você deve tomar e em alguns instantes sua alma estará morta! Mas talvez não tenha entendido... É exatamente ISTO! Se você fechar este pacto perderá sua alma e recomeçara aonde tudo começou! Na ilha dos fracotes novamente no level 8! Apénas tua habilidade com armas e magias serão mantidas! Tens certeza que queres isto?? <font color='red'><b>Lembre-se que depois do pacto firmado é impossivel revertar a morte de sua alma!</b></font>",
			"options" => array(
				array("text" => "É isto mesmo que quero! Sei que irei re-começar tudo mas com a alma fortificada em pouco tempo me re-erguerei e serei respeitado pela minha nova força e minha nova classe! Aceito!", "topic" => "6"),
				array("text" => "Agora entendi! Uffa... Não é isto que quero, sou mesmo um covarde! Até mais!", "topic" => "leave")
			)
		);		
		
		$this->_talk[6] = array(
			"text" => "Uma ultima chance! Tem certeza? Depois não adianta chorar! MUAHUAHUAHUMAUHAU [gargalhada malefica]",
			"options" => array(
				array("text" => "Não chorarei! Sim! Tenho certeza do que quero! Saúde! [começar a beber a poção...] (obs: precisa estar offline no jogo)", "topic" => "7"),
				array("text" => "Realmente é melhor eu pensar mais um pouco... Acho melhor fazermos isto outro dia!", "topic" => "leave")
			)
		);
		
		$this->_talk[7] = array(
			"text" => "Sinto que sua alma já está forte! Mas você está novamente ridiculamente fraco! Hahaha! Mas faz parte do processo! Agora conforme você for recuperando sua força fisica irá notar como a fortificação de sua alma foi um bom negocio! Vou lhe enviar para Island of Peace e poderá re-começar sua jornada! Até que és corajoso! Até mais e mande notícias!",
			"options" => array(
				array("text" => "Sim, me envie! Até mais!", "topic" => "leave")
			)
		);		
		
		
		return true;
	}
	
	function Post()
	{

	}
	
	function Draw()
	{
		global $module;
		
		$interactions = "";
		
		foreach($this->_talk as $key => $interact)
		{
			$display = "display: none;";
			
			if($key == 1)
				$display = "display: visible;";
			
			$interactions .= "
			<div title=\"{$key}\" style=\"{$display} margin: 0px; padding: 0px;\">
				<p>
					<b>Baron Samedi:</b> <em>{$interact["text"]}</em>
				</p>					
				
				<p>
					<ul style=\"list-style: none;\">";
			
			foreach($interact["options"] as $option)
			{
				$interactions .= "<li><input type='radio' name='{$key}' value='{$option["topic"]}'/>{$option["text"]}</li>";
			}

			$interactions .= "</ul>
				</p>		
			</div>";
		}
		
		$jquery = '
		<script type="text/javascript">
		var topic = 1;
		
		$(document).ready(function() {	
			$("#btNext").click(function() {
				var selected = $("input[name=\'" + topic + "\']:checked").val();
				
				if(selected == "leave")
				{
					window.location = "?ref=account.main";
				}
				else if(selected == "7")
				{
					if(requestRebornPlayer("'.$_GET["name"].'") == 0)
					{
						alert("Para efetuar está operação é necessario que seu personagem esteja off-line no jogo. Por favor, dê log-out e tente novamente.");
						return;
					}
					
					$("div[title=\'" + selected + "\']").show();
					$("div[title=\'" + topic + "\']").hide();
					topic = selected;					
				}
				else
				{
					$("div[title=\'" + selected + "\']").show();
					$("div[title=\'" + topic + "\']").hide();
					topic = selected;
				}
			});
		});
		</script>		
		';
		
		$module .= "	
		<fieldset>
			
			{$interactions}			
			
			<p id='line'></p>
			
			<p>
				<input id='btNext' class='button' type='submit' value='Proximo' />
			</p>
		</fieldset>
		
		{$jquery}";		
	}
}

$view = new View();
?>