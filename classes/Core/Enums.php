<?php 
class e_Configs extends \Core\Enumerators
{
	public
		/*
		 * Manutention related
		 */
		$ENABLE_MANUTENTION
		,$MENUTENTION_TEST_SERVER
		,$MENUTENTION_TITLE
		,$MANUTENTION_BODY
		
		/*
		 * Website
		 */
		,$WEBSITE_URL
		,$WEBSITE_NAME
		,$WEBSITE_TEAM
		
		,$LANGUAGE		
		
		/*
		 * Database
		 */
	
		,$SQL_HOST
		,$SQL_USER
		,$SQL_PASSWORD
		,$SQL_DATABASE
		,$SQL_WEBSITE_TABLES_PREFIX
		
		/*
		 * Status
		 */
		,$STATUS_HOST
		,$STATUS_PORT
		,$STATUS_SHOW_PING
		,$STATUS_IGNORE_AFK
		,$STATUS_SHOW_TEST_SERVER
		
		/*
		 * Website folding
		 */
		,$WEBSITE_FOLDER_FILES
		,$WEBSITE_FOLDER_GUILDS
		,$WEBSITE_FOLDER_TEMP
		
		/*
		 * Server related
		 */
		,$USE_DISTRO
		,$PATCH_SERVER
		,$FOLDER_DATA
		,$FILE_HOUSES
		,$FILE_MONSTERS
		,$USE_ENCRYPT
		
		/*
		 * Email related
		 */
		,$ENABLE_SEND_EMAILS
		,$SMTP_HOST
		,$SMTP_PORT
		,$SMTP_USER
		,$SMTP_PASSWORD	
		
		/*
		 * Days related
		 */
		,$CHANGEEMAIL_WAIT_DAYS
		,$CHARACTER_DELETION_WAIT_DAYS
		,$CHARACTER_SHOW_DEATHS_DAYS
		,$HIGHSCORE_ACTIVE_CHARACTER_DAYS
		
		/*
		 * News & Fast News
		 */
		,$NEWS_PER_PAGE
		,$FAST_NEWS_PER_PAGE
		,$ENABLE_PLAYERS_COMMENT_NEWS
		
		/*
		 * Premdays related
		 */
		,$DISABLE_ALL_PREMDAYS_FEATURES
		,$ENABLE_ITEM_SHOP
		,$ENABLE_STAMINA_REFILER
		,$ENABLE_REMOVE_SKULLS
		,$PREMCOST_CHANGENAME
		,$PREMCOST_CHANGESEX
		,$PREMCOST_REMOVE_RED_SKULL
		,$PREMCOST_REMOVE_BLACK_SKULL
		
		/*
		 * Guilds related
		 */
		,$ENABLE_GUILD_MANAGEMENT
		,$ENABLE_GUILD_WARS
		,$ENABLE_GUILD_IN_FORMATION
		,$ENABLE_GUILD_POINTS
		
		,$GUILDS_FORMATION_WAIT_DAYS
		,$GUILDS_VICES_TO_FORMATION
		
		/*
		 * Reborn related
		 */
		,$ENABLE_REBORN
		,$FIRST_REBORN_LEVEL
		
		/*
		 * Others
		 */
		,$SHOW_LAST_DEATHS_LIMIT
		,$INSTANT_DELETION_MAX_LEVEL
		,$ENABLE_PVP_SWITCH
		,$ENABLE_BATTLEGROUND_FEATURES
		,$AJAX_SEARCH_PLAYERS_COUNT
		,$ENABLE_MULTIWORLD
		,$DEFAULT_WORLD
		,$ENABLE_PLAYERS_ONLINE
	;
	
	function __construct(){
		parent::__construct();
	}	
}

