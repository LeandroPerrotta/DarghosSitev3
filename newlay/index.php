<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		
		<title><? echo CONFIG_SITENAME; ?></title>
		
		<link href="<?php echo $layoutDir; ?>style.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="default.css" media="screen" rel="stylesheet" type="text/css" />
		<script src="<?php echo $layoutDir; ?>jquery.js" type="text/javascript"></script>
		<script src="<?php echo $layoutDir; ?>functions.js" type="text/javascript"></script>
		<script src="<?php echo $layoutDir; ?>lists.js" type="text/javascript"></script>
		<script src="<?php echo $layoutDir; ?>ext.js" type="text/javascript"></script>
		<script src="<?php echo $layoutDir; ?>ping.js" type="text/javascript"></script>				
	
	</head>
	
	<body>
		<script>
		//sendPing();
		</script>		
	
		<div id="wrapper">
			<div id="wrapper_b">
				<h1><span>Darghos Server</span></h1> <!-- tudo que ficar dentro de <span> será escondido pelo CSS, deixe o texto para que deficientes visuais possam saber o nome do site -->
				
				<div id="announcement"><marquee style="margin: 13px;" direction=left behavior=scroll onmouseover=this.stop() onmouseout=this.start()>Novos pacotes de conta premium! Confira!</marquee></div>
				
				<div id="content_wrapper">
					<div id="left">
						<ul>
							<li>
								<div><strong><? echo $menu['NAVIGATION']; ?></strong></div>
								<ul class="always_viewable">
									<li><a href="?ref=news.last"><? echo $menu['LAST_NEWS']; ?></a></li>
									<li><a href="?ref=general.about"><? echo $menu['ABOUT']; ?></a></li>
									<li><a href="?ref=general.faq"><? echo $menu['FAQ']; ?></a></li>										
									<li><a href="?ref=general.howplay"><? echo $menu['HOW_PLAY']; ?></a></li>
									<li><a href="?ref=general.fansites"><? echo $menu['FANSITES']; ?></a></li>																																	
								</ul>
							</li>
							
							<?php 
								$menudropdown['darghopedia']['status'] = "class='viewable'";
								$menudropdown['darghopedia']['button'] = "toogleMinus";
							
								if($_COOKIE['menudropdown_darghopedia'])
								{ 
									if($_COOKIE['menudropdown_darghopedia'] == "true")
									{ 
										$menudropdown['darghopedia']['status'] = "class='viewable'"; 
										$menudropdown['darghopedia']['button'] = "toogleMinus";
									}
									elseif($_COOKIE['menudropdown_community'] == "false")
									{
										$menudropdown['darghopedia']['status'] = null;
										$menudropdown['darghopedia']['button'] = "tooglePlus";
									}	
								} 
							?>									
							
							<li>
								<div name="darghopedia"><strong><? echo $menu['DARGHOPEDIA']; ?></strong> <span class="<?php echo $menudropdown['darghopedia']['button']; ?>"></span></div>
								<ul <?php echo $menudropdown['darghopedia']['status']; ?>>
									<li><a href="?ref=darghopedia.world"><? echo $menu['DARGHOPEDIA_WORLD']; ?></a></li>
									<?php if(ENABLE_REBORN_SYSTEM){ ?><li><a href="?ref=darghopedia.reborn"><? echo $menu['DARGHOPEDIA_REBORN']; ?></a></li><?php } ?>
									<li><a href="?ref=darghopedia.quests"><? echo $menu['DARGHOPEDIA_QUESTS']; ?></a></li>
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
								<div name="accounts"><strong><? echo $menu['ACCOUNTS']; ?></strong> <span class="<?php echo $menudropdown['accounts']['button']; ?>"></span></div>
								<ul <?php echo $menudropdown['accounts']['status']; ?>>
									<li><a href="?ref=account.register"><? echo $menu['ACCOUNT.REGISTER']; ?></a></li>
									<li><a href="?ref=account.login"><? echo $menu['ACCOUNT.LOGIN']; ?></a></li>
									<li><a href="?ref=account.recovery"><? echo $menu['ACCOUNT.RECOVERY']; ?></a></li>
									<li><a href="?ref=account.premium"><? echo $menu['ACCOUNT.PREMIUM']; ?></a></li>
								</ul>
							</li>
							<?php } else { 
							include_once('classes/account.php');
							$account = new Account();
							$account->load($_SESSION["login"][0]);
							?>
							<li>
								<div><strong>Minha Conta</strong></div>
								<ul class="always_viewable">
									<li><a href="?ref=account.main"><? echo $menu['ACCOUNT.MAIN']; ?></a></li>
									<!-- <li><a href="?ref=account.premiumtest"></a></li> -->
									<!--  <li><a href="?ref=account.importElerian">Conta Premium Elerian </a></li> -->
									<li><a href="?ref=account.logout"><? echo $menu['ACCOUNT.LOGOUT']; ?></a></li>			
								</ul>
							</li>
							
							
						<?php 
								$menudropdown['support']['status'] = null;
								$menudropdown['support']['button'] = "tooglePlus";
							
								if($_COOKIE['menudropdown_support'])
								{ 
									if($_COOKIE['menudropdown_support'] == "true")
									{ 
										$menudropdown['support']['status'] = "class='viewable'"; 
										$menudropdown['support']['button'] = "toogleMinus";
									}
									elseif($_COOKIE['menudropdown_support'] == "false")
									{
										$menudropdown['support']['status'] = null;
										$menudropdown['support']['button'] = "tooglePlus";
									}	
								} 
							?>							
							<li>
								<div name="support"><strong><? echo $menu['SUPPORT']; ?></strong> <span class="<?php echo $menudropdown['support']['button']; ?>"></span></div>
								<ul <?php echo $menudropdown['support']['status']; ?>>
									<li><a href="?ref=tickets.send"><? echo $menu['SUPPORT.OPEN_TICKET']; ?></a></li>
									<li><a href="?ref=tickets.tickets"><? echo $menu['SUPPORT.MY_TICKETS']; ?></a></li>
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
								<div name="contribute"><strong><? echo $menu['PREMIUMS']; ?></strong> <span class="<?php echo $menudropdown['contribute']['button']; ?>"></span></div>
								<ul <?php echo $menudropdown['contribute']['status']; ?>>
									<li><a href="?ref=account.premium"><? echo $menu['ACCOUNT.PREMIUM']; ?></a></li>
									<li><a href="?ref=contribute.order"><? echo $menu['PREMIUM.ORDER']; ?></a></li>
									<li><a href="?ref=contribute.myorders"><? echo $menu['PREMIUM.MY_ORDERS']; ?></a></li>
									<li><a href="?ref=account.itemshop_log"><? echo $menu['PREMIUM.LOG_ITEMSHOP']; ?></a></li>
								</ul>
							</li>								
							
								<?php 
								if($account->getGroup() >= 5) 
								{
								?>
								
								<li>
									<div><strong>Admin Panel</strong></div>
									<ul class="always_viewable" >
										<li><a href="?ref=adv.fastnews">Noticia Rápida </a></li>
										<li><a href="?ref=adv.tutortest">Questões Tutortest </a></li>
										<li><a href="?ref=forum.newtopic">Novo Tópico </a></li>
										<li><a href="?ref=tickets.super_list">Ticket System</a></li>	
										<li><a href="?ref=adv.addprize">Permitir AdPage</a></li>	
									</ul>
								</li>								
								
								<?php } ?>																		
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
								<div name="community"><strong><? echo $menu['COMMUNITY']; ?></strong> <span class="<?php echo $menudropdown['community']['button']; ?>" ></span></div> 
								<ul <?php echo $menudropdown['community']['status']; ?>>
									<li><a href="?ref=character.view"><? echo $menu['COMMUNITY.CHARACTERS']; ?></a></li>
									<li><a href="?ref=community.highscores"><? echo $menu['COMMUNITY.HIGHSCORES']; ?></a></li>
									<li><a href="?ref=community.guilds"><? echo $menu['COMMUNITY.GUILDS']; ?></a></li>
									<li><a href="?ref=community.houses"><? echo $menu['COMMUNITY.HOUSES']; ?></a></li>
									<li><a href="?ref=community.lastdeaths"><? echo $menu['COMMUNITY.LAST_DEATHS']; ?></a></li>
									<li><a href="?ref=community.polls"><? echo $menu['COMMUNITY.POLLS']; ?></a></li>
									<li><a href="?ref=status.whoisonline"><? echo $menu['STATUS.WHO_IS_ONLINE']; ?></a></li>
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
													<em>IP:</em> darghos.com.br<br />
													<em>Port:</em> ".STATUS_PORT."<br />
													<em>Players:</em> {$status->getOnlinePlayers()}<br />
													<em>Uptime:</em> {$uptime}<br />
													<em>Ping:</em> <span class='ping'>aguarde...</span>
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
				
				<div id="footer"><p style="margin: 13px;">&copy; 2006~2010 <a href="http://www.ultraxsoft.com">Equipe UltraxSoft</a></p></div>
			</div>
		</div>
		
		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-9778971-1");
		pageTracker._trackPageview();
		} catch(err) {}</script>	
	</body>
</html>
