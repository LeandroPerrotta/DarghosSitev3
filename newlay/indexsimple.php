<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		
		<title><? echo CONFIG_SITENAME; ?></title>
		
		<link href="<?php echo $layoutDir; ?>style.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="default.css" media="screen" rel="stylesheet" type="text/css" />

	</head>
	
	<body>
		<div id="wrapper">
			<div id="wrapper_b">
				<h1><span><? echo CONFIG_SITENAME; ?></span></h1> <!-- tudo que ficar dentro de <span> ser� escondido pelo CSS, deixe o texto para que deficientes visuais possam saber o nome do site -->
				
				<p id="announcement"><marquee direction=left behavior=scroll onmouseover=this.stop() onmouseout=this.start()>Novos pacotes de Conta Premium! Confira!</marquee></p>
				
				<div id="content_wrapper">
					<div id="left">
						<ul>
							<li>
								<div><strong>Navegação</strong></div>
							</li>
							
														
							<li>
								<div><strong>Contas</strong></div>
							</li>
															
							<li>
								<div><strong>Comunidade</strong></div> 
							</li>							
						</ul>
					</div>
					
					<div id="right">
					
						<h2>Manutenção</h2>			
						
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