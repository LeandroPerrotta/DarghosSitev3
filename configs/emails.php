<?
$emailmodel[1] = "
<html>
<body>		
<p>Prezado jogador,</p>
<p>A sua conta no Darghos foi criada com sucesso! Memorize as informações acesso a sua conta abaixo:</p>

<p>
	Numero da Conta: <b>".$emailvalue[0]."</b>.<br>
	Chave de Acesso: <b>".$emailvalue[1]."</b>.
</p>

<p>Para você criar seu personagem basta acessar nosso website e acessar sua conta.</p>

<p>Para acessar sua conta clique <a href='".CONFIG_SITEEMAIL."/index.php?ref=account.login'><b>aqui</b></a>.</p>

<p>Nos vemos no Darghos!<br>
Equipe UltraxSoft.</p>
</body>
</html>
";

$emailmodel[2] = "
<html>
<body>		
<p>Prezado jogador,</p>
<p>O numero de sua conta foi recuperado com sucesso! Por favor, memorize este numero para segurança de sua conta.</p>

<p>
	Numero da Conta: <b>".$emailvalue[0]."</b>.<br>
</p>

<p>Para acessar sua conta clique <a href='".CONFIG_SITEEMAIL."/index.php?ref=account.login'><b>aqui</b></a>.</p>

<p>Nos vemos no Darghos!<br>
Equipe UltraxSoft.</p>
</body>
</html>
";

$emailmodel[3] = "
<html>
<body>		
<p>Prezado jogador,</p>
<p>O processo do pedido para gerar uma nova senha para sua conta foi efetuado com sucesso. Clique no link abaixo para receber o e-mail com a sua nova senha.</p>

<p>
	<a href='".CONFIG_SITEEMAIL."?ref=account.recovery&key=".$emailvalue[0]."'>".CONFIG_SITEEMAIL."/index.php?ref=account.recovery&key=".$emailvalue[0]."</a><br>
</p>

<p>Nos vemos no Darghos!<br>
Equipe UltraxSoft.</p>
</body>
</html>
";

$emailmodel[4] = "
<html>
<body>		
<p>Prezado jogador,</p>
<p>O numero de sua conta foi recuperado com sucesso! Por favor, memorize este numero para segurança de sua conta. Clique no link abaixo para receber o e-mail com a sua nova senha.</p>

<p>
	Numero da Conta: <b>".$emailvalue[0]."</b>.<br>
	<a href='".CONFIG_SITEEMAIL."?ref=account.recovery&key=".$emailvalue[1]."'>".CONFIG_SITEEMAIL."/index.php?ref=account.recovery&key=".$emailvalue[1]."</a><br>
</p>

<p>Nos vemos no Darghos!<br>
Equipe UltraxSoft.</p>
</body>
</html>
";

$emailmodel[5] = "
<html>
<body>		
<p>Prezado jogador,</p>
<p>Abaixo segue a nova senha gerada para sua conta.</p>

<p>
	Nova Senha: <b>".$emailvalue[0]."</b>.<br>
</p>

<p>Para acessar sua conta clique <a href='".CONFIG_SITEEMAIL."/index.php?ref=account.login'><b>aqui</b></a>.</p>

<p>Nos vemos no Darghos!<br>
Equipe UltraxSoft.</p>
</body>
</html>
";

$emailsubject[1] = "Conta criada com sucesso!";
$emailsubject[2] = "Recuperação de numero de sua Conta!";
$emailsubject[3] = "Recuperação da senha de sua Conta!";
$emailsubject[4] = "Recuperação do numero e senha de sua Conta!";
$emailsubject[5] = "Recuperação da senha de sua Conta!";
?>