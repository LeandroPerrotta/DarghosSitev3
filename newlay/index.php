<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
			<div id="fog-screen">
			
			</div>
			<div id="wrapper_b">				
				<h1 class='header'><span>Darghos Server</span></h1> <!-- tudo que ficar dentro de <span> será escondido pelo CSS, deixe o texto para que deficientes visuais possam saber o nome do site -->
				
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
									<li><a href="?ref=contribute.order"><span style="font-weight: bold"><? echo $menu['PREMIUM.ORDER']; ?></span></a></li>
									<li><a href="?ref=itemshop.purchase"><span style="font-weight: bold"><? echo $menu['PREMIUM.ITEM_SHOP']; ?></span></a></li>
									<li><a href="?ref=contribute.myorders"><? echo $menu['PREMIUM.MY_ORDERS']; ?></a></li>
								</ul>
							</li>								
							
								<?php 
								if($account->getGroup() >= GROUP_COMMUNITYMANAGER) 
								{
								?>
								
								<li>
									<div><strong>Admin Panel</strong></div>
									<ul class="always_viewable" >
										<li><a href="?ref=adv.fastnews">Noticia Rápida </a></li>						
										<li><a href="?ref=forum.newtopic">Novo Tópico </a></li>
										<li><a href="?ref=adv.emailcampaign">Campanha de Email</a></li> 
										<!-- 
										<li><a href="?ref=adv.tutortest">Questões Tutortest </a></li> 
										<li><a href="?ref=tickets.super_list">Ticket System</a></li>	
										<li><a href="?ref=adv.addprize">Permitir AdPage</a></li>	
										-->
									</ul>
								</li>								
								
								<?php } ?>																		
							<?php 
							} 
							
							$menudropdown['darghopedia']['status'] = "class='viewable'";
							$menudropdown['darghopedia']['button'] = "toogleMinus";
						
							if($_COOKIE['menudropdown_darghopedia'])
							{ 
								if($_COOKIE['menudropdown_darghopedia'] == "true")
								{ 
									$menudropdown['darghopedia']['status'] = "class='viewable'"; 
									$menudropdown['darghopedia']['button'] = "toogleMinus";
								}
								elseif($_COOKIE['menudropdown_darghopedia'] == "false")
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
									<li><a href="?ref=darghopedia.monsterlist"><? echo $menu['DARGHOPEDIA_MONSTERS']; ?></a></li>
									<?php if(ENABLE_REBORN_SYSTEM){ ?><li><a href="?ref=darghopedia.reborn"><? echo $menu['DARGHOPEDIA_REBORN']; ?></a></li><?php } ?>
									<li><a href="?ref=darghopedia.quests"><? echo $menu['DARGHOPEDIA_QUESTS']; ?></a></li>
									<li><a href="?ref=darghopedia.pvp_arenas"><? echo $menu['DARGHOPEDIA_PVP_ARENAS']; ?> <span style="font-size: 8px; color: #00ff00;">(novo!)</span></a></li>
									<li><a href="?ref=darghopedia.week_events"><? echo $menu['DARGHOPEDIA_WEEK_EVENTS']; ?> <span style="font-size: 8px; color: #00ff00;">(novo!)</span></a></li>
								</ul>
							</li>								
				
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
												
							<li>
								<div ><strong>Facebook</strong></div>
								<ul class="always_viewable" >
									<li>	
										<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like-box href="http://www.facebook.com/pages/Darghos/205124342834613" width="180" height="345" style="margin-top: 0px; border: none;" colorscheme="dark" show_faces="true" border_color="" stream="false" header="false"></fb:like-box>
									</li>						
								</ul>
							</li>																			
						</ul>
					</div>
					
					<div id="right">
					
						<div id="content">							
							<? if($patch['urlnavigation']){ ?>
							<div id="nav-bar" style="padding: 0px"><span><? echo $patch['urlnavigation']; ?></span></div>			
							<?php } ?>
							
							<div>								
								<? echo $module; ?>
							</div>
						</div>
						
						<div id="right-menu">
							<ul>
								<?php 
								$query = Core::$DB->query("SELECT `players`, `online`, `uptime`, `afk`, `date` FROM `serverstatus` ORDER BY `date` DESC LIMIT 1");
								$fetch = $query->fetch();
								?>
								
								<li>
									<div><strong>Server Status</strong></div>
									<ul class="always_viewable" >
										<li>	
											<div>								
												<p>
													<?php 
													if($fetch->online == 0 || $fetch->date < time - 60 * 5)
													{
														echo "
														<em>Status:</em> <font color='#ec0404'><b>offline</b></font>
														";
													}
													else
													{
														$seconds = $fetch->uptime % 60;
														$uptime = floor($fetch->uptime / 60);
												
														$minutes = $uptime % 60;
														$uptime = floor($uptime / 60);
												
														$hours = $uptime % 24;
														$uptime = floor($uptime / 24);
												
														$days = $uptime % 365;
														
														$uptime = ($days >= 1) ? "{$days}d {$hours}h {$minutes}m" : "{$hours}h {$minutes}m";
														
														$str = "<em>Status:</em> <font color='#00ff00'><b>online</b></font><br />";
														$str .= "<em>IP:</em> ".STATUS_ADDRESS."<br />";
														$str .= "<em>Port:</em> ".STATUS_PORT."<br />";
														
														if(REMOVE_AFK_FROM_STATUS)
														{
															$str .= "<em>Total connected:</em> ".($fetch->players + $fetch->afk)."<br />";
															$str .= "<em>Playing:</em> {$fetch->players}<br />";
															$str .= "<em>Training:</em> {$fetch->afk}<br />";
														}
														else
														{
															$str .= "<em>Total connected:</em> ".($fetch->players)."<br />";
														}
														
														$str .= "<em>Uptime:</em> {$uptime}<br />";
														$str .= "<em>Ping:</em> <span class='ping'>aguarde...</span>";
														
														
														echo $str;	
													}
													?>
												</p>
											</div>	
										</li>						
									</ul>
								</li>	
								
								<?php 
								$today = new CustomDate();
								
								$end_day = null;
								
								if($today->getHour() > 15) $end_day = $today->getDay();
								else $end_day = $today->getDay() - 1;
								
								$start_day = $end_day - 1;
																
								$make_stamp = new CustomDate(); $make_stamp->_hour = 15; $make_stamp->_month = $today->getMonth(); $make_stamp->_day = $start_day; $make_stamp->_year = $today->getYear();
								
								$start_stamp = $make_stamp->makeDate();		
								
								$make_stamp->_day = $end_day;
								
								$end_stamp = $make_stamp->makeDate();									
								$query = Deaths::getTopFraggers($start_stamp, $end_stamp);	
	
								if($query->numRows() > 0)
								{
								?>
								<li>
									<div class="red"><strong>Top 5 matadores do dia</strong></div>
									<ul class="always_viewable" >
										<?php 									
										$str = "";
										
										$pos = 1;
										while($fetch = $query->fetch())
										{
											$size = (strlen($fetch->name) > 15) ? "8px" : "9px";
											
											$str .= "<li><a href='?ref=character.view&name={$fetch->name}' style='font-size: {$size}'>{$pos}. {$fetch->name} ($fetch->c)</a></li>";
											$pos++;
										}
										
										echo $str
										?>				
									</ul>
								</li>
								<?php 
								}
												
								$result = Battleground::buildRating(Battleground::listAll($start_stamp, $end_stamp));
	
								if(count($result) > 0)
								{
								?>
								<li>
									<div class="red"><strong>Top Battleground Rating</strong></div>
									<ul class="always_viewable" >
										<?php 									
										$str = "";
										
										$pos = 1;
										foreach($result as $key => $value)
										{
											$size = (strlen($value["name"]) > 15) ? "8px" : "9px";
											$str .= "<li><a href='?ref=character.view&name={$value["name"]}' style='font-size: {$size}'>{$pos}. {$value["name"]} ({$value["rating"]})</a></li>";
											$pos++;
											
											if($pos > 5)
												break;
										}
										
										echo $str
										?>				
									</ul>
								</li>
								<?php 
								}
								?>		
							</ul>							
						</div>
					</div>
				</div>
				
				<div id="footer"><p style="margin: 13px;">&copy; 2006~2011 <a href="index.php">Equipe UltraxSoft</a></p></div>
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
