<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
		
		<title><? echo CONFIG_SITENAME; ?></title>
		
		<link href="layout/layout.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="layout/default.css" media="screen" rel="stylesheet" type="text/css" />
		
	</head>
	
	<body>
		<div id="tudo">
			<div id="menu">	
				<ul id="navigation">
					<li>
						<p>navegação</p>
						
						<ul>
							<li><a href="?ref=news.last">Ultimas Notícias</a></li>
							<li><a href="?ref=news.files">Arquivo de Notícias</a></li>
							<li><a href="?ref=general.about">Sobre o Darghos</a></li>
							<li><a href="?ref=general.faq">Darghos FAQ</a></li>
							<li><a href="?ref=general.howplay">Como Jogar?</a></li>
						</ul>
					</li>	
				</ul>	
				
				<ul id="account">
					<li>
						<?
						if(!$_SESSION['login'])
						{
							echo '
							<p>contas</p>
							
							<ul>								
							
							<li><a href="?ref=account.register">Registrar</a></li>
							<li><a href="?ref=account.login">Log-in</a></li>
							<li><a href="?ref=account.recovery">Recuperar Conta</a></li>
							<li><a href="?ref=account.premium">Conta Premium</a></li>';
						}
						else
						{
							echo '
							<p>minha conta</p>
							
							<ul>								
						
							<li><a href="?ref=account.main">Main</a></li>
							<li><a href="?ref=accounts.changepassword">Mudar Senha</a></li>
							<li><a href="?ref=account.changeemail">Mudar E-mail</a></li>
							<li><a href="?ref=account.changeinfos">Mudar Informações</a></li>
							<li><a href="?ref=account.logout">Log-out</a></li>';
						}							
						?>	
						</ul>
					</li>	
				</ul>	
				
				<?
				if($_SESSION['login'])
				{
					echo '
					<ul id="premium">
						<li>
							<p>conta premium</p>
							
							<ul>								
							
								<li><a href="?ref=account.premium">Conta Premium</a></li>
								<li><a href="?ref=contribute.order">Efetuar Pedido</a></li>
								<li><a href="?ref=contribute.myorders">Meus Pedidos</a></li>
							
							</ul>
						</li>	
					</ul>';					
				
					echo '
					<ul id="characters">
						<li>
							<p>personagens</p>
							
							<ul>								
							
								<li><a href="?ref=character.create">Criar</a></li>
								<li><a href="?ref=character.edit">Editar</a></li>
								<li><a href="?ref=character.itemshop">Item Shop</a></li>
								<li><a href="?ref=character.delete">Deletar</a></li>
							
							</ul>
						</li>	
					</ul>';
					
					$account = new Account();
					$account->load($_SESSION['login'][0]);
					
					$list = $account->getCharacterList();
					
					if(is_array($list))
					{	
						echo '
						<ul id="mycharacters">
							<li>
								<p>meus personagens</p>
								
								<ul>								
								';
									foreach($list as $value)
									{
										echo '<li><a href="?ref=character.view&name='.$value.'">'.$value.'</a></li>';
									}	
								
								echo '</ul>
							</li>	
						</ul>						
						';
					}
				}
				?>	
				

				<ul id="community">
					<li>
						<p>comunidade</p>
						
						<ul>
							<li><a href="?ref=character.view">Personagem</a></li>
							<li><a href="?ref=community.highscores">Highscores</a></li>
							<li><a href="?ref=community.guilds">Guildas</a></li>
							<li><a href="?ref=community.houses">Casas</a></li>
							<li><a href="?ref=community.polls">Enquetes</a></li>
							<li><a href="?ref=community.lastdeaths">Últimas Mortes</a></li>
							<li><a href="?ref=status.whoisonline">Quem está online?</a></li>
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
		
	<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	try {
	var pageTracker = _gat._getTracker("UA-3541977-8");
	pageTracker._trackPageview();
	} catch(err) {}</script>	
		
	</body>
</html>