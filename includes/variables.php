<?
	define('FILEROOT', $_SERVER['DOCUMENT_ROOT']);
	define('COOKIE_ROOT', '/');

	define('SVAR', 'xU3Fh9XLo21mlHuk6H31');
	define('MODAL', (isset($_GET['modal']) && $_GET['modal'] == 1) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')?TRUE:FALSE);
	define('CUR_TIMEZONE', '-08:00');
	define('PAGINATE_PER_PAGE', 20);
	define('HERITAGE_PAD', 4);
	$loggedIn = 0;
	$fixedMenu = FALSE;

	$dispatchInfo404 = array('url' => '/404', 'pageID' => '404', 'file' => 'errors/404.php', 'title' => 'Not Found');
	
	$permissionTypes = array('read' => 'Read', 'write' => 'Write', 'editPost' => 'Edit Post', 'deletePost' => 'Delete Post', 'createThread' => 'Create Thread', 'deleteThread' => 'Delete Thread', 'addPoll' => 'Add Poll', 'addRolls' => 'Add Rolls', 'addDraws' => 'Add Draws', 'moderate' => 'Moderate');

	$faqsCategories = array('Getting Started' => 'getting-started', 'Characters' => 'characters', 'Games' => 'games', 'Tools' => 'tools');

	$charTypes = array('PC', 'NPC', 'Mob');
	
	$timezones = array('Etc/GMT+12' => '(GMT-12:00) International Date Line West', 'Pacific/Apia' => '(GMT-11:00) Midway Island, Samoa', 'Pacific/Honolulu' => '(GMT-10:00) Hawaii', 'America/Anchorage' => '(GMT-09:00) Alaska', 'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada); Tijuana', 'America/Phoenix' => '(GMT-07:00) Arizona', 'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)', 'America/Chihuahua' => '(GMT-07:00) Chihuahua, La Paz, Mazatlan', 'America/Managua' => '(GMT-06:00) Central America', 'America/Regina' => '(GMT-06:00) Saskatchewan', 'America/Mexico_City' => '(GMT-06:00) Guadalajara, Mexico City, Monterrey', 'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)', 'America/Indianapolis' => '(GMT-05:00) Indiana (East)', 'America/Bogota' => '(GMT-05:00) Bogota, Lima, Quito', 'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)', 'America/Caracas' => '(GMT-04:00) Caracas, La Paz', 'America/Santiago' => '(GMT-04:00) Santiago', 'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)', 'America/St_Johns' => '(GMT-03:30) Newfoundland', 'America/Buenos_Aires' => '(GMT-03:00) Buenos Aires, Georgetown', 'America/Godthab' => '(GMT-03:00) Greenland', 'America/Sao_Paulo' => '(GMT-03:00) Brasilia', 'America/Noronha' => '(GMT-02:00) Mid-Atlantic', 'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.', 'Atlantic/Azores' => '(GMT-01:00) Azores', 'Africa/Casablanca' => '(GMT) Casablanca, Monrovia', 'Europe/London' => '(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London', 'Africa/Lagos' => '(GMT+01:00) West Central Africa', 'Europe/Berlin' => '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna', 'Europe/Paris' => '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris', 'Europe/Sarajevo' => '(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb', 'Europe/Belgrade' => '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague', 'Africa/Johannesburg' => '(GMT+02:00) Harare, Pretoria', 'Asia/Jerusalem' => '(GMT+02:00) Jerusalem', 'Europe/Istanbul' => '(GMT+02:00) Athens, Istanbul, Minsk', 'Europe/Helsinki' => '(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius', 'Africa/Cairo' => '(GMT+02:00) Cairo', 'Europe/Bucharest' => '(GMT+02:00) Bucharest', 'Africa/Nairobi' => '(GMT+03:00) Nairobi', 'Asia/Riyadh' => '(GMT+03:00) Kuwait, Riyadh', 'Europe/Moscow' => '(GMT+03:00) Moscow, St. Petersburg, Volgograd', 'Asia/Baghdad' => '(GMT+03:00) Baghdad', 'Asia/Tehran' => '(GMT+03:30) Tehran', 'Asia/Muscat' => '(GMT+04:00) Abu Dhabi, Muscat', 'Asia/Tbilisi' => '(GMT+04:00) Baku, Tbilisi, Yerevan', 'Asia/Kabul' => '(GMT+04:30) Kabul', 'Asia/Karachi' => '(GMT+05:00) Islamabad, Karachi, Tashkent', 'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg', 'Asia/Calcutta' => '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi', 'Asia/Katmandu' => '(GMT+05:45) Kathmandu', 'Asia/Colombo' => '(GMT+06:00) Sri Jayawardenepura', 'Asia/Dhaka' => '(GMT+06:00) Astana, Dhaka', 'Asia/Novosibirsk' => '(GMT+06:00) Almaty, Novosibirsk', 'Asia/Rangoon' => '(GMT+06:30) Rangoon', 'Asia/Bangkok' => '(GMT+07:00) Bangkok, Hanoi, Jakarta', 'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk', 'Australia/Perth' => '(GMT+08:00) Perth', 'Asia/Taipei' => '(GMT+08:00) Taipei', 'Asia/Singapore' => '(GMT+08:00) Kuala Lumpur, Singapore', 'Asia/Hong_Kong' => '(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi', 'Asia/Irkutsk' => '(GMT+08:00) Irkutsk, Ulaan Bataar', 'Asia/Tokyo' => '(GMT+09:00) Osaka, Sapporo, Tokyo', 'Asia/Seoul' => '(GMT+09:00) Seoul', 'Asia/Yakutsk' => '(GMT+09:00) Yakutsk', 'Australia/Darwin' => '(GMT+09:30) Darwin', 'Australia/Adelaide' => '(GMT+09:30) Adelaide', 'Pacific/Guam' => '(GMT+10:00) Guam, Port Moresby', 'Australia/Brisbane' => '(GMT+10:00) Brisbane', 'Asia/Vladivostok' => '(GMT+10:00) Vladivostok', 'Australia/Hobart' => '(GMT+10:00) Hobart', 'Australia/Sydney' => '(GMT+10:00) Canberra, Melbourne, Sydney', 'Asia/Magadan' => '(GMT+11:00) Magadan, Solomon Is., New Caledonia', 'Pacific/Fiji' => '(GMT+12:00) Fiji, Kamchatka, Marshall Is.', 'Pacific/Auckland' => '(GMT+12:00) Auckland, Wellington', 'Pacific/Tongatapu' => '(GMT+13:00) Nuku\'alofa');

	require_once('Systems.class.php');
	$systems = Systems::getInstance();

	require_once('FormErrors.class.php');
	$formErrors = FormErrors::getInstance();

	$addJSFiles = array();
	$bodyClasses = array();
?>