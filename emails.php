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

<p>Para acessar sua conta clique <a href='".CONFIG_SITEEMAIL."index.php?ref=account.login'><b>aqui</b></a>.</p>

<p>Nos vemos no Darghos!<br>
Equipe UltraxSoft.</p>
</body>
</html>
";

$emailsubject[1] = "Conta criada com sucesso!";
?>