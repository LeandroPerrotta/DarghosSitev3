<?
	/* Item Sprite Reader
		- Para usar faça algo parecido: <img src="http://site.do.seu.ot/tibiaitem/2400.gif" />, onde 2400 é o item id que irá variar
		- Coloque abaixo os caminhos dos arquivos necessários
		- Criado por Magus [www.otserv.com.br]
	*/
	class SpriteReader
	{
		private $file;
		
		function SpriteReader($filepatch)
		{
			$this->file = fopen($filepatch, 'rb') or exit("Cannot read file: {$filepatch}");
		}
	}	


	function DrawImage($id, $k, $fp, $spr, $multipler = 1)
	{		
		//if ($id < 100) return;
		
		fseek($fp, 6 + ($id - 1) * 4);

		extract(unpack('Laddress', fread($fp, 4)));
		fseek($fp, $address + 3);

		extract(unpack('Ssize', fread($fp, 2)));

		$num = 0;
		$bit = 0;
		
		while ($bit < $size)
		{
			$pixels = unpack('Strans/Scolored', fread($fp, 4));
			$num += $pixels['trans'];
			
			for ($i = 0; $i < $pixels['colored']; $i++)
			{
				extract(unpack('Cred/Cgreen/Cblue', fread($fp, 3)));
				
				$red = $red == 0 ? ($green == 0 ? ($blue == 0 ? 1 : $red) : $red) : $red;
				
				if($multipler > 1)
				{
					//echo "<br>multipler</br>";
					imagesetpixel($spr, $num % 32 + ($k % 2 == 1 ? 32 : 0), $num / 32 + ($k % 4 != 1 && $k % 4 != 0 ? 32 : 0), imagecolorallocate($spr, $red, $green, $blue));
				}
				else
				{
					//echo "<br>no multipler</br>";
					imagesetpixel($spr, $num % 32, $num / 32, imagecolorallocate($spr, $red, $green, $blue));
				}
				$num++;
			}
			
			$bit += 4 + 3 * $pixels['colored'];
		}		
	}

	$spr_path = '/home/leandro/www/files/Tibia.spr';
	$dat_path = '/home/leandro/www/files/Tibia.dat';
	$otb_path = '/home/leandro/darghos/data/items/items.otb';
	$caching = false;
	
	define('HEX_PREFIX', '0x');
	
	$isOutfit = false;
	
	if($_GET['type'])
	{
		$isOutfit = true;
		settype(($myId = $_GET['type']), 'integer');
		$img_path = "{$myId}.gif"; //only if caching
	}
	else	
	{
		settype(($myId = $_GET['id']), 'integer');
		$img_path = "id_{$myId}.gif"; //only if caching
	}
	
	if ($caching && file_exists($img_path))
	{
		$spr = imagecreatefromgif($img_path);
	}
	else
	{	
		if ($caching && !file_exists('cache')) mkdir('cache');
		
		if (!$isOutfit && $myId < 100)
			trigger_error('Item id must be a number above 100', E_USER_ERROR);
		
		if(!$isOutfit)
		{	
			$fp = fopen($otb_path, 'rb') or exit;
			
			while (false !== ($char = fgetc($fp)))
			{				
				$optByte = HEX_PREFIX.bin2hex($char);
				if ($optByte == 0xFE) 
					$init = true;
				elseif ($optByte == 0x10 && $init)
				{
					extract(unpack('x2/Ssid', fread($fp, 4)));
					if ($myId == $sid)
					{
						
						if (HEX_PREFIX.bin2hex(fread($fp, 1)) == 0x11)
							extract(unpack('x2/SmyId', fread($fp, 4)));
						break;
					}
					$init = false;
				}
			}
			
			fclose($fp);
		}
		
		$fp = fopen($dat_path, 'rb') or exit;
		
		$header = unpack('x4/S*', fread($fp, 12));
		$maxId = array_sum($header);
		
		if($isOutfit)
		{
			$myId = $header[1] + $myId;			
		}

		if ($myId > $maxId)
			trigger_error(sprintf('Out of range', ftell($fp)), E_USER_ERROR);
		
		$lastoffSet = 0;	
			
		for ($id = 100 /* Void */; $id <= $myId; $id++)
		{			
			
			while (($optByte = HEX_PREFIX.bin2hex(fgetc($fp))) != 0xFF)
			{
				$offset = 0;
				switch ($optByte)
				{
					case 0x00:case 0x09:
					case 0x0A:case 0x1A:
					case 0x1D:case 0x1E:
						$offset = 2;
					break;
					
					case 0x16:case 0x19:
						$offset = 4;
					break;
					
					case 0x01:case 0x02:case 0x03:case 0x04:case 0x05:
					case 0x06:case 0x07:case 0x08:case 0x0B:case 0x0C:
					case 0x0D:case 0x0E:case 0x0F:case 0x10:case 0x11:
					case 0x12:case 0x13:case 0x14:case 0x15:case 0x17:
					case 0x18:case 0x1B:case 0x1C:case 0x1F:case 0x20:
					break;
					
					default:
						trigger_error(sprintf('Unknown dat opt byte: %s (previous opt byte: %s; address: %x)', $optByte, $prevByte, ftell($fp)), E_USER_ERROR);
					break;
				}
				$prevByte = $optByte;
				fseek($fp, $offset, SEEK_CUR);
				
				$lastoffSet = $offset;
			}
			
			extract(unpack('Cwidth/Cheight', fread($fp, 2)));
			
			if ($width > 1 || $height > 1)
			{
				fseek($fp, 1, SEEK_CUR);
				$nostand = true;
			}
			
			$spr_count = array_product(unpack('C*', fread($fp, 5))) * $width * $height;
			$sprites = unpack('S*', fread($fp, 2 * $spr_count));
			
			//die("SprC:" . print_r($spr_count).", Sprites:" . print_r($sprites));
		}
		
		echo "Blend: ".$blend;
		
		fclose($fp);
			
		$fp = fopen($spr_path, 'rb');
		
		if ($nostand)
		{		
			echo "nostand";	
			for ($i = 0; $i < sizeof($sprites)/4; $i++)
				$spriteIds = array_merge((array)$spriteIds, array_reverse(array_slice($sprites, $i*4, 4)));
		}
		else
			$spriteIds = (array) $sprites[array_rand($sprites)];
		
		fseek($fp, 6);

		$printSeparated = true;
		
		if(!$printSeparated)
		{
			$animation_patch = "{$_GET["type"]}";
			if(!$isOutfit)
				$animation_patch = "id_{$_GET["id"]}";
				
			$cmd = 'gifsicle --loop -O1 --disposal=background --multifile --delay 50 - > '.$animation_patch.'.gif';
			$desc = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("file", "errors.txt", "a"));
			$proc = proc_open($cmd, $desc, $pipes);		
			
			if(!is_resource($proc))
			{
				die('Could not start gifsicle');
			}		
		}		
		
		$spr_c = count($spriteIds);
		
		if($isOutfit)
		{			
			if($spr_c == 12)
			{
				$front = array(5, 9);
				//$front = false;
			}
			elseif($spr_c == 16)
			{
				$front = array(1);
			}
			elseif($spr_c == 48)
			{
				$front = array(6, 10);
			}	
			elseif($spr_c == 24)
			{
				if($_GET["type"] == 19) //slime apenas?	
					$front = array(0, 4, 8, 12, 16, 20);
				else
					$front = array(15, 23);
			}
			elseif($spr_c == 32)
			{
				$front = array(2, 6);
			}
			//player outfit
			elseif($spr_c == 288)
			{
				#$front = array(28, 52);
			}	
			elseif($spr_c == 96)
			{
				$front = array(12, 20);
			}
			elseif($spr_c == 64)
			{
				$front = array(2, 6, 10);
			}	
			elseif($spr_c == 80)
			{
				$front = array(6, 14);
			}	
			elseif($spr_c == 8)
			{
				$front = array(1, 5);
			}	
			else
			{			
				$fron = false;
			}
					
			$gifPos = 0;
			$spritesDraw = 1;		
		}
		
		$multipler = ($width != 1 && $height != 1) ? 2 : 1;
		print_r($spriteIds);
		echo "<br>Count: {$spr_c}, Multipler: {$multipler}";
	
		echo "<br>Width: {$width} Height: {$height}";
		
		/*$hackoutfits = array(92);
		if(in_array($_GET["id"], $hackoutfits))
		{
			$multipler = 2;
		}*/
		$spr = imagecreatetruecolor(32 * $width, 32 * $height);
		imagecolortransparent($spr, imagecolorallocate($spr, 0, 0, 0));	
		
		foreach ($spriteIds as $k => $id)
		{				
			echo "<br>Id:".$id.", Sprite: {$spritesDraw}";
			
			if(!$isOutfit)
			{
				//$spr = imagecreatetruecolor(32 * $width, 32 * $height);
				//imagecolortransparent($spr, imagecolorallocate($spr, 0, 0, 0));				
				
				drawImage($id, $k, $fp, $spr, 2);
				
				/*if(!$printSeparated)
				{
					ob_start();
					imagegif($spr);
					$contents = ob_get_contents();		
					fwrite($pipes[0], $contents);		
					ob_end_clean();	
				}
				else
					imagegif($spr, "id_{$_GET["id"]}_{$k}.gif");
					
				imagedestroy($spr);*/
					
				continue;
			}				
				
			$label = $k;
							
			if($multipler == 1)
			{
				$spr = imagecreatetruecolor(32 * $width, 32 * $height);
				imagecolortransparent($spr, imagecolorallocate($spr, 0, 0, 0));						
			}
			else
			{
				if($spritesDraw == 1)
				{
					$spr = imagecreatetruecolor(32 * $width, 32 * $height);
					imagecolortransparent($spr, imagecolorallocate($spr, 0, 0, 0));									
				}
			}
			
			drawImage($id, $k, $fp, $spr, $multipler);
			
			if($multipler == 1)
			{
				if(!$printSeparated)
				{
					if(!$front || in_array($label, $front)) 
					{
						ob_start();
						imagegif($spr);
						$contents = ob_get_contents();		
						fwrite($pipes[0], $contents);		
						ob_end_clean();
					}		
				}
				else
				{
					if(!$front || in_array($label, $front)) 
						imagegif($spr, "{$_GET["type"]}_{$label}.gif");
				}
					
				imagedestroy($spr);	
			}
			else
			{
				if($spritesDraw == 4)
				{
					if(!$printSeparated)
					{
						if(!$front || in_array($gifPos, $front))
						{						
							ob_start();
							imagegif($spr);
							$contents = ob_get_contents();		
							fwrite($pipes[0], $contents);		
							ob_end_clean();		
						}		
					}
					else	
					{					
						if(!$front || in_array($gifPos, $front))
						{
							$label = $gifPos;
							imagegif($spr, "{$_GET["type"]}_{$label}.gif");
							imagedestroy($spr);	
						}
					}
					$spritesDraw = 0;	
					$gifPos++;
				}
			}

			$spritesDraw++;
		}
		
		if(!$printSeparated)
		{
			fclose($pipes[0]);
			fclose($pipes[1]);
			fclose($pipes[2]);		
			proc_close($proc);		
		}
		
		if(!$isOutfit)
		{
			imagegif($spr, "id_{$_GET["id"]}.gif");
			imagedestroy($spr);
		}
		
		fclose($fp);

		//if ($caching && !file_exists($img_path)) imagegif($spr, $img_path);
	}
	
	$spr = imagecreatefromgif($img_path);
	header('Content-type: image/gif');

	imagegif($spr);
	imagedestroy($spr);
?>