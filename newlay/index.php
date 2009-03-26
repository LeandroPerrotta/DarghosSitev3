<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
	<head>
		<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
		
		<title><? echo CONFIG_SITENAME; ?></title>
		
		<link href="<?php echo $layoutDir; ?>style.css" media="screen" rel="stylesheet" type="text/css" />
		
		<script src="<?php echo $layoutDir; ?>jquery.js" type="text/javascript"></script>
		<script src="<?php echo $layoutDir; ?>lists.js" type="text/javascript"></script>
	</head>
	
	<body>
		<div id="wrapper">
			<div id="wrapper_b">
				<h1><span><? echo CONFIG_SITENAME; ?></span></h1> <!-- tudo que ficar dentro de <span> será escondido pelo CSS, deixe o texto para que deficientes visuais possam saber o nome do site -->
				
				<p id="announcement"><marquee direction=left behavior=scroll onmouseover=this.stop() onmouseout=this.start()>Novos pacotes de Conta Premium! Confira!</marquee></p>
				
				<div id="content_wrapper">
					<div id="left">
						<ul>
							<li>
								<strong>navegação</strong>
								<ul class="always_viewable">
									<li><a href="?ref=news.last">Últimas Notícias </a></li>
									<li><a href="?ref=news.files">Arquivo de Notícias </a></li>
									<li><a href="?ref=general.about">Sobre o Darghos </a></li>
									<li><a href="?ref=general.faq">Darghos FAQ </a></li>
									<li><a href="?ref=general.howplay">Como jogar? </a></li>
								</ul>
							</li>
							
							<?php if(!$_SESSION['login']){ ?>
							<li>
								<strong>contas</strong>
								<ul class="always_viewable">
									<li><a href="?ref=account.register">Registrar-se </a></li>
									<li><a href="?ref=account.login">Log-in </a></li>
									<li><a href="?ref=account.recovery">Recuperar Conta </a></li>
									<li><a href="?ref=account.premium">Conta Premium </a></li>
								</ul>
							</li>
							<?php } else { ?>
							<li>
								<strong>minha conta</strong>
								<ul class="always_viewable">
									<li><a href="?ref=account.main">Main </a></li>
									<li><a href="?ref=account.logout">Log-out </a></li>
								</ul>
							</li>
							
							<li>
								<strong>conta premium</strong>
								<ul class="always_viewable">
									<li><a href="?ref=account.premium">Conta Premium </a></li>
									<li><a href="?ref=contribute.order">Efetuar Pedido </a></li>
									<li><a href="?ref=contribute.myorders">Meus Pedidos </a></li>
									<li><a href="?ref=account.itemshop_log">Historico Item Shop </a></li>
								</ul>
							</li>			
																									
							<?php } ?>
							
							<li>
								<strong>comunidade</strong>
								<ul class="always_viewable">
									<li><a href="?ref=character.view">Personagens</a></li>
									<li><a href="?ref=community.highscores">Highscores</a></li>
									<li><a href="?ref=community.guilds">Guildas</a></li>
									<li><a href="?ref=community.houses">Casas</a></li>
									<li><a href="?ref=community.polls">Enquetes</a></li>
									<li><a href="?ref=community.lastdeaths">Últimas Mortes</a></li>
									<li><a href="?ref=status.whoisonline">Quem está Online?</a></li>
								</ul>
							</li>
						</ul>
					</div>
					
					<div id="right">
					
						<? if($patch['urlnavigation']){ ?>
						<h2><? echo $patch['urlnavigation']; ?></h2>			
						<?php } ?>
						
						<div>
							<? echo $module; ?>
						</div>
					</div>
				</div>
				
				<p id="footer">&copy; 2006~2009 <a href="http://www.ultraxsoft.com">Equipe UltraxSoft</a></p>
			</div>
		</div>
	</body>
</html>