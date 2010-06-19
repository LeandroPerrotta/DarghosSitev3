<?php
class View
{
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $topic, $user;	
	
	function View()
	{
		if(!$_GET['v'] && !$_GET['removemsg'])
		{
			return;
		}
		
		if(!$this->Prepare())
		{
			Core::sendMessageBox(Lang::Message(LMSG_ERROR), $this->_message);
			return false;			
		}
		else
		{
			if($_GET['removemsg'])
			{
				Core::sendMessageBox(Lang::Message(LMSG_SUCCESS), $this->_message);
				return true;					
			}
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
				
				global $module;
				$module .= "<p><a class='buttonstd' href='?ref=forum.topic&v={$_GET['v']}'>Voltar</a></p>";				
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
		
		if($_GET['v'])
		{
			$this->topic = new Forum_Topics();
			
			$page = 0;
			
			if($_GET["p"])
				$page = $_GET["p"];
				
			$start = $page * 10;
			
			$this->topic->SetPostStart($start);
			
			if(!$this->topic->Load($_GET['v']))
			{
				$this->_message = Lang::Message(LMSG_REPORT);
				return false;
			}
		}
		elseif($_GET['removemsg'])
		{
			if($this->user->GetAccount()->getGroup() < GROUP_COMMUNITYMANAGER)
			{
				$this->_message = Lang::Message(LMSG_REPORT);
				return false;				
			}
			
			Forum_Topics::DeletePost($_GET['removemsg']);
			$this->_message = "Post removido com sucesso!";
		}
		
		return true;
	}
	
	function Post()
	{
		if($_POST["poll_option"] && $this->topic->IsPoll())
		{
			return $this->PollPost();
		}
		
		if($_POST["user_post"])
		{
			if(strlen($_POST["user_post"]) > 2048)
			{
				$this->_message = Lang::Message(LMSG_FORUM_POST_TOO_LONG);
				return false;				
			}
			
			$this->topic->SendPost(strip_tags($_POST["user_post"]), $this->user->GetId());
			
			$this->_message = Lang::Message(LMSG_FORUM_POST_SENT);
			return true;
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
			
			if(time() > $this->topic->GetPollEnd())
				$pollTable->AddDataRow("<span style='float:left;'>Enquete</span> <span style='float: right;'>Terminou em: ".Core::formatDate($this->topic->GetPollEnd())."</span>");
			else
				$pollTable->AddDataRow("<span style='float:left;'>Enquete</span> <span style='float: right;'>Termina em: ".Core::formatDate($this->topic->GetPollEnd())."</span>");
			
			$pollTable->AddField($this->topic->GetPollText());
			$pollTable->AddRow();
			
			$options = $this->topic->GetPollOptions();
			
			$optString = "";
			
			$vote = $this->user->GetPollVote($this->topic->GetPollId());
			
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
				
				if($vote)
				{
					$field->IsDisabled();
					
					if($optionid == $vote["id"])
						$field->IsDefault();
				}
				
				$optVotes = null;
				
				if(time() > $this->topic->GetPollEnd())
					$optVotes = " - {$option["votes"]} voto(s) ou ".round(($option["votes"] / $this->topic->GetTotalVotes()) * 100, 2)."%";
					
				$optString .= "{$field->Draw()} {$option["option"]} {$optVotes}<br>";
			}
			
			$pollTable->AddField($optString);
			$pollTable->AddRow();			
			
			if(!$vote)
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
			
			if($vote)
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
		
		$table->AddDataRow("<span style='float: right; font-weight: normal;'>".Core::formatDate($this->topic->GetDate())."</span>");
		
		$author = new Forum_User();
		$author->Load($this->topic->GetAuthorId());
		
		$character = new Character();
		$character->load($author->GetPlayerId());
		
		$group = new t_Group($character->getGroup());
		
		$string = "
		<a href='?ref=character.view&name={$character->getName()}'>{$character->getName()}</a><br>
		{$group->GetByName()}
		";
		
		$table->AddField($string, 20, "height: 90px; vertical-align: top;");
		
		$string = "
		<p><span style='font-size: 12px; font-weight: bold;'>{$this->topic->GetTitle()}</span></p>
		<p class='line'></p>
		<p style='margin-top: 25px;'>".nl2br($this->topic->GetTopic())."</p>";
		
		$table->AddField($string, null, "vertical-align: top;");
		$table->AddRow();
		
		$module .= "{$table->Draw()}";
		
		$table = new HTML_Table();
		
		if($this->topic->GetPostCount() != 0)
		{			
			$page = 0;
			
			if($_GET["p"])
				$page = $_GET["p"];
				
			$start = $page * 10;			
			
			$i = $start + 1;
			foreach($this->topic->GetPosts() as $key => $post)
			{
				//header
				$string = "<span style='float: left; font-weight: bold;'>#{$i}</span> <span style='float: right; font-weight: normal;'>".Core::formatDate($post["date"])."</span>";				
				
				$table->addField($string, null, null, 2, true);
				$table->AddRow();
				
				//body user
				$user_post = new Forum_User();
				$user_post->Load($post["user_id"]);
				
				$user_character = new Character();
				$user_character->load($user_post->GetPlayerId());
				
				$group = new t_Group($user_character->getGroup());				
				
				$string = "
				<a href='?ref=character.view&name={$user_character->getName()}'>{$user_character->getName()}</a><br>
				{$group->GetByName()}
				";
				
				$table->AddField($string, 20, "height: 90px; vertical-align: top;");
				
				$string = "";
				
				//body post
				
				$vote = $user_post->GetPollVote($this->topic->GetPollId());
				
				if($this->topic->IsPoll() && $vote && $vote["public"] == 1)
				{
					$string .= "<div style='border: 1px #9f9d9d solid; line-height:200%; padding: 0px; padding-left: 5px;'>Meu voto: <span style='font-weight: bold;'>{$vote["option"]}</span></div>";
				}
				
				if($this->user->GetAccount()->getGroup() >= GROUP_COMMUNITYMANAGER)
				{
					$string .= "					
					<div style='margin: 0px; padding: 0px; text-align: right;'><a onclick='return confirm(\"Você tem certeza que deseja deletar o post com id #{$post["id"]} de {$user_character->getName()}?\")' href='?ref=forum.topic&removemsg={$post["id"]}'>Deletar</a></div>";
				}
				
				$string .= "
				<!-- <p><span style='font-size: 12px; font-weight: bold;'></span></p>
				<p class='line'></p> -->
				<div style='margin: 0px; margin-top: 10px; padding: 0px;'><p>".nl2br($post["post"])."</p></div>";
				
				$table->AddField($string, null, "vertical-align: top;");				
				
				$table->AddRow();
		
				$i++;				
			}
		}
	
		$now = 0;
		$page = 0;
		
		if(!$_GET["p"])
			$page = 1;
		else
		{
			$now = $_GET["p"];
			$page = $_GET["p"] + 1;
		}
			
		$ultima = floor($this->topic->GetPostCount() / 10);
		
		$module .= "<div>";
		
		if($now > 0)
			$module .= "<span style='margin-top: 10px; float: left;'><a href='?ref=forum.topic&v={$_GET['v']}'>Primeira</a> | <a href='?ref=forum.topic&v={$_GET['v']}&p=".($now - 1)."'>Anterior</a></span>";
			
		$module .= "<span style='margin-top: 10px; float: right;'>";	

		$havenext = false;
		
		if($now != $ultima)
		{
			$module .= "<a href='?ref=forum.topic&v={$_GET['v']}&p=".($now + 1)."'>Proximo</a>";
			$havenext = true;
		}		
		
		if($now < $ultima)
		{
			if($havenext)
			{
				$module .= " | ";
			}			
			
			$module .= "<a href='?ref=forum.topic&v={$_GET['v']}&p={$ultima}'>Ultima</a>";
		}
		
		$module .= "</span>";
			
		$module .= "</div>";
		$module .= "{$table->Draw()}";
		
		$post = new HTML_Input();
		$post->SetName("user_post");
		$post->IsTextArea(7, 65);
		
		$button = new HTML_Input();
		$button->IsButton();
		$button->SetValue("Enviar");
		
		$table = new HTML_Table();
		$table->AddDataRow("Postar comentario <span class='tooglePlus'></span>");
		$table->IsDropDownHeader();
		
		$string = "
			<div style='text-align: center;'>{$post->Draw()}
			
			<p>
				{$button->Draw()}
			</p></div>";
		
		$table->AddField($string);
		$table->AddRow();
		
		$module .= "
		<form action='{$_SERVER['REQUEST_URI']}' method='post'>
			<fieldset>
				{$table->Draw()}				
			</fieldset>
		</form>";
	}
}

$view = new View();
?>