<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
	<head>
		<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
		
		<title><? echo CONFIG_SITENAME; ?></title>
		
		<link href="<?php echo $layoutDir; ?>style.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="default.css" media="screen" rel="stylesheet" type="text/css" />
		
		<script src="<?php echo $layoutDir; ?>functions.js" type="text/javascript"></script>
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
								<div><strong>Navegação</strong></div>
								<ul class="always_viewable">
									<li><a href="?ref=news.last">Últimas Notícias </a></li>
									<li><a href="http://forum.darghos.com.br/index.php?board=1.0">Arquivo de Notícias </a></li>
									<li><a href="?ref=general.about">Sobre o Darghos </a></li>
									<li><a href="http://forum.darghos.com.br/index.php?board=15.0">Darghos FAQ </a></li>										
									<li><a href="http://forum.darghos.com.br/index.php?topic=2.new#new">Como jogar? </a></li>
									<li><a href="http://forum.darghos.com.br/">Forum </a></li>
									<li><a href="http://forum.darghos.com.br/index.php?board=6.0">Darghopédia </a></li>								
									<li><a href="http://forum.darghos.com.br/index.php?board=3.0">Feedbacks </a></li>									
								</ul>
							</li>
							
							<?php if(!$_SESSION['login']){ ?>
							<?php 
								$menudropdown['accounts']['status'] = null;
								$menudropdown['accounts']['button'] = "tooglePlus";
							
								if($_COOKIE['menudropdown_accounts'])
								{ 
									if($_COOKIE['menudropdown_accounts'] == "true")
									{ 
										$menudropdown['accounts']['status'] = "class='viewable'"; 
										$menudropdown['accounts']['button'] = "toogleMinus";
									}
									elseif($_COOKIE['menudropdown_accounts'] == "false")
									{
										$menudropdown['accounts']['status'] = null;
										$menudropdown['accounts']['button'] = "tooglePlus";
									}	
								} 
							?>
														
							<li>
								<div name="accounts"><strong>Contas</strong> <span class="<?php echo $menudropdown['accounts']['button']; ?>"></span></div>
								<ul <?php echo $menudropdown['accounts']['status']; ?>>
									<li><a href="?ref=account.register">Registrar-se </a></li>
									<li><a href="?ref=account.login">Log-in </a></li>
									<li><a href="?ref=account.recovery">Recuperar Conta </a></li>
									<li><a href="?ref=account.premium">Conta Premium </a></li>
								</ul>
							</li>
							<?php } else { ?>
							<li>
								<div><strong>Minha Conta</strong></div>
								<ul class="always_viewable">
									<li><a href="?ref=account.main">Main </a></li>
									<li><a href="?ref=account.logout">Log-out </a></li>
								</ul>
							</li>
							
							
							<?php 
								$menudropdown['contribute']['status'] = null;
								$menudropdown['contribute']['button'] = "tooglePlus";
							
								if($_COOKIE['menudropdown_contribute'])
								{ 
									if($_COOKIE['menudropdown_contribute'] == "true")
									{ 
										$menudropdown['contribute']['status'] = "class='viewable'"; 
										$menudropdown['contribute']['button'] = "toogleMinus";
									}
									elseif($_COOKIE['menudropdown_contribute'] == "false")
									{
										$menudropdown['contribute']['status'] = null;
										$menudropdown['contribute']['button'] = "tooglePlus";
									}	
								} 
							?>							
							<li>
								<div name="contribute"><strong>Conta Premium</strong> <span class="<?php echo $menudropdown['contribute']['button']; ?>"></span></div>
								<ul <?php echo $menudropdown['contribute']['status']; ?>>
									<li><a href="?ref=account.premium">Conta Premium </a></li>
									<li><a href="?ref=contribute.order">Efetuar Pedido </a></li>
									<li><a href="?ref=contribute.myorders">Meus Pedidos </a></li>
									<li><a href="?ref=account.itemshop_log">Historico Item Shop </a></li>
								</ul>
							</li>			
																									
							<?php } ?>
				
							<?php 
								$menudropdown['community']['status'] = "class='viewable'";
								$menudropdown['community']['button'] = "toogleMinus";
							
								if($_COOKIE['menudropdown_community'])
								{ 
									if($_COOKIE['menudropdown_community'] == "true")
									{ 
										$menudropdown['community']['status'] = "class='viewable'"; 
										$menudropdown['community']['button'] = "toogleMinus";
									}
									elseif($_COOKIE['menudropdown_community'] == "false")
									{
										$menudropdown['community']['status'] = null;
										$menudropdown['community']['button'] = "tooglePlus";
									}	
								} 
							?>							
							<li>
								<div name="community"><strong>Comunidade</strong> <span class="<?php echo $menudropdown['community']['button']; ?>" ></span></div> 
								<ul <?php echo $menudropdown['community']['status']; ?>>
									<li><a href="?ref=character.view">Personagens</a></li>
									<li><a href="?ref=community.highscores">Highscores</a></li>
									<li><a href="?ref=community.guilds">Guildas</a></li>
									<li><a href="?ref=community.houses">Casas</a></li>
									<li><a href="?ref=community.lastdeaths">Últimas Mortes</a></li>
									<li><a href="?ref=status.whoisonline">Quem está Online?</a></li>
								</ul>
							</li>	
							
							<?php 
							$info = new OTS_ServerInfo(STATUS_ADDRESS, STATUS_PORT);
							$status = $info->info(OTS_ServerStatus::REQUEST_BASIC_SERVER_INFO | OTS_ServerStatus::REQUEST_OWNER_SERVER_INFO | OTS_ServerStatus::REQUEST_MISC_SERVER_INFO | OTS_ServerStatus::REQUEST_PLAYERS_INFO | OTS_ServerStatus::REQUEST_MAP_INFO);
							?>
							
							<li>
								<div><strong>Server Status</strong></div>
								<ul class="always_viewable" >
									<li>	
										<div>								
											<p>
												<?php 
												if(!$status)
												{
													echo "
													<em>Status:</em> <font color='#ec0404'><b>offline</b></font>
													";
												}
												else
												{
													$seconds = $status->getUptime() % 60;
													$uptime = floor($status->getUptime() / 60);
											
													$minutes = $uptime % 60;
													$uptime = floor($uptime / 60);
											
													$hours = $uptime % 24;
													$uptime = floor($uptime / 24);
											
													$days = $uptime % 365;
													
													$uptime = ($days >= 1) ? "{$days}d {$hours}h {$minutes}m" : "{$hours}h {$minutes}m";
													
													echo "
													<em>Status:</em> <font color='#00ff00'><b>online</b></font><br />
													<em>Players:</em> {$status->getOnlinePlayers()}<br />
													<em>Uptime:</em> {$uptime}
													";	
												}
												?>
											</p>
										</div>	
									</li>						
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