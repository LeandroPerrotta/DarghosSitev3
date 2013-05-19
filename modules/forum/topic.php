<?php
use \Core\Configs;
use \Framework\Forums\Topics;
use \Framework\Forums\User;
class View
{
	//variables
	private $_message;	
	
	//custom variables
	private $loggedAcc, $topic, $user;	
	
	//html fields
	private $_bantype, $_banreason, $_topic, $_title;
	
	function View()
	{
		if(!$_GET['v'] && !$_GET['removemsg'] && !$_GET['banuser'])
		{
			return;
		}
		
		$this->loggedAcc = \Framework\Account::loadLogged();
		
		if(!$this->Prepare())
		{
			\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			return false;			
		}
		else
		{
			if($_GET['removemsg'] || $_GET['delete'])
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
				return true;					
			}
		}	
		
		if($_GET['banuser'])
		{
			$this->_bantype = new \Framework\HTML\SelectBox();
			$this->_bantype->SetName("bantype");
			$this->_bantype->SetSize(250);
			$this->_bantype->AddOption("24 horas sem postar", Topics::BAN_DAY);
			$this->_bantype->AddOption("7 dias sem postar", Topics::BAN_WEEK);
			$this->_bantype->AddOption("30 dias sem postar", Topics::BAN_MONTH);
			$this->_bantype->AddOption("Proibido de postar para sempre", Topics::BAN_PERSISTENT);
			
			$this->_banreason = new \Framework\HTML\Input();
			$this->_banreason->IsTextArea();
			$this->_banreason->SetName("banreason");		
		}
		