class e_LangMsg extends \Core\Enumerators
{
	public
	$ERROR
	,$SUCCESS
	,$FILL_FORM
	,$PRIVACY_POLICY
	,$WRONG_EMAIL
	,$WRONG_PASSWORD
	,$ACCOUNT_NAME_WRONG_SIZE
	,$ACCOUNT_EMAIL_ALREADY_USED
	,$ACCOUNT_NAME_ALREADY_USED
	,$FAIL_SEND_EMAIL
	,$FAIL_LOGIN
	,$RECOVERY_UNKNOWN_EMAIL
	,$RECOVERY_UNKNOWN_CHARACTER
	,$RECOVERY_WRONG_KEY
	,$RECOVERY_WRONG_SECRET_KEY
	,$RECOVERY_FILL_CHARACTER_NAME
	,$RECOVERY_DISABLED
	,$OPERATION_ARE_BLOCKED
	,$OPERATION_HAS_BLOCKED
	,$CHANGEPASS_WRONG_NEWPASS_CONFIRM
	,$CHANGEPASS_SAME_PASSWORD
	,$CHANGEPASS_WRONG_NEWPASS_LENGHT
	,$CHANGEEMAIL_ALREADY_HAVE_REQUEST
	,$CHARACTER_WRONG
	,$ACCOUNT_REGISTERED
	,$RECOVERY_ACCOUNT_NAME_SEND
	,$RECOVERY_PASSWORD_SEND
	,$RECOVERY_NEWPASS_SEND
	,$RECOVERY_BOTH_SEND
	,$RECOVERY_EMAIL_CHANGED
	,$ACCOUNT_PASSWORD_CHANGED
	,$CHANGEEMAIL_SCHEDULED
	,$ACCOUNT_INFOS_SEND
	,$ACCOUNT_PASSWORD_IS
	,$CHANGEEMAIL_NOTHING
	,$CHANGEEMAIL_CANCELED
	,$CHANGEINFOS_WRONG_SIZE
	,$CHANGEINFOS_SUCCESS
	,$SECRETKEY_ALREADY_EXISTS
	,$SECRETKEY_SUCCESS
	,$SECRETKEY_WRONG_SIZE
	,$SECRETKEY_CUSTOM_SUCCESS
	,$ACCOUNT_SETNAME_SAME_ID
	,$ACCOUNT_SETNAME_SUCCESS
	,$WRONG_NAME
	,$CHARACTER_NAME_ALREADY_USED
	,$ACCOUNT_CANNOT_HAVE_MORE_CHARACTERS
	,$CHARACTER_CREATED
	,$CHARACTER_NOT_TO_DELETION
	,$CHARACTER_NOT_FROM_YOUR_ACCOUNT
	,$CHARACTER_NO_MORE_DELETED
	,$CHARACTER_MUST_BE_RED_OR_BLACK_SKULL
	,$CONTR_TERMS
	,$CONTR_ACTIVATED
	,$CONTR_ORDER_NUMBER_DUPLICATED
	,$CONTR_ORDER_CREATED
	,$ACCOUNT_HAS_NO_ORDERS
	,$REPORT
	,$NEED_LOGIN
	,$NEED_PREMIUM
	,$PAGE_NOT_FOUND
	,$SQL_INJECTION
	,$GUILD_NOT_FOUND
	,$GUILD_CHARACTER_NOT_INVITED
	,$GUILD_JOIN
	,$GUILD_JOIN_REJECT
	,$GUILD_NAME_ALREADY_USED
	,$GUILD_ONLY_ONE_VICE_PER_ACCOUNT
	,$GUILD_INVITE_CHARACTER_NOT_SAME_WORLD
	,$GUILD_NOT_SAME_WORLD
	,$CHARACTER_ALREADY_MEMBER_GUILD
	,$GUILD_CREATED
	,$CHARACTER_ALREADY_TO_DELETE
	,$CHARACTER_MUST_AWARE_INSTANT_DELETION
	,$CHARACTER_DELETED
	,$CHARACTER_DELETION_SCHEDULED
	,$CHARACTER_COMMENT_WRONG_SIZE
	,$CHARACTER_COMMENT_CHANGED
	,$CHARACTER_CHANGE_THING_CONFIRM
	,$CHARACTER_REMOVE_SKILLS_CONFIRM
	,$CHARACTER_NEED_OFFLINE
	,$CHARACTER_CHANGENAME_COST
	,$CHARACTER_NAME_CHANGED
	,$CHARACTER_CHANGESEX_COST
	,$CHARACTER_SEX_CHANGED
	,$ITEMSHOP_OLD_PURCHASE
	,$ITEMSHOP_COST
	,$ITEMSHOP_PURCHASE_SUCCESS
	,$GUILD_NEED_NO_MEMBERS_DISBAND
	,$GUILD_DISBANDED
	,$GUILD_COMMENT_SIZE
	,$GUILD_LOGO_SIZE
	,$GUILD_FILE_WRONG
	,$GUILD_LOGO_DIMENSION_WRONG
	,$GUILD_LOGO_EXTENSION_WRONG
	,$GUILD_DESC_CHANGED
	,$GUILD_INVITE_LIMIT
	,$GUILD_INVITE_ALREADY_MEMBER
	,$GUILD_INVITE_ALREADY_INVITED
	,$GUILD_INVITE_CHARACTER_NOT_FOUNDS
	,$GUILD_INVITEDS
	,$GUILD_LEAVE
	,$GUILD_IS_NOT_MEMBER
	,$GUILD_RANK_ONLY_PREMIUM
	,$GUILD_PERMISSION
	,$GUILD_TITLE_SIZE
	,$GUILD_ACCOUNT_ALREADY_IS_HIGH_RANK
	,$GUILD_MEMBER_EDITED
	,$GUILD_PASSLEADERSHIP
	,$GUILD_RANK_WRONG_ORDER
	,$GUILD_RANK_WRONG_SIZE
	,$GUILD_RANK_MIMINUM_NEEDED
	,$GUILD_RANKS_EDITED
	,$GUILD_RANK_IN_USE
	,$GUILD_CANNOT_LEAVE
	,$GUILD_WAR_NO_HAVE_OPPONENTS
	,$GUILD_NEED_TO_BE_FORMED
	,$FILL_NUMERIC_FIELDS
	,$GUILD_WAR_WRONG_FRAG_LIMIT
	,$GUILD_WAR_WRONG_END_DATE
	,$GUILD_WAR_WRONG_FEE
	,$GUILD_WAR_WRONG_COMMENT_LENGTH
	,$GUILD_WAR_DECLARED
	,$GUILD_WAR_ACCEPTED
	,$GUILD_WAR_REJECTED
	,$GUILD_WAR_NEGOTIATE_SEND
	,$GUILD_IS_ON_WAR
	,$GUILD_WAR_ALREADY
	,$GUILD_INVITE_CANCEL
	,$FORUM_ACCOUNT_NOT_HAVE_USER
	,$FORUM_ACCOUNT_NOT_HAVE_CHARACTERS
	,$FORUM_ACCOUNT_REGISTERED
	,$FORUM_POLL_ALREADY_VOTED
	,$FORUM_POLL_ONLY_FOR_PREMIUM
	,$FORUM_POLL_NEED_MIN_LEVEL
	,$FORUM_POLL_TIME_EXPIRED
	,$FORUM_POLL_VOTE_DONE
	,$FORUM_POST_TOO_LONG
	,$FORUM_POST_SENT
	,$FORUM_USER_BANNISHED
	,$STAMINA_NOT_HAVE_PREMDAYS
	,$STAMINA_VALUE_WRONG
	,$STAMINA_SUCCESSFULY
	,$REMOVE_SKULL_SUCCESSFULY
	,$DARGHOSPOINTS_NEED_ACCEPT_TERMS
	,$MONSTER_NOT_FOUND
	,$OPERATION_NEED_PREMDAYS
	,$ACCOUNT_CHANGENAME_SUCCESS
	,$GUILD_BALANCE_TOO_LOW
	,$SECRETKEY_MUST_BY_UNLIKE_REMINDER
	,$CAN_NOT_VALIDATE_EMAIL
	,$VALIDATE_EMAIL_SUCCESSFULY
	,$OPERATION_REQUIRE_VALIDATED_EMAIL
	,$ACCOUNT_ALREADY_VALIDATED_EMAIL
	,$ACCOUNT_VALIDATING_EMAIL_SEND
	;

