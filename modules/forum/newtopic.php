<?php
class View
{
	//html fields
	private $_title, $_topic, $_ispoll, $_isnotice, $_poll_text, $_poll_end_date, $_poll_onlypremium, $_poll_minlevel, $_poll_optioncount; 
	
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $user;	
	
	function View()
	{		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;
		}
		
		if($_SESSION['login'])
		{
			$this->loggedAcc = new \Framework\Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
		}		
		
		$this->_title = new \Framework\HTML\Input();
		$this->_title->SetName("topic_title");
		
		$this->_topic = new \Framework\HTML\Input();
		$this->_topic->IsTextArea(25, 80);
		$this->_topic->SetName("topic_content");
		$this->_topic->SetId("topic_content");
		
		$this->_ispoll = new \Framework\HTML\Input();
		$this->_ispoll->IsCheackeable();
		$this->_ispoll->SetValue("true");
		$this->_ispoll->SetName("topic_ispoll");
		
		$this->_isnotice = new \Framework\HTML\Input();
		$this->_isnotice->IsCheackeable();
		$this->_isnotice->SetValue("true");
		$this->_isnotice->SetName("topic_isnotice");
		
		$this->_poll_text = new \Framework\HTML\Input();
		$this->_poll_text->IsTextArea(7, 50);
		$this->_poll_text->SetName("topic_poll_text");
		
		$this->_poll_end_date = new \Framework\HTML\Input();
		$this->_poll_end_date->SetName("topic_poll_enddays");
		$this->_poll_end_date->SetSize(10);
		
		$this->_poll_onlypremium = new \Framework\HTML\Input();
		$this->_poll_onlypremium->IsCheackeable();
		$this->_poll_onlypremium->SetValue("true");	
		$this->_poll_onlypremium->SetName("topic_poll_onlypremium");

		$this->_poll_minlevel = new \Framework\HTML\Input();
		$this->_poll_minlevel->SetName("topic_poll_minlevel");		
		$this->_poll_minlevel->SetSize(10);		
		
		$this->_poll_optioncount = new \Framework\HTML\SelectBox();
		$this->_poll_optioncount->SetName("topic_poll_optioncount");
		