		if($_POST)
		{
			if($this->loggedAcc && $_GET['banuser'] && $this->loggedAcc->getGroup() >= t_Group::CommunityManager)
			{
				if(!$this->PostBanUser())
				{
					\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
				}			

				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
				
				return true;
			}
			
			if($this->loggedAcc && $_GET['edit'] && $this->loggedAcc->getGroup() >= t_Group::CommunityManager)
			{
				if(!$this->PostEditTopic())
				{
					\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
				}			

				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
				
				return true;
			}			
			
			if(!$this->Post())
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->ERROR), $this->_message);
			}			
			else
			{
				\Core\Main::sendMessageBox(\Core\Lang::Message(\Core\Lang::$e_Msgs->SUCCESS), $this->_message);
				
				global $module;
				$module .= "<p><a class='buttonstd' href='?ref=forum.topic&v={$_GET['v']}'>Voltar</a></p>";				
				return true;
			}
		}	

		if($this->loggedAcc && $this->loggedAcc->getGroup() >= t_Group::CommunityManager && $_GET['banuser'])
		{
			$this->DrawBanUser();
			return true;
		}	
		
		if($this->loggedAcc && $this->loggedAcc->getGroup() >= t_Group::CommunityManager && $_GET['edit'])
		{
			$this->DrawEditTopic();
			return true;
		}		
		
		$this->Draw();
		return true;		
	}
	
	function Prepare()
	{
		$this->user = new User();
		
		if($_GET['v'])
		{
			$this->topic = new Topics();
			
			$page = 0;
			
			if($_GET["p"])
				$page = $_GET["p"];
				
			$start = $page * 10;
			
			$this->topic->SetPostStart($start);
			
			if(!$this->topic->Load($_GET['v']))
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;
			}
			
			if($this->topic->IsNotice() && ((!$this->loggedAcc || $this->loggedAcc->getGroup() < t_Group::GameMaster) && !Configs::Get(Configs::eConf()->ENABLE_PLAYERS_COMMENT_NEWS)))
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;
			}
			
			if($this->loggedAcc && $_GET['edit'] && $_GET['edit'] == "1")
			{
				$this->_title = new \Framework\HTML\Input();
				$this->_title->SetName("topic_title");
				$this->_title->SetValue($this->topic->GetTitle());
				
				$this->_topic = new \Framework\HTML\Input();
				$this->_topic->IsTextArea(25, 80);
				$this->_topic->SetName("topic_content");
				$this->_topic->SetId("topic_content");	
				$this->_topic->SetValue($this->topic->GetTopic());		
			}	
			elseif($this->loggedAcc && $_GET['delete'] && $_GET['delete'] == "1")
			{
				if($this->loggedAcc->getGroup() < t_Group::CommunityManager)
				{
					$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
					return false;				
				}
				
				$this->topic->SetIsDeleted();
				$this->topic->Save();
				
				$this->_message = "Topico removido com sucesso!";
			}						
		}
		elseif($_GET['removemsg'])
		{
			if($this->loggedAcc && $this->loggedAcc->getGroup() < t_Group::CommunityManager)
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
				return false;				
			}
			
			Topics::DeletePost($_GET['removemsg']);
			$this->_message = "Post removido com sucesso!";
		}	
		
		return true;
	}
	
	function Post()
	{
		if(!$this->loggedAcc)
		{
			\Core\Main::requireLogin();
			return false;
		}
		
		if(!$this->user->LoadByAccount($_SESSION['login'][0]))
		{			
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_ACCOUNT_NOT_HAVE_USER);
			return false;			
		}		
		
		if($_POST["poll_option"] && $this->topic->IsPoll())
		{
			return $this->PollPost();
		}
		
		if($_POST["user_post"])
		{
			if(strlen($_POST["user_post"]) > 2048)
			{
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_POST_TOO_LONG);
				return false;				
			}
			
			$ban = $this->user->IsBannished();
			
			if($ban)
			{
				$t_BanType = new t_ForumBans($ban["type"]);
				
				$bannisher = new User();
				$bannisher->Load($ban["author"]);
				$bannisher_p = new \Framework\Player();
				$bannisher_p->load($bannisher->GetPlayerId());
				
				$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_USER_BANNISHED, \Core\Main::formatDate($ban["date"]), $t_BanType->GetByName(), $bannisher_p->getName(), $ban["reason"]);
				return false;					
			}
			
			$this->topic->SendPost(strip_tags($_POST["user_post"]), $this->user->GetId());
			
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_POST_SENT);
			return true;
		}
	}
	
	function PollPost()
	{		
		$options = $this->topic->GetPollOptions();
		if(!array_key_exists($_POST["poll_option"], $options))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->REPORT);
			return false;			
		}
		
		if($this->user->GetPollVote($this->topic->GetPollId()))
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_POLL_ALREADY_VOTED);
			return false;
		}
		
		if(time() > $this->topic->GetPollEnd())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_POLL_TIME_EXPIRED);
			return false;			
		}
		
		$userAcc = new \Framework\Account();
		$userAcc->load($this->user->GetAccountId());
		
		if($this->topic->PollIsOnlyForPremiums() && $userAcc->getPremDays() == 0)
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_POLL_ONLY_FOR_PREMIUM);
			return false;			
		}
		
		if($userAcc->getHighLevel() < $this->topic->GetPollMinLevel())
		{
			$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_POLL_NEED_MIN_LEVEL, $this->topic->GetPollMinLevel());
			return false;			
		}
		
		$visible = 0;
		
		if($_POST["visibility"] == "true")
			$visible = 1;
		
		$this->user->SetPollVote($_POST["poll_option"], $visible);
		$this->_message = \Core\Lang::Message(\Core\Lang::$e_Msgs->FORUM_POLL_VOTE_DONE);
		return true;
	}
	
	function PostBanUser()
	{
		$user = new User();
		if(!$user->Load($_GET["banuser"]))
		{
			$this->_message = "Usuario não encontrado!";
			return false;
		}
		
		$user->AddBan($_POST['bantype'], time(), $_POST['banreason'], $this->user->GetId());
		$this->_message = "Punição aplicada ao usuario com sucesso!";

		return true;
	}
	
	function PostEditTopic()
	{
		if($this->_title->GetPost())
		{
			$this->topic->SetTitle($this->_title->GetPost());
		}
		
		if($this->_topic->GetPost())
		{
			$this->topic->SetTopic($this->_topic->GetPost());
		}
		
		$this->topic->Save();
		$this->_message = "Topico {$this->_title->GetPost()} editado com sucesso!";

		return true;
	}	
	
	function DrawBanUser()
	{
		global $module;
		
		$module .=	"
		<form action='' method='post'>
			<fieldset>
				
				<p>
					<label for='bantype'>Tipo de punição</label><br />
					{$this->_bantype->Draw()}
				</p>					
				
				<p>
					<label for='banreason'>Motivo da punição</label><br />
					{$this->_banreason->Draw()}
				</p>				
				
				<p id='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' />
				</p>
			</fieldset>
		</form>";		
	}
	
	function DrawEditTopic()
	{
		global $module;
		
		$module .=	"
		<form action='' method='post'>
			<fieldset>
				
				<p>
					<label for='title'>Titulo do Topico</label><br />
					{$this->_title->Draw()}
				</p>					
				
				<p>
					<label for='{$this->_topic->GetName()}'>Conteudo do topico</label><br />
					".\Core\Main::CKEditor($this->_topic->GetName(), $this->_topic->GetValue())."
				</p>				
				
				<p id='line'></p>
				
				<p>
					<input class='button' type='submit' value='Enviar' />
				</p>
			</fieldset>
		</form>";		
	}
	
	function Draw()
	{
		global $module;
		
		$table = new \Framework\HTML\Table();
		
		$module .= "<p><span style='font-weight: bold;'>Topico:</span> <span style='font-size: 14px; font-weight: bold;'><a href='?ref=forum.topic&v={$this->topic->GetId()}'>{$this->topic->GetTitle()}</a></span></p>";
		
		if($this->topic->IsPoll())
		{
			$pollTable = new \Framework\HTML\Table();
			
			if(time() > $this->topic->GetPollEnd())
				$pollTable->AddDataRow("<span style='float:left;'>Enquete</span> <span style='float: right;'>Terminou em: ".\Core\Main::formatDate($this->topic->GetPollEnd())."</span>");
			else
				$pollTable->AddDataRow("<span style='float:left;'>Enquete</span> <span style='float: right;'>Termina em: ".\Core\Main::formatDate($this->topic->GetPollEnd())."</span>");
			
			$pollTable->AddField(nl2br($this->topic->GetPollText()));
			$pollTable->AddRow();
			
			$options = $this->topic->GetPollOptions();
			
			$optString = "";
			
			$vote = $this->user->GetPollVote($this->topic->GetPollId());
			
			foreach($options as $optionid => $option)
			{
				$field = new \Framework\HTML\Input();
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
				
				if(time() > $this->topic->GetPollEnd() || ($this->loggedAcc && $this->loggedAcc->getGroup() >= t_Group::CommunityManager))
					$optVotes = " - {$option["votes"]} voto(s) ou ".round(($option["votes"] / $this->topic->GetTotalVotes()) * 100, 2)."%";
					
				$optString .= "{$field->Draw()} {$option["option"]} {$optVotes}<br>";
			}
			
			$pollTable->AddField($optString);
			$pollTable->AddRow();			
			
			if(!$vote)
			{
				$isVisible = new \Framework\HTML\Input();
				$isVisible->SetName("visibility");
				$isVisible->IsCheackeable();
				$isVisible->SetValue("true");
				
				$pollTable->AddField($isVisible->Draw()." Tornar meu voto publico? (Os seus posts nesta enquete irá exibir a opção que você votou)");
				$pollTable->AddRow();
			}						
			
			$button = new \Framework\HTML\Input();
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
		
		$table->AddDataRow("<span style='float: right; font-weight: normal;'>".\Core\Main::formatDate($this->topic->GetDate())."</span>");
		
		$author = new User();
		$author->Load($this->topic->GetAuthorId());
		
		$player = new \Framework\Player();
		$player->load($author->GetPlayerId());
		
		$group_str = t_Group::GetString($player->getGroup());
		
		$string = "
		<a href='?ref=character.view&name={$player->getName()}'>{$player->getName()}</a><br>
		{$group_str}
		";
		
		$table->AddField($string, 20, "height: 90px; vertical-align: top;");
		
		
		$string = "
		<p><span style='font-size: 12px; font-weight: bold;'>{$this->topic->GetTitle()}</span></p>
		<p class='line'></p>";

		if($this->loggedAcc && $this->loggedAcc->getGroup() >= t_Group::CommunityManager)
		{
			$string .= "					
			<div style='margin: 0px; padding: 0px; text-align: right;'><a onclick='return confirm(\"Você tem certeza que deseja deletar o topico {$this->topic->GetTitle()} com id #{$this->topic->GetId()}?\")' href='?ref=forum.topic&v={$this->topic->GetId()}&delete=1'>Excluir</a> - <a href='?ref=forum.topic&v={$this->topic->GetId()}&edit=1'>Editar</a></div>";
		}		
		
		$string .= "<p style='margin-top: 25px;'>{$this->topic->GetTopic()}</p>";
		
		$table->AddField($string, null, "vertical-align: top;");
		$table->AddRow();
		
		$module .= "{$table->Draw()}";
		
		$table = new \Framework\HTML\Table();
		
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
				$string = "<span style='float: left; font-weight: bold;'>#{$i}</span> <span style='float: right; font-weight: normal;'>".\Core\Main::formatDate($post["date"])."</span>";				
				
				$table->addField($string, null, null, 2, true);
				$table->AddRow();
				
				//body user
				$user_post = new User();
				$user_post->Load($post["user_id"]);
				
				$user_character = new \Framework\Player();
				
				$nochar = false;
				
				//o character escolhido não existe mais... vamos pegar o personagem com mais level entao...
				if(!$user_character->load($user_post->GetPlayerId()))
				{
					$user_acc = $user_post->GetAccount();
					if(count($user_acc->getCharacterList()) > 0)
					{
						$cid = $user_acc->getHighCharacter(true);
						$user_character->load($cid);
					}
					else
					{
						$nochar = true;
					}
				}

				$group_str = t_Group::GetString($user_character->getGroup());	
							
				
				if(!$nochar)
				{
					$voc = new t_Vocation($user_character->getVocation());
					
					$accountType = "Conta Gratuita";
					
					if($user_character->isPremium())
						$accountType = "<font style='color: #00ff00; font-weight: bold;'>Conta Premium</font>";
					
					$string = "
					<span style='font-size: 8pt;'>
					<a href='?ref=character.view&name={$user_character->getName()}'>{$user_character->getName()}</a><br>
					{$group_str}<br>
					<strong>{$voc->GetByName()}</strong><br>
					Mundo: <strong>".t_Worlds::GetString($user_character->getWorldId())."</strong><br>
					Level: <strong>{$user_character->getLevel()}</strong><br>
					{$accountType}
					</span>
					";
				}
				else
				{
					$string = "
					Desconhecido<br>
					Conta sem personagem
					";
				}
				
				$table->AddField($string, 20, "height: 90px; vertical-align: top;");
				
				$string = "";
				
				//body post
				
				$vote = $user_post->GetPollVote($this->topic->GetPollId());
				
				if($this->topic->IsPoll() && $vote && $vote["public"] == 1)
				{
					$string .= "<div style='border: 1px #9f9d9d solid; line-height:200%; padding: 0px; padding-left: 5px;'>Meu voto: <span style='font-weight: bold;'>{$vote["option"]}</span></div>";
				}
				
				if($this->loggedAcc && $this->loggedAcc->getGroup() >= t_Group::CommunityManager)
				{
					$string .= "					
					<div style='margin: 0px; padding: 0px; text-align: right;'><a onclick='return confirm(\"Você tem certeza que deseja deletar o post com id #{$post["id"]} de {$user_character->getName()}?\")' href='?ref=forum.topic&removemsg={$post["id"]}'>Deletar</a> - <a href='?ref=forum.topic&banuser={$user_post->GetId()}'>Punir</a></div>";
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
		
		$post = new \Framework\HTML\Input();
		$post->SetName("user_post");
		$post->SetId("user_post");
		$post->IsTextArea(7, 65);
		$post->OnKeyPress("countCharacters(2048);");
		
		$button = new \Framework\HTML\Input();
		$button->IsButton();
		$button->SetValue("Enviar");
		
		$table = new \Framework\HTML\Table();
		$table->AddDataRow("Postar comentario <span class='tooglePlus'></span>");
		$table->IsDropDownHeader();
		
		$string = "			
		
			<div style='text-align: center;'>{$post->Draw()}
			
			<p>
				{$button->Draw()}
			</p>
			
			<p id='charactersLeft'>Restam 2048 caracteres.</p>
			
			</div>";
		
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