<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
		
		<title>Darghos Website</title>
		
		<link href="layout/layout.css" media="screen" rel="stylesheet" type="text/css" />
		
	</head>
	
	<body>
		<center>
			<div id="tudo">
				<div id="menu">	
					<ul id="navigation">
						<li>
							<p>navegação</p>
							
							<ul>
								<li><a href="?ref=news.last">Últimas Notícias</a></li>
								<li><a href="?ref=news.files">Arquivo de Notícias</a></li>
								<li><a href="?ref=general.about">Sobre o Darghos</a></li>
								<li><a href="?ref=general.faq">Darghos FAQ</a></li>
								<li><a href="?ref=general.downloads">Downloads</a></li>
							</ul>
						</li>	
					</ul>	
					
					<ul id="account">
						<li>
							<p>contas</p>
							
							<ul>
								<li><a href="?ref=account.register">Registrar</a></li>
								<li><a href="?ref=account.login">Login</a></li>
								<li><a href="?ref=account.recovery">Recuperar Conta</a></li>
								<li><a href="?ref=account.donation">Doações</a></li>							
							</ul>
						</li>	
					</ul>	

					<ul id="community">
						<li>
							<p>comunidade</p>
							
							<ul>
								<li><a href="?ref=community.characters">Personagem</a></li>
								<li><a href="?ref=community.highscores">Highscores</a></li>
								<li><a href="?ref=community.guilds">Guildas</a></li>
								<li><a href="?ref=community.houses">Casas</a></li>
								<li><a href="?ref=community.polls">Enquetes</a></li>
								<li><a href="?ref=community.lastdeaths">Últimas Mortes</a></li>
							</ul>
						</li>	
					</ul>						
				</div>		
				
				<div id="content">
					
					<? if($patch['urlnavigation']){ ?>
					<p id="urlnavigation"><? echo $patch['urlnavigation']; ?></p>
					<div id="line1"></div>					
					
					<?php } echo $module; ?>
					
				</div>
			</div>
			
			<div id="footer">
				<p>Pagina gerada em: <?php list($te, $date) = explode(" ", microtime()); echo $te - $t; ?>ms</p>
			</div>	
		</center>
	</body>
</html>