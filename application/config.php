<?
	/**
	* @package   cmangos-website
	* @version   1.0
	* @author    Victor Corbuleanu
	* @copyright 2023 Victor Corbuleanu
	* @link      https://github.com/vcorbuleanu/cmangos-website/
	* @license   https://github.com/vcorbuleanu/cmangos-website/blob/master/LICENCE.txt Attribution-NonCommercial-NoDerivatives 4.0 International  
	**/

	define('WEBSITE_TITLE', 'cMaNGOS');
	define('WEBSITE_DOMAIN', '127.0.0.1');
	define('WEBSITE_BASE_URL', WEBSITE_HTTPS ? 'https://' : 'http://' . WEBSITE_DOMAIN);
	define('WEBSITE_TIMEZONE', 'Europe/Bucharest');
	define('DEFAULT_CONTROLLER', 'news');
	define('DEFAULT_METHOD', 'latest');
	// Whether to display the site disclaimer or not. Disable at your own discretion/peril.
	define('WEBSITE_DISCLAIMER', true);
	define('NEWS_ARCHIVE_AFTER_DAYS', 30);
	define('ACCOUNT_ACCESS_LOGS_DAYS', 7);
	define('REQUIRE_ACCOUNT_ACTIVATION', false);
	define('DEFAULT_AVATAR', 'default');
	define('DEFAULT_SIGNATURE', 'default');
	define('AVATAR_LIST', [
		'default', 'ashbringer', 'dancing_murloc', 'drake', 'druid', 'essence', 'furbolg',
		'gnome', 'halloween', 'gryphon', 'lava_elemental', 'lich', 'monkey', 'moonkin',
		'murloc', 'nerubian', 'panda', 'phoenix', 'pirate', 'rock_elemental', 'shadow',
		'spirit_healer', 'squirrel', 'toxic_slime', 'treant', 'turtle', 'water_elemental', 'welp'
	]);
	define('SIGNATURE_LIST', [
		'default', 'caverns', 'icecrown', 'darnassus', 'nagrand', 'netherstorm', 'temple'
	]);
	
	// This setting controlls which uptimes and how long before they are removed from the database.
	define('MANGOSD_UPTIME_CLEAR', 60*60);
	 // This setting controlls which uptimes and how long before they are removed from the database.
	define('MANGOSD_UPTIME_HISTORY_CLEAR', 60*60*24*7);
	define('MANGOSD_SHOW_UPTIME_HISTORY', true);

	// Database credentials
    	define('DB_HOST', '127.0.0.1');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', 'root');
	define('DB_WEBSITE', 'website');
	// This is your main realmd. All your realms MUST use the same realmd database. Read the CMaNGOS documentation on how to configure your realms to use a single account.
	define('DB_REALMD', 'realmd');
	// This maps to the ids of the realms specified in the DB_REALMD.realmlist table.
	define('DB_MANGOSD', array(
		1 => array(
			// This is to avoid a DNS lookup (if the server is hosted on the same machine) every time we check for realm status (even if it's cached).
			'address' => '127.0.0.1',
			// World and character databases of the realm
			'world' => 'mangosd_classic',
			'character' => 'characters_classic'
		),
		2 => array(
			// This is to avoid a DNS lookup (if the server is hosted on the same machine) every time we check for realm status (even if it's cached).
			'address' => '127.0.0.1',
			// World and character databases of the realm
			'world' => 'mangosd_tbc',
			'character' => 'characters_tbc'
		),
		3 => array(
			// This is to avoid a DNS lookup (if the server is hosted on the same machine) every time we check for realm status (even if it's cached).
			'address' => '127.0.0.1',
			// World and character databases of the realm
			'world' => 'mangosd_wotlk',
			'character' => 'characters_wotlk'
		),
	));
	
	define('COOKIE_PATH', '/');
	define('COOKIE_NAME', 'CREDENTIALS');
	define('COOKIE_TIMEOUT', 60*60*24*7);
	define('COOKIE_ENCRYPTION', true);
	define('COOKIE_ENCRYPTION_KEY', ''); // CONFIGURE THIS BEFORE YOU PUBLISH YOUR WEBSITE. CHANGING IT AFTER LOGS EVERY PLAYER OUT OF THEIR ACCOUNT.
	define('COOKIE_SIGNING_KEY', ''); // CONFIGURE THIS BEFORE YOU PUBLISH YOUR WEBSITE. CHANGING IT AFTER LOGS EVERY PLAYER OUT OF THEIR ACCOUNT.

	// DO NOT TOUCH THIS IF YOU HAVE NO IDEA HOW MFA-TOTP WORKS!
	// ONCE YOU LAUNCH YOUR WEBSITE, DO NOT (UNDER ANY CIRCUMSTANCES) CHANGE THESE VALUES. IF YOU DO, ALL ACCOUNTS WITH MFA ENABLED WILL NO LONGER BE ABLE TO AUTHENTICATE!
	define('OTP_SECRET_LENGTH', '16');
	define('OTP_TOKEN_LENGTH', '6');
	define('OTP_RECOVERY_TOKENS', '12');
	define('OTP_TOKEN_TIMEWINDOW', '0');
	define('OTP_TOKEN_TIMESTEP', '30');
	define('OTP_TOKEN_CEILING', '1000000');

	// THIS IS THE CONFIGURATION FOR THE 3RD PARTY SMTP RELAY (USUALLY ISSUED BY YOUR DOMAIN NAME REGISTRAR)
	define('SMTP_HOST', '');
	define('SMTP_PORT', 587);
	define('SMTP_USERNAME', '');
	define('SMTP_PASSWORD', '');
?>