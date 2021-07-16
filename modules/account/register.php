<?php
class View
{
	//html fields
	private $_account_name, $_account_password, $_account_confirm_password, $_account_email, $_char_name, $_char_world, $_char_town, $_char_genre, $_char_vocation;
	
	//variables
	private $_message;		
	
	//custom variables
	private $loggedAcc, $steps;		
	
	function View()
	{		
		if($_SESSION["login"])
		{
			\Core\Main::redirect("index.php");
			return;
		}
		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}		
		
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
		
		$this->_account_name = new \Framework\HTML\Input();
		$this->_account_name->SetName("account_name");
		$this->_account_name->SetSize(\Framework\HTML\Input::SIZE_SMALL);
				
		$this->_account_password = new \Framework\HTML\Input();
		$this->_account_password->SetName("account_password");		
		$this->_account_password->IsPassword();		
		$this->_account_password->SetSize(\Framework\HTML\Input::SIZE_SMALL);
		
		$this->_account_confirm_password = new \Framework\HTML\Input();
		$this->_account_confirm_password->SetName("account_confirm_password");		
		$this->_account_confirm_password->IsPassword();		
		$this->_account_confirm_password->SetSize(\Framework\HTML\Input::SIZE_SMALL);

		$this->_account_email = new \Framework\HTML\Input();
		$this->_account_email->SetName("account_email");	
		$this->_account_email->SetSize(\Framework\HTML\Input::SIZE_SMALL);
		
		$this->_char_name = new \Framework\HTML\Input();
		$this->_char_name->SetName("character_name");
		$this->_char_name->SetSize(\Framework\HTML\Input::SIZE_SMALL);
		
		$this->_char_world = new \Framework\HTML\SelectBox();
		$this->_char_world->SetName("character_world");
		$this->_char_world->AddOption("");
		$this->_char_world->AddOption(t_Worlds::GetString(t_Worlds::Ordon), t_Worlds::Ordon);
		$this->_char_world->AddOption(t_Worlds::GetString(t_Worlds::Aaragon), t_Worlds::Aaragon);
		
		$this->_char_town = new \Framework\HTML\SelectBox();
		$this->_char_town->SetName("character_town");
		$this->_char_town->AddOption("");
		$this->_char_town->AddOption(t_Towns::GetString(t_Towns::IslandOfPeace), t_Towns::IslandOfPeace);	
		$this->_char_town->AddOption(t_Towns::GetString(t_Towns::Quendor), t_Towns::Quendor);
		$this->_char_town->AddOption(t_Towns::GetString(t_Towns::Thorn), t_Towns::Thorn);
		
		$this->_char_genre = new \Framework\HTML\SelectBox();
		$this->_char_genre->SetName("character_genre");
		$this->_char_genre->AddOption("Masculino", t_Genre::GetString(t_Genre::Male));
		$this->_char_genre->AddOption("Feminino", t_Genre::GetString(t_Genre::Female));
		
		$this->_char_vocation = new \Framework\HTML\SelectBox();
		$this->_char_vocation->SetName("character_vocation");
		$this->_char_vocation->SetSize(\Framework\HTML\Consts::SELECTBOX_SIZE_BIG);
		$this->_char_vocation->AddOption("");
		$this->_char_vocation->AddOption("Knight (Cavaleiro)", "knight");
		$this->_char_vocation->AddOption("Paladin (Paladino)", "paladin");		
		$this->_char_vocation->AddOption("Sorcerer (Feitiçeiro)", "sorcerer");		
		$this->_char_vocation->AddOption("Druid (Druida)", "druid");		
		
		$this->steps = array();
		
		$this->steps[] = array(
			"step" => "1",
			"title" => "Criar nova conta (passo a passo)",
			"body" => "
			<p>Seja bem vindo ao Darghos! Este formulario irá o ajudar a criar a sua primeira conta, seu primeiro personagem e também informções que você deve saber.</p>
			<p class='long-margin-top'>Escreva no campo a baixo o <b>nome de sua conta</b> que você desejar, este é o principal dado de sua conta para você efetuar tanto o login no jogo como no website.</p>
			<p> 
				<label>Nome da conta:</label>
				{$this->_account_name->Draw()}
			</p>
			<p class='long-margin-top'>Escreva no campo a baixo a <b>senha de acesso para sua conta</b> que você desejar, este dado não é menos importante que o nome de sua conta.</p>
			<p> 
				<label>Senha de acesso:</label>
				{$this->_account_password->Draw()}
				
				<label>Confirmação:</label>
				{$this->_account_confirm_password->Draw()}
				
			</p>			
			",
		);
		
