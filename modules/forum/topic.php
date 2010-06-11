<?php
class View
{
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $topic, $user;	
	
	function View()
	{
		if(!$_GET['v'])
		{
			return;
		}
		
		if(!$this->Prepare())
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		
		if($_SESSION['login'])
		{
			$this->loggedAcc = new Account();
			$this->loggedAcc->load($_SESSION['login'][0]);
		}		
		
		if($_POST)
		{
			if(!$this->Post())
			{
				Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			}
			else
			{
				Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $this->_message);
				return true;
			}
		}		
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{
		$this->user = new Forum_User();
		
		if(!$this->user->LoadByAccount($_SESSION['login'][0]))
		{
			$this->_message = Lang::Message(LMSG_FORUM_ACCOUNT_NOT_HAVE_USER);
			return false;			
		}
		
		$this->topic = new Forum_Topics();
		
		if(!$this->topic->Load($_GET['v']))
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;
		}
		
		return true;
	}
	
	function Post()
	{
		if($_POST["poll_option"] && $this->topic->IsPoll())
		{
			return $this->PollPost();
		}
	}
	
	function PollPost()
	{
		$options = $this->topic->GetPollOptions();
		if(!array_key_exists($_POST["poll_option"], $options))
		{
			$this->_message = Lang::Message(LMSG_REPORT);
			return false;			
		}
		
		if($this->user->GetPollVote($this->topic->GetPollId()))
		{
			$this->_message = Lang::Message(LMSG_FORUM_POLL_ALREADY_VOTED);
			return false;
		}
		
		if(time() > $this->topic->GetPollEnd())
		{
			$this->_message = Lang::Message(LMSG_FORUM_POLL_TIME_EXPIRED);
			return false;			
		}
		
		$userAcc = new Account();
		$userAcc->load($this->user->GetAccountId());
		
		if($this->topic->PollIsOnlyForPremiums() && $userAcc->getPremDays() == 0)
		{
			$this->_message = Lang::Message(LMSG_FORUM_POLL_ONLY_FOR_PREMIUM);
			return false;			
		}
		
		if($userAcc->getHighLevel() < $this->topic->GetPollMinLevel())
		{
			$this->_message = Lang::Message(LMSG_FORUM_POLL_NEED_MIN_LEVEL, $this->topic->GetPollMinLevel());
			return false;			
		}
		
		$visible = 0;
		
		if($_POST["visibility"] == "true")
			$visible = 1;
		
		$this->user->SetPollVote($_POST["poll_option"], $visible);
		
		$this->_message = Lang::Message(LMSG_FORUM_POLL_VOTE_DONE);
		return true;
	}
	
	function Draw()
	{
		global $module;
		
		$table = new HTML_Table();
		
		$module .= "<p><span style='font-weight: bold;'>Topico:</span> <span style='font-size: 14px; font-weight: bold;'><a href='?ref=forum.topic&v={$this->topic->GetId()}'>{$this->topic->GetTitle()}</a></span></p>";
		
		if($this->topic->IsPoll())
		{
			$pollTable = new HTML_Table();
			$pollTable->AddDataRow("<span style='float:left;'>Enquete</span> <span style='float: right;'>Termina em: ".Core::formatDate($this->topic->GetPollEnd())."</span>");
			
			$pollTable->AddField($this->topic->GetPollText());
			$pollTable->AddRow();
			
			$options = $this->topic->GetPollOptions();
			
			$optString = "";
			
			$hasVoted = $this->user->GetPollVote($this->topic->GetPollId());
			
			foreach($options as $optionid => $option)
			{
				$field = new HTML_Input();
				$field->SetName("poll_option");
				
				/* Por enquanto não iremos suportas multiplas seleções devido a complexidade de implementação do recurso no codigo */
				/*if($this->topic->PollIsMultipleSelection())
					$field->IsCheackeable();
				else*/
					$field->IsRadio();
					
				$field->SetValue($optionid);
				
				if($hasVoted)
				{
					$field->IsDisabled();
					
					if($optionid == $hasVoted)
						$field->IsDefault();
				}
				
				$optString .= "{$field->Draw()} {$option["option"]} <br>";
			}
			
			$pollTable->AddField($optString);
			$pollTable->AddRow();			
			
			if(!$hasVoted)
			{
				$isVisible = new HTML_Input();
				$isVisible->SetName("visibility");
				$isVisible->IsCheackeable();
				$isVisible->SetValue("true");
				
				$pollTable->AddField($isVisible->Draw()." Tornar meu voto publico? (Os seus posts nesta enquete irá exibir a opção que você votou)");
				$pollTable->AddRow();
			}						
			
			$button = new HTML_Input();
			$button->IsButton();
			$button->SetValue("Votar");
			
			if($hasVoted)
				$button->IsDisabled();
			
			$module .= "
			<form action='{$_SERVER['REQUEST_URI']}' method='post'>
				<fieldset>
					{$pollTable->Draw()}
				
					<p>
						{$button->Draw()}
					</p>				
				</fieldset>
			</form>";
		}
		
		
		$table->AddDataRow("<span style='float: left;'>#1</span> <span style='float: right; font-weight: normal;'>".Core::formatDate($this->topic->GetDate())."</span>");
		
		$character = new Character();
		$character->load($this->user->GetPlayerId());
		
		$group = new t_Group($character->getGroup());
		
		$string = "
		<a href='?ref=character.view&name={$character->getName()}'>{$character->getName()}</a><br>
		{$group->GetByName()}
		";
		
		$table->AddField($string, 20, "height: 90px; vertical-align: top;");
		
		$string = "
		<p><span style='font-size: 12px; font-weight: bold;'>{$this->topic->GetTitle()}</span></p>
		<p class='line'></p>
		<p style='margin-top: 25px;'>{$this->topic->GetTopic()}</p>";
		
		$table->AddField($string, null, "vertical-align: top;");
		$table->AddRow();
		
		$module .= "{$table->Draw()}";
	}
}

$view = new View();
?>