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
							<?php echo Menus::drawLeftMenu(); ?>															
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
								<?php echo Menus::drawRightMenu(); ?>	
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