		$this->steps[] = array(
			"step" => "2",
			"title" => "Conta criada! E-mail e segurança",
			"body" => "
			<h3>Parabens!</h3>
			<p>A sua conta, nomeada como <b><span id='account_name'></span></b> e a senha informada foi cadastrada com <b>sucesso</b>! Não se esqueça de memorizar-las ou anotar-las num local seguro!</p>
			<p class='long-margin-top'>Porem existem mais dados que são necessarios para aumentar a segurança de sua conta, o principal é o cadastro de seu e-mail.</p>
			<p>Quando você precisar recuperar sua conta, em caso de perda de dados, esquecimento ou mesmo <i>hacking</i>, será em seu e-mail que o sistema enviará tudo o que for necessario para você recuperar o acesso a sua conta em segurança. Você pode optar por já efetuar o registro de seu e-mail em sua conta ou deixar-lo para depois.</p>
			<p><b>Obs:</b> Você recebera alertas de segurança quando você efetuar login tanto no website como no jogo enquanto você não cadastrar um e-mail, além de que alguns recursos de sua conta ficarão bloqueados, entre eles:</p>
			<ul>
				<li>Gerar uma chave de recuperação.</li>
				<li>Adquirir uma Conta Premium.</li>
				<li>Adquirir uma semana de Premium gratuita ao atingir level 100 em seu personagem.</li>
			</ul>	
			
			<div class='long-margin-top'>
				<label><input type='radio' name='email_check' value='0'> <span>Quero deixar o cadastro de meu e-mail para depois...</span></label>
				<label><input type='radio' name='email_check' value='1'> <span><b>Eu desejo cadastrar meu e-mail agora! (recomendavel)</b></span></label>
				
				<div class='email_check'>
					<div class='1' style='display: none;'>
						<p>
							<label>Endereço de e-mail</label>
							{$this->_account_email->Draw()}						
						</p>
					</div>
				</div>
			</div>
			",
		);
		
		$world_str = "
		<p class='long-margin-top'>Selecione em qual cidade você deseja iniciar sua jornada. Note que Island of Peace é ideal para iniciantes aonde o PvP não é permitido e existem muitos respawns, quests e missões que auxiliaram você até o nivel 80. Já Quendor e Thorn são regiões de PvP permitido.</p>
		<p>
			<label>Cidade do personagem:</label>
			{$this->_char_town->Draw()}
		</p>";

		if(\Core\Configs::Get(\Core\Configs::eConf()->ENABLE_MULTIWORLD))
			$world_str = "
				<p class='long-margin-top'>O Darghos oferece atualmente duas opções de mundos, que apesar de possuirem o mesmo conteudo possuem caracteristicas diferentes, selecione de qual você deseja participar:</p>
				<p>
					<label>Mundo do personagem:</label>
					{$this->_char_world->Draw()}
					
					<div class='character_world'>
						<div class='1' style='display: none;'>
							<p>
								<p><b>Tipo PvP:</b><br> somente aberto.</p>
								<p><b>Inauguração:</b><br> fev/2012.</p>
								<p><b>Dificuldade & Rates:</b><br> Moderada.</p>
							</p>
							<p>
								<label>Cidade natal do personagem:</label>
								{$this->_char_town->Draw()}
							</p>
						</div>
						<div class='0' style='display: none;'>
							<p>
								<p><b>Tipo PvP:</b><br> mudança permitida.</p>
								<p><b>Inauguração:</b><br> fev/2011.</p>
								<p><b>Dificuldade & Rates:</b><br> Facil.</p>
							</p>
							<p>
								<label>Cidade natal do personagem:</label>
								{$this->_char_town->Draw()}
							</p>						
						</div>								
					</div>
				</p>		
			";
							