	function __construct(){
		parent::__construct();
	}
}

class e_MenuPosition
{	
	const
		Left = 0,
		Right = 1
		;
}

class e_MenuColor
{	
	const
		Green = 0,
		Red = 1
		;
}

class e_MenuVisibilityStyle
{	
	const
		Normal = 0,
		DropDown = 1
		;
}

class e_MenuType
{	
	const
		Items = 0,
		CallFunction = 1
		;
}

class e_BanTypes
{
	const
		IpAddress = 0
		,NameLock = 1
		,Banishment = 2
		,Notation = 3
		,Deletion = 4
	;
}

class t_Worlds extends \Core\Structs
{
	const
		Ordon = 0
		,Aaragon = 1
		;
		
	static protected
		$m_typeStrings = array(
			self::Ordon => "Ordon"
			,self::Aaragon => "Aaragon"
		);
}

class t_PremdaysServices extends \Core\Structs
{
	const
		ChangeCharacterName = 1
		,ChangeCharacterSex = 2
		,RechargeCharacterStamina = 3
		,AccountRename = 4
		,ChangeCharacterPvp = 5
		,ClearChangePvpDebuff = 6
		,ClearSkullRed = 7
		,ClearSkullBlack = 8
		;
}

class t_PaymentStatus extends \Core\Structs
{
	const
		WaitingPayment = 0
		,Confirmed = 1
		,Finished = 2
		,Canceled = 3
	;	
	