		for($i = 1; $i <= 10; $i++)
		{
			$this->_poll_optioncount->AddOption($i);
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
		$this->user = new \Framework\Forums\User();
		
		if(!$this->user->LoadByAccount($_SESSION['login'][0]))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_ACCOUNT_NOT_HAVE_USER);
			return false;			
		}
		
		return true;
	}
	
	function Post()
	{
		if(!$this->_title->GetPost() || !$this->_topic->GetPost())
		{
			$this->_message = "Preencha todos campos corretamente.";
			return false;			
		}
			
		/*if($this->_ispoll->GetPost() != "true")
		{
			$this->_message = "S?? ?? permitido criar topicos em forma de enquete no momento.";
			return false;			
		}	*/

		if($this->_ispoll->GetPost() == "true" && (!$this->_poll_text->GetPost() || !$this->_poll_end_date->GetPost() || !$this->_poll_minlevel->GetPost()))
		{
			$this->_message = "Preencha todos campos referentes a enquete corretamente.";
			return false;			
		}

		if($this->_ispoll->GetPost() == "true" && (!is_numeric($this->_poll_end_date->GetPost()) || !is_numeric($this->_poll_minlevel->GetPost())))
		{
			$this->_message = "Dura????o da enquete e level minimo devem conter ap??nas numeros.";
			return false;				
		}

		if($this->_ispoll->GetPost() == "true" && ($this->_poll_end_date->GetPost() < 5 || $this->_poll_end_date->GetPost() > 90))
		{
			$this->_message = "A dura????o da enquete deve ser entre 5 dias e 90 dias.";
			return false;				
		}
		
		if($this->_ispoll->GetPost() == "true")
		{
			for($i = 1; $i <= $this->_poll_optioncount->GetPost(); $i++)
			{
				if(!$_POST["option_{$i}"])
				{
					$this->_message = "Voc?? deve preencher todas op????es.";
					return false;					
				}
			}
		}
		
		$topic = new \Framework\Forums\Topics();
		
		$topic->SetTitle($this->_title->GetPost());
		$topic->SetTopic($this->_topic->GetPost());
		$topic->SetDate(time());
		$topic->SetAuthorId($this->user->GetMemberId());
		
		if($this->_isnotice->GetPost() == "true")
		{
			$topic->SetIsNotice();
		}

		if($this->_ispoll->GetPost() == "true")
		{
			$topic->SetIsPoll();
			
			$topic->SetPollText($this->_poll_text->GetPost());
			$topic->SetPollMinLevel($this->_poll_minlevel->GetPost());
			$topic->SetPollEnd(time() + (60 * 60 * 24 * $this->_poll_end_date->GetPost()));
			
			if($this->_poll_onlypremium->GetPost() == "true")
				$topic->SetPollIsOnlyForPremium();			
		}			
			
		$topic->Save();	
			
		if($this->_ispoll->GetPost() == "true")
		{
			for($i = 1; $i <= $this->_poll_optioncount->GetPost(); $i++)
			{
				$topic->AddPollOption($_POST["option_{$i}"]);
			}
		}		

		$this->_message = "Seu topico foi criado com sucesso!";
		return true;			
	}
	
	function Draw()
	{
		global $module;		
				
		$module .= "
		<script type='text/javascript'>		
		function drawOptions(number)
		{
			$('.poll_options').empty();
		
			var i = 0;
			
			while(i < number)
			{
				i++;
				
				var input = \"<p><label>Op????o \" + i + \"</label><br /><input name='option_\" + i + \"' value='' size='40' type='text'/></p>\";
				
				$('.poll_options').append(input);
			}		
		}
		
		$(document).ready(function() {
		
			var isPoll = false;
		
			$('.ispoll').hide();
		
			$('input[name=topic_ispoll]').change(function() {
			
				if(!isPoll)
				{
					$('.ispoll').show();
					isPoll = true;
				}
				else
				{
					$('.ispoll').hide();
					isPoll = false;
				}
			});
			
			$('select[name=topic_poll_optioncount]').change(function() {
			
				var str = $('select[name=topic_poll_optioncount] option:selected').text();
				drawOptions(str);
			});
			
			var str = $('select[name=topic_poll_optioncount] option:selected').text();
			drawOptions(str);

		});
		</script>	
		
		<form action='{$_SERVER['REQUEST_URI']}' method='post'>
			<fieldset>
	
				<p>
					<label for='topic_title'>Titulo do topico</label><br />
					{$this->_title->Draw()}
				</p>					
			  	
				<p>
					<label for='{$this->_topic->GetName()}'>Conteudo do topico</label><br />
					".\Core\Main::CKEditor($this->_topic->GetName(), "")."
				</p>	

				<p>
					{$this->_ispoll->Draw()} Topico em forma de enquete.<br />
				</p>				

				<div class='ispoll' style='margin: 0px; margin-top: 20px; padding: 0px;'>
					<p>
						<label for='topic_poll_text'>Assunto da enquete</label><br />
						{$this->_poll_text->Draw()}
					</p>				
	
					<p>
						<label for='topic_poll_enddays'>Dura????o da enquete (em numero de dias)</label><br />
						{$this->_poll_end_date->Draw()}
					</p>	
	
					<p>
						<label for='topic_poll_minlevel'>Level minimo para votar</label><br />
						{$this->_poll_minlevel->Draw()}
					</p>				
					
					<p>
						{$this->_poll_onlypremium->Draw()} Ap??nas jogadores com conta premium podem votar.<br />
					</p>	

					<p>
						<label for='topic_poll_optioncount'>Quantas op????es estar??o disponiveis?</label><br />
						{$this->_poll_optioncount->Draw()}
					</p>		

					<div class='poll_options' style='margin: 0px; margin-top: 20px; padding: 0px;'>
					</div>
				</div>
				
				<p>
					{$this->_isnotice->Draw()} O t??pico ?? uma not??cia.<br />
				</p>
				
				<p id='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' />
				</p>
			</fieldset>
		</form>";					
	}
}

$view = new View();
?>