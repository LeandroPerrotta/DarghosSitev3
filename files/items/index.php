<?php

	//ini_set("display_errors", "true");
	error_reporting(E_ALL | E_STRICT); 

	// Set the item ID to re-create.
	$myid = ( isset( $_GET['id'] ) ? $_GET['id'] : 0 );
	$mycount = ( isset( $_GET['count'] ) ? $_GET['count'] : 1 );

	if ( !function_exists( 'stackId' ) )
	{
		function stackId( $count )
		{
			if ( $count >= 50 )
				$stack = 8;
			elseif ( $count >= 25 )
				$stack = 7;
			elseif ( $count >= 10 )
				$stack = 6;
			elseif ( $count >= 5 )
				$stack = 5;
			elseif ( $count >= 4 )
				$stack = 4;
			elseif ( $count >= 3 )
				$stack = 3;
			elseif ( $count >= 2 )
				$stack = 2;
			else
				$stack = 1;

			return $stack;
		}
	}
	
	
	if ( $myid >= 100 )
	{		
		$files = array(
			'otb' => '/home/leandro/darghos/data/items/items.otb',
			'spr' => '/home/leandro/www/files/Tibia.spr',
			'dat' => '/home/leandro/www/files/Tibia.dat'
		);
		
		if($mycount > 1)
			$newImagePath = "{$myid}/".stackId($mycount).".gif";
		else
			$newImagePath = "{$myid}.gif";
			
		$useCache = true;
		$debug = false;
		
		if(file_exists($newImagePath) && $useCache)
		{
			$spr = imagecreatefromgif($newImagePath);

			header('Content-type: image/gif');

			imagegif($spr);
			imagedestroy($spr);
			return;
		}
		
		
		
		if ( !defined( 'HEX_PREFIX' ) )
			define( 'HEX_PREFIX', '0x' );
		
		$nostand = false;

		/* READ OTB */
		$otb = fopen( $files['otb'], 'rb' ) or die("Can not load otb.");
		while( false !== ( $char = fgetc( $otb ) ) )
		{
			
			$byte = HEX_PREFIX.bin2hex( $char );
			if ( $byte == 0xFE )
				$init = true;
			elseif ( $byte == 0x10 and $init )
			{
				extract( unpack( 'Ssskiped/Ssid', fread( $otb, 4 ) ) );

				if ( $myid == $sid )
				{
					if ( HEX_PREFIX.bin2hex( fread( $otb, 1 ) ) == 0x11 )
					{		
						fseek( $otb, 2, SEEK_CUR );
						$optByte = bin2hex( fread( $otb, 1 ));

						if(HEX_PREFIX.$optByte == 0xFD)
						{
							extract( unpack( 'Smyid', fread( $otb, 2 ) ) );
						}
						else
						{
							$secondByte = bin2hex( fread( $otb, 1 ));
							
							$value = $secondByte . $optByte;
							//echo $value;
							$myid = hexdec($value);
						}

						
						break;
					}
				}
				$init = false;
			}
		}

		if($debug) echo("Client id: {$myid}, sid: {$sid}, sskiped: {$sskiped}, cskiped: {$cskiped}");

		fclose( $otb );
		/* CLOSE OTB */

		/* READ DAT */
		$dat = fopen( $files['dat'], 'rb' ) or die("Can not load dat.");
		$max = array_sum( unpack( 'x4/S*', fread( $dat, 12 ) ) );

		if($debug) echo "<br> Max dat items: {$max}";

		if ( $myid > $max )
		{
			return false; #trigger_error( sprintf( 'Out of range', ftell( $dat ) ), E_USER_ERROR );
		}

		for( $i = 100; $i <= $myid; $i++ )
		{

			if($debug) echo "<br>{$i}: <br>";

			do
			{
				$byte = HEX_PREFIX.bin2hex(fgetc($dat));

				if($debug) echo "{$byte} ";

				$offset = 0;
				switch( $byte )
				{
					case 0x00:
					case 0x08:
					case 0x09:	
					case 0x19:
					case 0x1C:
					case 0x1D:
					
						if($debug) echo "OFFSET(2) ";
						$offset = 2;
						break;

					case 0x15:
					case 0x18:
					

						if($debug) echo "OFFSET(4) ";
						$offset = 4;
						break;

					case 0x16:
					case 0x01:
					case 0x02:
					case 0x03:
					case 0x04:
					case 0x05:
					case 0x06:
					case 0x07:
					case 0x0A:
					case 0x0B:
					case 0x0C:
					case 0x0D:
					case 0x0E:
					case 0x0F:
					case 0x10:
					case 0x11:
					case 0x12:
					case 0x13:
					case 0x14:
					case 0x17:
					case 0x1A:
					case 0x1B:	
					case 0x1F:
					case 0x20:
					case 0x1E:
						break;

					case 0xFF:
						//die ("PARO {$i}");
						break;

					default:
						echo "AHAM", die($prev . " - " . $byte);
						#return false; #trigger_error( sprintf( 'Unknown .DAT byte %s (previous byte: %s; address %x)', $byte, $prev, ftell( $dat ), E_USER_ERROR ) );
						break;
				}

				$prev = $byte;
				fseek( $dat, $offset, SEEK_CUR );
			} while($byte != 0xFF);

			extract( unpack( 'Cwidth/Cheight', fread( $dat, 2 ) ) );

			if ( $width > 1 or $height > 1 )
			{
				fseek( $dat, 1, SEEK_CUR );
				$nostand = true;
			}

			$sprites_c = array_product( unpack( 'C*', fread( $dat, 5 ) ) ) * $width * $height;
			$sprites = unpack( 'S*', fread( $dat, 2 * $sprites_c ) );
		}

		fclose( $dat );
		/* CLOSE DAT */


		if($debug) echo("\n Width: {$width}, Height: {$height}, Count: {$sprites_c}, Sprites: " . var_dump($sprites));


		/* READ SPR */
		$spr = fopen( $files['spr'], 'rb' ) or die("Can not load spr.");

		/*
		if ( $nostand )
		{
			for( $i = 0; $i < sizeof( $sprites ) / 4; $i++ )
			{
				$sprites = array_merge( (array) $sprites, array_reverse( array_slice( $sprites, $i * 4, 4 ) ) );
			}
		}
		else
		{
			$sprites = (array) $sprites[array_rand( $sprites ) ];
		}
		*/

		if ( array_key_exists( stackId( $mycount ), $sprites ) )
		{
			$sprites = (array) $sprites[stackId( $mycount )];
		}
		else
		{
			$sprites = (array) $sprites[array_rand( $sprites ) ];
		}

		fseek( $spr, 6 );

		$sprite = imagecreatetruecolor( 32 * $width, 32 * $height );
		imagecolortransparent( $sprite, imagecolorallocate( $sprite, 0, 0, 0 ) );

		foreach( $sprites as $key => $value )
		{
			fseek( $spr, 6 + ( $value - 1 ) * 4 );
			extract( unpack( 'Laddress', fread( $spr, 4 ) ) );

			fseek( $spr, $address + 3 );
			extract( unpack( 'Ssize', fread( $spr, 2 ) ) );

			list( $num, $bit ) = array( 0, 0 );

			while( $bit < $size )
			{
				$pixels = unpack( 'Strans/Scolored', fread( $spr, 4 ) );
				$num += $pixels['trans'];
				for( $i = 0; $i < $pixels['colored']; $i++ )
				{
					extract( unpack( 'Cred/Cgreen/Cblue', fread( $spr, 3 ) ) );

					$red = ( $red == 0 ? ( $green == 0 ? ( $blue == 0 ? 1 : $red ) : $red ) : $red );

					imagesetpixel( $sprite, 
						$num % 32 + ( $key % 2 == 1 ? 32 : 0 ), 
						$num / 32 + ( $key % 4 != 1 and $key % 4 != 0 ? 32 : 0 ), 
						imagecolorallocate( $sprite, $red, $green, $blue ) );

					$num++;
				}

				$bit += 4 + 3 * $pixels['colored'];
			}
		}

		/*if ( $mycount >= 2 )
		{
			if ( $mycount > 100 )
				$mycount = 100;

			$font = 3;
			$length = imagefontwidth( $font ) * strlen( $mycount );

			$pos = array(
				'x' => ( 32 * $width ) - ( $length + 1 ),
				'y' => ( 32 * $height ) - 13
			);
			imagestring( $sprite, $font, $pos['x'] - 1, $pos['y'] - 1, $mycount, imagecolorallocate( $sprite, 1, 1, 1 ) );
			imagestring( $sprite, $font, $pos['x'], $pos['y'] - 1, $mycount, imagecolorallocate( $sprite, 1, 1, 1 ) );
			imagestring( $sprite, $font, $pos['x'] - 1, $pos['y'], $mycount, imagecolorallocate( $sprite, 1, 1, 1 ) );

			imagestring( $sprite, $font, $pos['x'], $pos['y'] + 1, $mycount, imagecolorallocate( $sprite, 1, 1, 1 ) );
			imagestring( $sprite, $font, $pos['x'] + 1, $pos['y'], $mycount, imagecolorallocate( $sprite, 1, 1, 1 ) );
			imagestring( $sprite, $font, $pos['x'] + 1, $pos['y'] + 1, $mycount, imagecolorallocate( $sprite, 1, 1, 1 ) );

			imagestring( $sprite, $font, $pos['x'], $pos['y'], $mycount, imagecolorallocate( $sprite, 219, 219, 219 ) );
		}*/

		fclose( $spr );
		/* CLOSE SPR */

		if ( $mycount > 1 )
		{
			$folder = explode( '/', $newImagePath );
			unset( $folder[count( $folder )-1] );
			$folder = implode( '/', $folder );
			if ( !file_exists( $folder ) )
			{
				mkdir( $folder );
			}
		}
		imagegif( $sprite, $newImagePath );

		header('Content-type: image/gif');

		imagegif($sprite);
		imagedestroy($sprite);
	}