	static protected
		$m_typeStrings = array(
			self::WaitingPayment => "Aguardando Pagamento"
			,self::Confirmed => "Confirmado"
			,self::Finished => "Concluido"
			,self::Canceled => "Cancelado"				
		);
}

class t_Skulls extends \Core\Structs
{
	const
		None = 0
		,Yellow = 1
		,Green = 2
		,White = 3
		,Red = 4
		,Black = 5
		;
}

class t_Skills extends \Core\Structs
{
	const
		Fist = 0
		,Club = 1
		,Sword = 2
		,Axe = 3
		,Distance = 4
		,Shielding = 5
		,Fishing = 6
	;
	
	static protected
		$m_typeStrings = array(
			self::Fist => "fist"
			,self::Club => "club"
			,self::Sword => "sword"
			,self::Axe => "axe"
			,self::Distance => "distance"
			,self::Shielding => "shielding"
			,self::Fishing => "fishing"		
		);
}

class t_TownsGlobal extends \Core\Structs
{
	const
		Venore = 1
		,Thais = 2	
		,Kazordoon = 3	
		,Carlin = 4
		,AbDendriel = 5	
		,Rookgaard = 6	
		,LibertyBay = 7	
		,PortHope = 8	
		,Ankrahmun = 9	
		,Darashia = 10	
		,Edron = 11	
		,Svarground = 12	
		,Yalahar = 13	
		,Farmine = 14	
	;
	
	static protected
	$m_typeStrings = array(
			self::Venore => "Venore",
			self::Thais => "Thais",
			self::Kazordoon => "Kazordoon",
			self::Carlin => "Carlin",
			self::AbDendriel => "Ab'Dendriel",
			self::Rookgaard => "Rookgaard",
			self::LibertyBay => "Liberty Bay",
			self::PortHope => "Port Hope",
			self::Ankrahmun => "Ankrahmun",
			self::Darashia => "Darashia",
			self::Edron => "Edron",
			self::Svarground => "Svarground",
			self::Yalahar => "Yalahar",
			self::Farmine => "Farmine"
	);	
}

