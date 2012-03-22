<?php 
namespace Core;
class Configs
{
	const
		__GLOBAL__ = "default"
	;
	
	private static $m_configs;
	private static $e_Configs;
		
	static function eConf()
	{
		return self::$e_Configs;
	}
	
	static function Get($configType, $world_id = NULL)
	{		
		if($world_id && isset(self::$m_configs[$configType][$world_id]))
		{
			return self::$m_configs[$configType][$world_id];
		}
		
		return self::$m_configs[$configType][self::__GLOBAL__];
	}
	
	static function Set($configType, $value, $default)
	{
		$value = ($value) ? $value : $default;
		
		if(!is_array($value))
			self::$m_configs[$configType][self::__GLOBAL__] = $value;
		else
			self::$m_configs[$configType] = $value;
	}
	
	static function IsConfigType($configType)
	{
		return defined("self::{$configType}");
	}
	
	static function Init()
	{
		self::$e_Configs = new \e_Configs();
		
		if(file_exists(Consts::CONFIG_FILE))
		{
			include_once(Consts::CONFIG_FILE);
		}
		
		$manutention_body = "<p>Estamos fora do ar devido uma manutenção. Estaremos de volta em breve.</p><p>Contamos com a sua compreensão.</p><p>Att. Equipe UltraxSoft.</p>";
		
		for($i = 0; $i != self::$e_Configs->last(); $i++)
		{
			switch($i)
			{
				case self::$e_Configs->ENABLE_MANUTENTION: self::Set($i, $__configs[$i], false); break;
				case self::$e_Configs->MENUTENTION_TEST_SERVER: self::Set($i, $__configs[$i], false); break;
				case self::$e_Configs->MENUTENTION_TITLE: self::Set($i, $__configs[$i], "Estamos em manutenção"); break;
				case self::$e_Configs->MANUTENTION_BODY: self::Set($i, $__configs[$i], $manutention_body); break;
				
				case self::$e_Configs->WEBSITE_URL: self::Set($i, $__configs[$i], "http://darghos.com.br"); break;
				case self::$e_Configs->WEBSITE_NAME: self::Set($i, $__configs[$i], "Darghos Server"); break;
				case self::$e_Configs->WEBSITE_TEAM: self::Set($i, $__configs[$i], "UltraxSoft Team"); break;
				case self::$e_Configs->LANGUAGE: self::Set($i, $__configs[$i], Consts::LANGUAGE_PTBR); break;
				
				case self::$e_Configs->SQL_HOST: self::Set($i, $__configs[$i], "72.8.150.74"); break;
				case self::$e_Configs->SQL_USER: self::Set($i, $__configs[$i], "darghos"); break;
				case self::$e_Configs->SQL_PASSWORD: self::Set($i, $__configs[$i], "zlabia7r"); break;
				case self::$e_Configs->SQL_DATABASE: self::Set($i, $__configs[$i], "darghos"); break;
				case self::$e_Configs->SQL_WEBSITE_TABLES_PREFIX: self::Set($i, $__configs[$i], "wb_"); break;
				
				case self::$e_Configs->STATUS_HOST: self::Set($i, $__configs[$i], "darghos.com.br"); break;
				case self::$e_Configs->STATUS_PORT: self::Set($i, $__configs[$i], "7171"); break;
				case self::$e_Configs->STATUS_SHOW_PING: self::Set($i, $__configs[$i], false); break;
				case self::$e_Configs->STATUS_IGNORE_AFK: self::Set($i, $__configs[$i], false); break;
				case self::$e_Configs->STATUS_SHOW_TEST_SERVER: self::Set($i, $__configs[$i], false); break;
				
				case self::$e_Configs->WEBSITE_FOLDER_FILES: self::Set($i, $__configs[$i], "files/"); break;
				case self::$e_Configs->WEBSITE_FOLDER_GUILDS: self::Set($i, $__configs[$i], self::Get(self::$e_Configs->WEBSITE_FOLDER_FILES) . "guildImages/"); break;
				
				case self::$e_Configs->USE_DISTRO: self::Set($i, $__configs[$i], Consts::SERVER_DISTRO_TFS); break;
				case self::$e_Configs->PATCH_SERVER: self::Set($i, $__configs[$i], "/home/darghos/"); break;
				case self::$e_Configs->FOLDER_DATA: self::Set($i, $__configs[$i], "data/"); break;
				case self::$e_Configs->FILE_HOUSES: self::Set($i, $__configs[$i], "world/-house.xml"); break;
				case self::$e_Configs->FILE_MONSTERS: self::Set($i, $__configs[$i], "monster/monsters.xml"); break;
				case self::$e_Configs->USE_ENCRYPT: self::Set($i, $__configs[$i], Consts::ENCRYPT_TYPE_MD5); break;
				
				case self::$e_Configs->ENABLE_SEND_EMAILS: self::Set($i, $__configs[$i], true); break;
				case self::$e_Configs->SMTP_HOST: self::Set($i, $__configs[$i], "smtp.sendgrid.net"); break;
				case self::$e_Configs->SMTP_PORT: self::Set($i, $__configs[$i], 25); break;
				case self::$e_Configs->SMTP_USER: self::Set($i, $__configs[$i], "webadmin@darghos.com.br"); break;
				case self::$e_Configs->SMTP_PASSWORD: self::Set($i, $__configs[$i], "***REMOVED***"); break;
				
				case self::$e_Configs->CHANGEEMAIL_WAIT_DAYS: self::Set($i, $__configs[$i], 15); break;
				case self::$e_Configs->CHARACTER_DELETION_WAIT_DAYS: self::Set($i, $__configs[$i], 30); break;
				case self::$e_Configs->CHARACTER_SHOW_DEATHS_DAYS: self::Set($i, $__configs[$i], 30); break;
				case self::$e_Configs->HIGHSCORE_ACTIVE_CHARACTER_DAYS: self::Set($i, $__configs[$i], 2); break;
				
				case self::$e_Configs->NEWS_PER_PAGE: self::Set($i, $__configs[$i], 6); break;
				case self::$e_Configs->FAST_NEWS_PER_PAGE: self::Set($i, $__configs[$i], 5); break;
				case self::$e_Configs->ENABLE_PLAYERS_COMMENT_NEWS: self::Set($i, $__configs[$i], true); break;
				
				case self::$e_Configs->DISABLE_ALL_PREMDAYS_FEATURES: self::Set($i, $__configs[$i], false); break;
				case self::$e_Configs->ENABLE_ITEM_SHOP: self::Set($i, $__configs[$i], true); break;
				case self::$e_Configs->ENABLE_STAMINA_REFILER: self::Set($i, $__configs[$i], false); break;
				case self::$e_Configs->ENABLE_REMOVE_SKULLS: self::Set($i, $__configs[$i], true); break;
				case self::$e_Configs->PREMCOST_CHANGENAME: self::Set($i, $__configs[$i], 15); break;
				case self::$e_Configs->PREMCOST_CHANGESEX: self::Set($i, $__configs[$i], 10); break;
				case self::$e_Configs->PREMCOST_REMOVE_RED_SKULL: self::Set($i, $__configs[$i], 4); break;
				case self::$e_Configs->PREMCOST_REMOVE_BLACK_SKULL: self::Set($i, $__configs[$i], 10); break;
				
				case self::$e_Configs->ENABLE_GUILD_MANAGEMENT: self::Set($i, $__configs[$i], true); break;
				case self::$e_Configs->ENABLE_GUILD_WARS: self::Set($i, $__configs[$i], true); break;
				case self::$e_Configs->ENABLE_GUILD_IN_FORMATION: self::Set($i, $__configs[$i], true); break;
				case self::$e_Configs->ENABLE_GUILD_POINTS: self::Set($i, $__configs[$i], false); break;
				case self::$e_Configs->GUILDS_FORMATION_WAIT_DAYS: self::Set($i, $__configs[$i], 5); break;
				case self::$e_Configs->GUILDS_VICES_TO_FORMATION: self::Set($i, $__configs[$i], 4); break;
				
				case self::$e_Configs->ENABLE_REBORN: self::Set($i, $__configs[$i], false); break;
				case self::$e_Configs->FIRST_REBORN_LEVEL: self::Set($i, $__configs[$i], 200); break;
				
				case self::$e_Configs->SHOW_LAST_DEATHS_LIMIT: self::Set($i, $__configs[$i], 100); break;					
				case self::$e_Configs->INSTANT_DELETION_MAX_LEVEL: self::Set($i, $__configs[$i], 50); break;					
				case self::$e_Configs->ENABLE_PVP_SWITCH: self::Set($i, $__configs[$i], true); break;					
				case self::$e_Configs->AJAX_SEARCH_PLAYERS_COUNT: self::Set($i, $__configs[$i], 5); break;					
				case self::$e_Configs->ENABLE_BATTLEGROUND_FEATURES: self::Set($i, $__configs[$i], true); break;					
			}
		}
	}
}
?>