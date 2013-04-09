<?php
namespace Core;
class Emails
{
	const EMSG_VALIDATE_EMAIL 					= 0;
	const EMSG_RECOVERY_ACCOUNT_NAME 			= 1;
	const EMSG_RECOVERY_ACCOUNT_PASSWORD 		= 2;
	const EMSG_RECOVERY_ACCOUNT_BOTH 			= 3;
	const EMSG_RECOVERY_ACCOUNT_NEW_PASSWORD	= 4;
	
	private static $emails = array(), $defaultHead;
	
	static function init()
	{
		self::$defaultHead = "
		<html>
			<head>
				<meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
				<title>%emailtitle%</title>
			</head>
			<body>
				%emailbody%
			</body>
		</html>		
		";
				
		$mails = &self::$emails;
		
		$mails[self::EMSG_VALIDATE_EMAIL] = array(
			"subject" => "Validação de de-mail!",
			"title" => "Validação de e-mail!",
			"body" => "
<p>Caro jogador,</p>
<p>Atravez desta mensagem você poderá validar este endereço de e-mail como o cadastrado em sua conta no ".Configs::Get(Configs::eConf()->WEBSITE_NAME)."</p>

<p>Para a validação ser concluida, basta que você acesse o link abaixo:<br>
<a href='".Configs::Get(Configs::eConf()->WEBSITE_URL)."/?ref=account.validateEmail&code=%value_1%' target='_blank'><b>".Configs::Get(Configs::eConf()->WEBSITE_URL)."/?ref=account.validateEmail&code=%value_1%</b></a>
</p>

<p>Nos vemos no ".Configs::Get(Configs::eConf()->WEBSITE_NAME)."!<br>
".Configs::Get(Configs::eConf()->WEBSITE_TEAM).".</p>

<p style='font-size: 8px;'>Se você não conhece o assunto desta mensagem, por favor, apenas ignore esta mensagem.</p>	
			"
		);
				
		$mails[self::EMSG_RECOVERY_ACCOUNT_NAME] = array(
			"subject" => "Recuperação do nome de sua conta!",
			"title" => "Recuperação do nome de sua conta!",
			"body" => "
<p>Prezado jogador,</p>
<p>O nome de sua conta foi recuperado com sucesso! Por favor, memorize o nome de sua conta para sua segurança.</p>

<p>
	Nome da Conta: <b>%value_1%</b>.<br>
</p>

<p>Para acessar sua conta clique <a href='".Configs::Get(Configs::eConf()->WEBSITE_URL)."/index.php?ref=account.login'><b>aqui</b></a>.</p>

<p>Nos vemos no ".Configs::Get(Configs::eConf()->WEBSITE_NAME)."!<br>
".Configs::Get(Configs::eConf()->WEBSITE_TEAM).".</p>	
			"
		);
		
		$mails[self::EMSG_RECOVERY_ACCOUNT_PASSWORD] = array(
			"subject" => "Recuperação da senha de sua conta!",
			"title" => "Recuperação da senha de sua conta!",
			"body" => "
<p>Prezado jogador,</p>
<p>O processo do pedido para gerar uma nova senha para sua conta foi efetuado com sucesso. Clique no link abaixo para receber o e-mail com a sua nova senha.</p>

<p>
	<a href='".Configs::Get(Configs::eConf()->WEBSITE_URL)."?ref=account.recovery&key=%value_1%'>".Configs::Get(Configs::eConf()->WEBSITE_URL)."/index.php?ref=account.recovery&key=%value_1%</a><br>
</p>

<p>Nos vemos no ".Configs::Get(Configs::eConf()->WEBSITE_NAME)."!<br>
".Configs::Get(Configs::eConf()->WEBSITE_TEAM).".</p>
			"
		);		
		
		$mails[self::EMSG_RECOVERY_ACCOUNT_BOTH] = array(
			"subject" => "Recuperação do nome e senha de sua conta!",
			"title" => "Recuperação do nome e senha de sua conta!",
			"body" => "
<p>Prezado jogador,</p>
<p>O nome de sua conta foi recuperado com sucesso! Por favor, memorize este nome para segurança de sua conta. Clique no link abaixo para receber o e-mail com a sua nova senha.</p>

<p>
	Nome da Conta: <b>%value_1%</b>.<br>
	<a href='".Configs::Get(Configs::eConf()->WEBSITE_URL)."?ref=account.recovery&key=%value_2%'>".Configs::Get(Configs::eConf()->WEBSITE_URL)."/index.php?ref=account.recovery&key=%value_2%</a><br>
</p>

<p>Nos vemos no ".Configs::Get(Configs::eConf()->WEBSITE_NAME)."!<br>
".Configs::Get(Configs::eConf()->WEBSITE_TEAM).".</p>
			"
		);		

		$mails[self::EMSG_RECOVERY_ACCOUNT_NEW_PASSWORD] = array(
			"subject" => "Nova senha gerada para sua conta!",
			"title" => "Nova senha gerada para sua conta!",
			"body" => "
<p>Prezado jogador,</p>
<p>Abaixo segue a nova senha gerada para sua conta.</p>

<p>
	Nova Senha: <b>%value_1%</b>.<br>
</p>

<p>Para acessar sua conta clique <a href='".Configs::Get(Configs::eConf()->WEBSITE_URL)."/index.php?ref=account.login'><b>aqui</b></a>.</p>

<p>Nos vemos no ".Configs::Get(Configs::eConf()->WEBSITE_NAME)."!<br>
".Configs::Get(Configs::eConf()->WEBSITE_TEAM).".</p>
			"
		);			
	}
	
	static function getBody($email, $args)
	{
		$n = 1;
		foreach($args as $value)
		{
			self::$emails[$email]["body"] = str_replace("%value_{$n}%", $value, self::$emails[$email]["body"]);
			$n++;
		}
		
		$str = str_replace("%emailtitle%", self::$emails[$email]["title"], self::$defaultHead);
		$str = str_replace("%emailbody%", self::$emails[$email]["body"], self::$defaultHead);
		
		return $str;
	}
	
	static function getSubject($email)
	{
		return self::$emails[$email]["subject"];
	}
	
	static function send($to, $email, $args = null, $from = NULL) 
	{		
		if(!$from)
			$from = Configs::Get(Configs::eConf()->WEBSITE_URL);
		
		$mail = new \PHPMailer();
		
		$mail->IsHTML(true);
		$mail->IsSMTP();
		//$mail->SMTPDebug = true;

		$mail->SMTPAuth   = true;
		
		if(Configs::Get(Configs::eConf()->SMTP_USE_SSL))
		    $mail->SMTPSecure = 'ssl';
		    
		$mail->Host       = Configs::Get(Configs::eConf()->SMTP_HOST);
		$mail->Port       = Configs::Get(Configs::eConf()->SMTP_PORT);

		$mail->Username   = Configs::Get(Configs::eConf()->SMTP_USER);
		$mail->Password   = Configs::Get(Configs::eConf()->SMTP_PASSWORD);
			
		$mail->FromName = Configs::Get(Configs::eConf()->WEBSITE_NAME);
		$mail->From = Configs::Get(Configs::eConf()->SMTP_USER);
		$mail->CharSet = "utf-8";
			
		$mail->AddAddress($to);

		$mail->Subject = self::getSubject($email);
		$mail->Body = self::getBody($email, $args);
		
		if ($mail->Send()) 
		{
			return true;
		}
		
		return false;
	}	
}