class t_Towns extends \Core\Structs
{
	const
		Quendor = 1
		,Aaragon = 2
		,Rookgaard = 3
		,Thorn = 4
		,Salazart = 5
		,IslandOfPeace = 6
		,Northrend = 7
		,Kashmir = 9
		,Aracura = 10
	;
	
	static protected
		$m_typeStrings = array(
			self::Quendor => "Quendor",
			self::Aaragon => "Aaragon",
			self::Rookgaard => "Rookgaard",
			self::Thorn => "Thorn",
			self::Salazart => "Salazart",
			self::IslandOfPeace => "Island of Peace",
			self::Northrend => "Northrend",
			self::Kashmir => "Kashmir",
			self::Aracura => "Aracura"				
		);
}

class t_Group extends \Core\Structs
{
	const
		Player = 1
		,PlayerNonPvp = 2
		,Tutor = 3
		,SeniorTutor = 4
		,GameMaster = 5
		,CommunityManager = 6
		,Administrator = 7
		,PlayerNonLogout = 8
	;	
	
	static protected
		$m_typeStrings = array(
			self::Player => "Jogador",
			self::PlayerNonPvp => "Jogador",
			self::Tutor => "Tutor",
			self::SeniorTutor => "Senior Tutor",
			self::GameMaster => "Game Master",
			self::CommunityManager => "Community Manager",
			self::Administrator => "Administrador",	
			self::PlayerNonLogout => "Jogador"				
		);		
}

class t_Access
{
	const
		Player = 0
		,Tutor = 1
		,SeniorTutor = 2
		,GameMaster = 3
		,CommunityManager = 4
		,Administrator = 5
	;
}

class t_Genre extends \Core\Structs
{	
	const
		Female = 0
		,Male = 1
	;	
	
	static protected
		$m_typeStrings = array(
			self::Female => "Feminino"
			,self::Male => "Masculino"				
		);
}

class t_Vocation
{
	private $_vocation_id;
	private $_vocation_names = array(
			0 => array("name" => "none", "abrev" => "n"),
			1 => array("name" => "sorcerer", "abrev" => "s"),
			2 => array("name" => "druid", "abrev" => "d"),
			3 => array("name" => "paladin", "abrev" => "p"),
			4 => array("name" => "knight", "abrev" => "k"),
			5 => array("name" => "master sorcerer", "abrev" => "ms"),
			6 => array("name" => "elder druid", "abrev" => "ed"),
			7 => array("name" => "royal paladin", "abrev" => "rp"),
			8 => array("name" => "elite knight", "abrev" => "ek"),
			9 => array("name" => "warmaster sorcerer", "abrev" => "ws"),
			10 => array("name" => "warden druid", "abrev" => "wd"),
			11 => array("name" => "holy paladin", "abrev" => "hp"),
			12 => array("name" => "berserk knight", "abrev" => "bk"),
	);

	function t_Vocation($vocation_id = null)
	{
		if($vocation_id)
			$this->Set($vocation_id);
	}

	function Set($vocation_id)
	{
		$this->_vocation_id = $vocation_id;
	}

	function SetByName($name)
	{
		foreach($this->_vocation_names as $k => $value)
		{
			if(strtolower($name) == $value["name"])
			{
				$this->_vocation_id = $k;
				break;
			}
		}
	}

	function Get()
	{
		return $this->_vocation_id;
	}

	function GetByName()
	{
		return $this->_vocation_names[$this->_vocation_id]["name"];
	}

	function GetByAbrev()
	{
		return $this->_vocation_names[$this->_vocation_id]["abrev"];
	}
}

class t_ForumBans
{
	private $_type;
	private $_type_str = array(
			0 => "24 horas",
			1 => "7 dias",
			2 => "30 dias",
			3 => "indeterminado"
	);

	function t_ForumBans($_type = null)
	{
		if($_type)
			$this->Set($_type);
	}

	function Set($_type)
	{
		$this->_type = $_type;
	}

	function Get()
	{
		return $this->_type;
	}

	function GetByName()
	{
		return $this->_type_str[$this->_type];
	}
}

?>