		$this->steps[] = array(
			"step" => "3",
			"title" => "Seu primeiro personagem",
			"body" => "
			<p class='email_check long-margin-bottom' style='display: none;'>Foi enviado a seu endereço de e-mail <b><span id='account_email'></span></b> uma mensagem contendo um link para validação do e-mail informado, basta que você acesse-o para finalizar o cadastro do e-mail em sua conta garantindo sua segurança e liberando todos os recursos disponiveis para conta!</p>
			<p>O proximo passo será a criação de seu primeiro personagem no Darghos. Preencha o campo abaixo com o nome que você deseja para seu personagem.</p>	
			
			<p>
				<label>Nome do personagem:</label>
				{$this->_char_name->Draw()}			
			</p>
			
			{$world_str}	
			
			<p class='long-margin-top'>Os personagens podem ser do genero masculino ou feminino, selecione abaixo o genero desejado para seu personagem.</p>
			<p>
				<label>Genero do personagem:</label>
				{$this->_char_genre->Draw()}			
			</p>
			
			<p class='long-margin-top'>Todo personagem no Darghos pertence a uma vocação. No total existem quatro vocações disponiveis, cada uma com suas proprias habilidades, estilo de jogo e caracteristicas, selecione abaixo a vocação que você deseja para o seu personagem.</p>
			<p>
				<label>Vocação do personagem:</label>
				{$this->_char_vocation->Draw()}			
				
				<div class='character_vocation'>
					<div class='knight' style='display: none;'>
						<p>
							<p><b>Especialidade:</b><br> Causar dano fisico com armas de curta distancia e proteção de sí proprio com escudo.</p>
							<p><b>Armas usada:</b><br> Espadas (swords), machados (axes), martelos (club).</p>
							<p><b>Dominio magico:</b><br> Baixo.</p>
							<p><b>Outras caracteristicas:</b><br> Possuir muita vida, defesa e capacidade de regeneração fazem desta classe sua especialidade a sobrevivencia.</p>
						</p>
					</div>
					<div class='paladin' style='display: none;'>
						<p>
							<p><b>Especialidade:</b><br> Causar dano fisico com armas a longa e curta distancia.</p>
							<p><b>Armas usada:</b><br> Arcos (bows), bestas (crossbows), lanças (spears), estrelas (stars).</p>
							<p><b>Dominio magico:</b><br> Médio.</p>
							<p><b>Outras caracteristicas:</b><br> Possuir vida e defesa razoaveis, junto a uma boa capacidade de regeneração fazem classe sua especialidade causar danos fisicos e ter boas chances de sobrevivencia.</p>
						</p>
					</div>		
					<div class='sorcerer' style='display: none;'>
						<p>
							<p><b>Especialidade:</b><br> Extremamente letal causando dano mágico a longa distancia. </p>
							<p><b>Armas usada:</b><br> Varinhas (wands).</p>
							<p><b>Dominio magico:</b><br> Alto.</p>
							<p><b>Outras caracteristicas:</b><br> Seu grande arsenal de feitiços e encantamentos fazem desta classe sua especilidade a agressividade com danos magicos. Porem sua baixa vida a torna vulneravel.</p>
						</p>
					</div>	
					<div class='druid' style='display: none;'>
						<p>
							<p><b>Especialidade:</b><br> Causar dano mágico a longa distancia. </p>
							<p><b>Armas usada:</b><br> Cajados (rods).</p>
							<p><b>Dominio magico:</b><br> Alto.</p>
							<p><b>Outras caracteristicas:</b> Grande arsenal com feitiços e encantamentos para ajudar e recuperar a si proprio e amigos fazem desta classe sua especialidade causar danos magicos e auxiliar em combate.</p>
						</p>
					</div>												
				</div>				
			</p>			
			
			",
		);		
		
		$this->steps[] = array(
			"step" => "4",
			"title" => "Personagem criado!",
			"body" => "
			<h3>Parabens!</h3>
			<p>O seu primeiro personagem, <b><span id='character_name'></span></b> foi criado com <b>sucesso</b>!</p>
			<p>Você já pode se conectar ao Darghos e começar a se divertir! Se necessario, neste <a href='?ref=general.howplay'>link</a> você pode obter o download do cliente e instruções de como se conectar.</p>
			<p><h3>Primeiros passos</h3></p>
			<p>Você começa sua jornada em Island of Peace. Mas não se deixe enganar pelo nome do lugar, de inicio <b>Mereus</b> solicitará sua ajuda na batalha conta os inimigos! Boa sorte!</p>
			",
		);		
		
		return true;
	}
	
	function Post()
	{
		return true;
	}
	
	function Draw()
	{
		global $module;		
		
		\Core\Main::includeJavaScriptSource("views/account_register.js");
		
		$module .= "		
		<fieldset style='margin-top: 20px;'>
		
		<div id='step_by_step'>
		";
		
		foreach($this->steps as $step)
		{
			$hide = ($step["step"] > 1) ? "style='display: none;'" : null;
			$module .= "
			
			<div class='{$step["step"]}' {$hide}>
				<div id='new-title-bar'>
					<h3 id='new-title'>
						{$step["title"]}
					</h3>
					<div id='infos-line'>
					parte {$step["step"]} de ".count($this->steps)."
					</div>	
				</div>
				<div id='new-summary'>{$step["body"]}</div>
			</div>
			";			
		}
		
		$module .= "	
		</div>		
		<p class='line'></p>
			
		<p>
			<input id='btNext' class='button' type='submit' value='Proximo'/>
		</p>
		</fieldset>";
	}
}	

$view = new View();
?>