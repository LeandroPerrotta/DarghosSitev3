<?
class Tickets
{
	private $db, $data = array(), $responses = array();
	
	function __construct()
	{
		global $db;
		$this->db = $db;
	}
	
	function load($id)
	{
		$query = $this->db->query("SELECT id, player_id, account, title, question, send_date, type, closed, last_update, fixed, attachment FROM wb_tickets WHERE id = $id");
		
		if($query->numRows() != 0)
		{
			$fetch = $query->fetch();
			
			$this->data["id"] = $fetch->id;
			$this->data["player_id"] = $fetch->player_id;
			$this->data["account"] = $fetch->account;
			$this->data["title"] = $fetch->title;
			$this->data["question"] = $fetch->question;
			$this->data["send_date"] = $fetch->send_date;
			$this->data["type"] = $fetch->type;
			$this->data["closed"] = $fetch->closed;
			$this->data["last_update"] = $fetch->last_update;
			$this->data["fixed"] = $fetch->fixed;
			$this->data["attachment"] = $fetch->attachment;
									
			return true;
		}
		else 
		{
			return false;
		}
			
	}
	
	function loadResponses($id)
	{
		$query = $this->db->query("SELECT id, ticket_id, text, by_name, send_date FROM wb_tickets_answers WHERE ticket_id = '".$id."' ORDER by send_date ASC");
			
		while($fetch = $query->fetch())
		{
			if($n < 5)
			{
				$n = 0;
				$this->responses[$n] = $fetch->text;
				$n++;
			}
		}
	}
	
	function sendNew($id, $player_id, $account, $title, $question, $send_date, $type, $closed, $last_update, $fixed, $attachment = "")
	{
		
		$this->db->query("INSERT INTO wb_tickets values ('{$id}', '{$player_id}', '{$account}', '{$title}', '{$question}', '{$send_date}', '{$type}', '{$closed}', '{$last_update}', '{$fixed}', '{$attachment}')");

	}
	
	function getID()
	{
		return $this->data['id'];
	}
	
	function getPlayerId()
	{
		return $this->data['player_id'];
	}
	
	function getAccount()
	{
		return $this->data['account'];
	}

	function getTitle()
	{
		return $this->data['title'];
	}
	
	function getQuestion()
	{
		return $this->data['question'];
	}
	
	function getSendDate()
	{
		return $this->data['send_date'];
	}
	
	function getType()
	{
		return $this->data['type'];
	}
	
	function getClosed()
	{
		return $this->data['closed'];
	}
	
	function getLastUpdate()
	{
		return $this->data["last_update"];
	}
	
	function getAttachment()
	{
		return $this->data["attachment"];
	}
	
	function getResponsesText()
	{
		return $this->responses;
	}
	
	function isFixed()
	{
		return $this->data['fixed'];
	}

	function sendAnotherReply($id, $ticket_id, $text, $author, $send_date)
	{
		$this->db->query("INSERT INTO wb_tickets_answers values ('{$id}', '{$ticket_id}', '{$text}', '{$author}', '{$send_date}')");
	}
	
	function changeState($id, $state)
	{
		$this->db->query("UPDATE wb_tickets SET `closed` = {$state} WHERE id = {$id}");
	}
	
	function setUpdate($id, $state)
	{
		$this->db->query("UPDATE wb_tickets SET `last_update` = {$state} WHERE id = {$id}");
	}
	
	function killReply($id)
	{
		$this->db->query("DELETE FROM wb_tickets_answers WHERE id = '{$id}'");
	}
	
	function changeFixed($id, $fixed)
	{
		$this->db->query("UPDATE wb_tickets SET `fixed` = {$fixed} WHERE id = {$id}");
	}
	
}
?>