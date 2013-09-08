<?
	$action = '';
	require_once('includes/requires.php');
?>
<!DOCTYPE html>
<html>
<head>
<?php //require_once(FILEROOT.'/javascript/js.php'); ?>
	<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.min.js"></script>
</head>

<body>
<?php
	require_once('javascript/markItUp/markitup.bbcode-parser.php');

//	foreach ($_SERVER as $key => $value) echo "$key => $value<br><br>";
/*$arr = mcrypt_list_algorithms();
foreach ($arr as $key => $value) echo "$key => $value<br>";
echo '<br>';
$arr = mcrypt_list_modes();
foreach ($arr as $key => $value) echo "$key => $value<br>";*/
	
/*	$stones = 1;
	
	$stones =+ 1;
	
	echo $stones;*/
	
//	$stones = 10.996666666667;
	
//	echo formatStones($stones);
	
//	$convert = '';
//	for ($count = 1; $count <= 54; $count++) { $convert .= '1'; }
//	echo base_convert(0x128f94eabd25280000000000000000000000000000000, 10, 16	);
//	echo base_convert(substr($convert, 0, 28), 2, 16);

//foreach ($_SERVER as $key => $value) echo "$key => $value <br><br>";
//	$mysql->query('SELECT * FROM marvel_actions WHERE LOWER(name) = LOWER("'.sanatizeString(' aCrobatics'."\n").'")');
//	echo $mysql->rowCount();
//	echo filterString('ass').'<br>';
//	echo filterString('asss').'<br>';
//	echo filterString('massive');
//echo substr('asdf@asdf.com', 0, strpos('asdf@asdf.com', '@'));
//foreach (timezone_identifiers_list() as $timezone) echo timezone_offset_get('now', $timezone).'<br><br>';
//echo date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' +1 hour'));
//if (date('I')) echo 1;

//echo date('H:i:s', strtotime('05:00 +1 hour'));
//echo preg_match('/[+-][01]\d{1}:\d{2}/', '+04:00');
/*echo str_replace('~"D ', '~"1"~"', '"Etc/GMT+12"~"0"~"(GMT-12:00) International Date Line West"
"Pacific/Apia"~"0"~"(GMT-11:00) Midway Island, Samoa"
"Pacific/Honolulu"~"0"~"(GMT-10:00) Hawaii"
"America/Anchorage"~"D (GMT-09:00) Alaska"
"America/Los_Angeles"~"D (GMT-08:00) Pacific Time (US & Canada); Tijuana"
"America/Phoenix"~"0"~"(GMT-07:00) Arizona"
"America/Denver"~"D (GMT-07:00) Mountain Time (US & Canada)"
"America/Chihuahua"~"D (GMT-07:00) Chihuahua, La Paz, Mazatlan"
"America/Managua"~"0"~"(GMT-06:00) Central America"
"America/Regina"~"0"~"(GMT-06:00) Saskatchewan"
"America/Mexico_City"~"D (GMT-06:00) Guadalajara, Mexico City, Monterrey"
"America/Chicago"~"D (GMT-06:00) Central Time (US & Canada)"
"America/Indianapolis"~"0"~"(GMT-05:00) Indiana (East)"
"America/Bogota"~"0"~"(GMT-05:00) Bogota, Lima, Quito"
"America/New_York"~"D (GMT-05:00) Eastern Time (US & Canada)"
"America/Caracas"~"0"~"(GMT-04:00) Caracas, La Paz"
"America/Santiago"~"D (GMT-04:00) Santiago"
"America/Halifax"~"D (GMT-04:00) Atlantic Time (Canada)"
"America/St_Johns"~"D (GMT-03:30) Newfoundland"
"America/Buenos_Aires"~"0"~"(GMT-03:00) Buenos Aires, Georgetown"
"America/Godthab"~"D (GMT-03:00) Greenland"
"America/Sao_Paulo"~"D (GMT-03:00) Brasilia"
"America/Noronha"~"D (GMT-02:00) Mid-Atlantic"
"Atlantic/Cape_Verde"~"0"~"(GMT-01:00) Cape Verde Is."
"Atlantic/Azores"~"D (GMT-01:00) Azores"
"Africa/Casablanca"~"0"~"(GMT) Casablanca, Monrovia"
"Europe/London"~"D (GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London"
"Africa/Lagos"~"0"~"(GMT+01:00) West Central Africa"
"Europe/Berlin"~"D (GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna"
"Europe/Paris"~"D (GMT+01:00) Brussels, Copenhagen, Madrid, Paris"
"Europe/Sarajevo"~"D (GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb"
"Europe/Belgrade"~"D (GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague"
"Africa/Johannesburg"~"0"~"(GMT+02:00) Harare, Pretoria"
"Asia/Jerusalem"~"0"~"(GMT+02:00) Jerusalem"
"Europe/Istanbul"~"D (GMT+02:00) Athens, Istanbul, Minsk"
"Europe/Helsinki"~"D (GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius"
"Africa/Cairo"~"D (GMT+02:00) Cairo"
"Europe/Bucharest"~"D (GMT+02:00) Bucharest"
"Africa/Nairobi"~"0"~"(GMT+03:00) Nairobi"
"Asia/Riyadh"~"0"~"(GMT+03:00) Kuwait, Riyadh"
"Europe/Moscow"~"D (GMT+03:00) Moscow, St. Petersburg, Volgograd"
"Asia/Baghdad"~"D (GMT+03:00) Baghdad"
"Asia/Tehran"~"D (GMT+03:30) Tehran"
"Asia/Muscat"~"0"~"(GMT+04:00) Abu Dhabi, Muscat"
"Asia/Tbilisi"~"D (GMT+04:00) Baku, Tbilisi, Yerevan"
"Asia/Kabul"~"0"~"(GMT+04:30) Kabul"
"Asia/Karachi"~"0"~"(GMT+05:00) Islamabad, Karachi, Tashkent"
"Asia/Yekaterinburg"~"D (GMT+05:00) Ekaterinburg"
"Asia/Calcutta"~"0"~"(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi"
"Asia/Katmandu"~"0"~"(GMT+05:45) Kathmandu"
"Asia/Colombo"~"0"~"(GMT+06:00) Sri Jayawardenepura"
"Asia/Dhaka"~"0"~"(GMT+06:00) Astana, Dhaka"
"Asia/Novosibirsk"~"D (GMT+06:00) Almaty, Novosibirsk"
"Asia/Rangoon"~"0"~"(GMT+06:30) Rangoon"
"Asia/Bangkok"~"0"~"(GMT+07:00) Bangkok, Hanoi, Jakarta"
"Asia/Krasnoyarsk"~"D (GMT+07:00) Krasnoyarsk"
"Australia/Perth"~"0"~"(GMT+08:00) Perth"
"Asia/Taipei"~"0"~"(GMT+08:00) Taipei"
"Asia/Singapore"~"0"~"(GMT+08:00) Kuala Lumpur, Singapore"
"Asia/Hong_Kong"~"0"~"(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi"
"Asia/Irkutsk"~"D (GMT+08:00) Irkutsk, Ulaan Bataar"
"Asia/Tokyo"~"0"~"(GMT+09:00) Osaka, Sapporo, Tokyo"
"Asia/Seoul"~"0"~"(GMT+09:00) Seoul"
"Asia/Yakutsk"~"D (GMT+09:00) Yakutsk"
"Australia/Darwin"~"0"~"(GMT+09:30) Darwin"
"Australia/Adelaide"~"D (GMT+09:30) Adelaide"
"Pacific/Guam"~"0"~"(GMT+10:00) Guam, Port Moresby"
"Australia/Brisbane"~"0"~"(GMT+10:00) Brisbane"
"Asia/Vladivostok"~"D (GMT+10:00) Vladivostok"
"Australia/Hobart"~"D (GMT+10:00) Hobart"
"Australia/Sydney"~"D (GMT+10:00) Canberra, Melbourne, Sydney"
"Asia/Magadan"~"0"~"(GMT+11:00) Magadan, Solomon Is., New Caledonia"
"Pacific/Fiji"~"0"~"(GMT+12:00) Fiji, Kamchatka, Marshall Is."
"Pacific/Auckland"~"D (GMT+12:00) Auckland, Wellington"
"Pacific/Tongatapu"~"0"~"(GMT+13:00) Nuku\'alofa"
');
$max = 0;
$biggest = '';
$file = fopen('../timezones4.csv', 'r');
while (($data = fgetcsv($file, 1000, ",")) !== FALSE) echo '"'.$data[0].'",';//{ if (strlen($data[0]) > $max) { $max = strlen($data[0]); $biggest = $data[0]; } }
fclose($file);
echo $max.','.$biggest;
$testTime = new DateTime('2009-02-03 15:31:59');
$testTime->setTimezone(new DateTimeZone('America/New_York'));
echo $testTime->format('Y-m-d H:i:s').'<br>';
if ($testTime->format('I') == 1) {
	$offset = $testTime->getOffset();
	$testTime->modify('+6 months');
	$offset = $testTime->getOffset() - $offset;
	$testTime->modify('-6 months');
echo $offset.'<br>';
	$testTime->modify($offset.' seconds');
}
echo $testTime->format('Y-m-d H:i:s').'<br>';
/*echo $testTime->format('I').'<br>';
echo $testTime->getOffset().' seconds<br>';
$testTime->modify('+6 months');
echo $testTime->format('I').'<br>';
echo $testTime->getOffset().' seconds';*/
//echo serialize('test asdf');
//echo dechex(str_pad(ord('a'), 3, '0', STR_PAD_LEFT));
//echo base_convert('12345678901234', 10, 36).'<br>';
//echo '12345678901234<br>'.base_convert('4djiygmia', 36, 10);
/*$str = '';
$rnd = range(0, 53);
shuffle($rnd);
//print_r($rnd);
foreach ($rnd as $value) $str .= str_pad($value, 2, '0', STR_PAD_LEFT);
//echo base_convert($str, 10, 36);
echo strlen('4jrz5woo8sgwwks8g000ww04sog4w4oow4wwocco4ws0wwgg0gowsco4408s0wg4');*/
//echo urlencode(gzcompress('12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890', 9));

/*preg_match_all('/(\d+d\d+([+-]\d+)?)/', '2d2+2, 2d6-2', $matches);
foreach($matches[0] as $value) echo $value.'<br>';
echo '<br>';
foreach($matches[1] as $value) echo $value.'<br>';*/

//rollDice('2d6+1');

//echo substr('move_1', 5);
//echo substr('addUser_2', 8);

//preg_match('/_(\d{1,2})_/', 'moveUp_1_test', $matches);
//print_r($matches);

/*$allCategories = array(7 => 1, 5 => 2, 10 => 3);
$actionKey = 5;
$allCategories[array_search($allCategories[$actionKey] - 1, $allCategories)] += 1;
$allCategories[$actionKey] -= 1;

asort($allCategories);
print_r($allCategories);*/

//$ta = array(1 => 0, 3 => 0, 7 => 0, 2 => 1);
//print_r($ta);

//print_r(retrieveHeritage(2));

//print_r(retrievePermissions(array('createThread'), 1, 1));

//print_r(retrieveHeritage(1, 0));
//print_r(array_keys(array(3 => 0, 5 => 0, 2 => 1), 0));
//foreach(array_keys(array(3 => 0, 5 => 0, 2 => 1), 0) as $value) echo $value.'<br>';
//unset($_SESSION['chatLastPull']);
//foreach($_SERVER as $key_name => $key_value) {
//print $key_name . " = " . $key_value . "<br>";
//}
//echo ini_get('short_open_tag');
//echo intval("-1");
//echo sprintf("%04d", 10);
/*$test = array_unique(array(1, 2, 3, 4, 6, 7, 7, 2, 0));
sort($test);
print_r($test);*/
//print_r(array_intersect(array(10), array(0, 1, 2, 3, 4, 6, 8, 9)));
//echo hash('sha256', 'xU3Fh9XLo21mlHuk6H31rohit86');
//$var = sanatizeString('"test
//"');
//echo printReady($var);
//echo printReady(str_replace(array('\r', '\n'), array('', ''), $var));

//echo get_magic_quotes_gpc();

//echo preg_replace('/^the /', '', 'the cat is cool');

//$str = "lost the complete 14th season";
//$str = preg_replace("/the\scomplete\s([0-9]+)(?:[stndrh]{2})\s(season|series)/", "$2 $1", $str);
//echo $str;

//echo strtotime('2011-08-08 12:00') > time();

//echo preg_match('/^[A-Z0-9]{20}$/i', 'asdf0123asdf0123asdf');

/*for ($count = 0; $count <= 
$validChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
$randomStr = "";
for ($count = 0; $count < 20; $count++) $randomStr .= $validChars[rand(0, strlen($validChars) - 1)];*/

/*$heritage = array(2);
$readData = array(0 => array(
					'forums' => array(
						1 => array(
							'forums' => array(
								3 => array(
									'threads' => array(
										103 => array(
											'lastRead' => 10,
											'lastPost' => 10
										)
									),
									'markedRead' => 0
								)
							),
							'threads' => array(
								101 => array(
									'lastRead' => 12,
									'lastPost' => 12
								),
								102 => array(
									'lastRead' => 0,
									'lastPost' => 0
								),
								104 => array(
									'lastRead' => 0,
									'lastPost' => 0
								)
							),
							'markedRead' => 8
						),
						2 => array(
							'threads' => array(
								100 => array(
									'lastRead' => 21,
									'lastPost' => 21
								),
							),
							'markedRead' => 13
						)
					),
					'threads' => array(
					),
					'markedRead' => 0
				)
			  );
$readData = array(0 => array(
					'forums' => array(
					),
					'threads' => array(
					),
					'markedRead' => 0
				)
			  );

//echo checkNewPosts($heritage, $readData, 21)?1:0;
echo setcookie('readData', serialize($readData), strtotime('+1 year'), COOKIE_ROOT);
//print_r(unserialize($_COOKIE['readData']));

/*$test = array(array(), array());
$point = &$test;
$point = &$point[0];
$point[] = 1;
print_r($test);

function test($asdf) {
$a = &$asdf;
$a = array();
}

$asdf = array('not a');
$a = &$asdf;
$a[] = 1;
print_r($asdf); echo '<br>';
test($asdf);
print_r($asdf);*/

/*$test = '			content plus more
and more';
echo $test;*/

//echo strlen('a:1:{i:2;a:1:{i:0;a:3:{s:6:"forums";a:3:{i:1;a:3:{s:6:"forums";a:3:{i:3;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:4;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:1:{i:31;a:2:{s:8:"lastRead";s:3:"113";s:8:"lastPost";s:3:"113";}}s:10:"markedRead";i:0;}i:5;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:6;a:3:{s:6:"forums";a:3:{i:7;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:8;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:9;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:2;a:3:{s:6:"forums";a:6:{i:10;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:1:{i:28;a:2:{s:8:"lastRead";s:3:"109";s:8:"lastPost";s:3:"109";}}s:10:"markedRead";i:0;}i:11;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:12;a:3:{s:6:"forums";a:2:{i:16;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:2:{i:23;a:2:{s:8:"lastRead";s:3:"121";s:8:"lastPost";s:3:"121";}i:24;a:2:{s:8:"lastRead";i:0;s:8:"lastPost";s:2:"80";}}s:10:"markedRead";i:0;}i:15;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:1:{i:7;a:2:{s:8:"lastRead";i:0;s:8:"lastPost";s:1:"8";}}s:10:"markedRead";i:0;}i:13;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:17;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:18;a:3:{s:6:"forums";a:2:{i:19;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:1:{i:33;a:2:{s:8:"lastRead";s:3:"119";s:8:"lastPost";s:3:"119";}}s:10:"markedRead";i:0;}i:20;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:2:{i:37;a:2:{s:8:"lastRead";i:0;s:8:"lastPost";s:3:"118";}i:35;a:2:{s:8:"lastRead";i:0;s:8:"lastPost";s:3:"116";}}s:10:"markedRead";s:3:"119";}}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:2:{i:23;a:1:{i:0;a:1:{s:7:"threads";a:1:{i:23;a:2:{s:8:"lastRead";s:3:"111";s:8:"lastPost";s:3:"111";}}}}i:31;a:1:{i:0;a:1:{s:7:"threads";a:1:{i:31;a:2:{s:8:"lastRead";s:3:"113";s:8:"lastPost";s:3:"113";}}}}}s:10:"markedRead";s:3:"103";}}}');

//$test['asdf.asdf'] = 1;
//print_r($test);
//echo serialize(array(0 => array('forums' => array(), 'threads' => array(), 'markedRead' => 0)));

//foreach (glob(FILEROOT.'/images/cards/*.png') as $file) if (preg_match('/.*?\/c[0-9]{1,2}_mini\.png/', $file)) rename($file, preg_replace('/(.*?\/)c([0-9]{1,2}_mini\.png)/', '$1$2', $file));
/*foreach (glob(FILEROOT.'/images/cards/*.png') as $file) { if (preg_match('/.*?\/s[0-9]{1,2}(?:_mini)?\.png/', $file)) {
	preg_match('/.*?\/s([0-9]{1,2})(?:_mini)?\.png/', $file, $matches);
	$newNum = intval($matches[1]) + 13 * 3;
//	echo preg_replace('/d[0-9]{1,2}/', $newNum, $file).'<br>';
	rename($file, preg_replace('/s[0-9]{1,2}/', $newNum, $file));
} }*/

//echo cardText(1, 'pcwj');

//preg_match_all('/([a-z0-9]+): ([0-9]+)/i', 'held: 1 foot: 0', $results, PREG_SET_ORDER);
//print_r($results);

/*preg_match_all('/([a-z0-9]+?): ([0-9]+?)/i', 'held: 1 foot: 0', $matches, PREG_SET_ORDER);
$items = array();
foreach ($matches as $match) {
$items[$match[1]] = $match[2];
}

print_r($items);*/

/*echo date('Y-m-d H:i:s I e', strtotime('2010-10-30 13:29')).'<br>'.date('Y-m-d H:i:s I e', strtotime('2010-10-30 13:29 Europe/London')).'<br><br>';
$date = new DateTime('2010-10-30 13:29', new DateTimeZone('GMT'));
echo $date->format('Y-m-d H:i:sP') . "<br>";

$date->setTimezone(new DateTimeZone('Europe/London'));
echo $date->format('Y-m-d H:i:sP') . "\n";*/

//foreach(glob(SITEROOT.'/styles/characters/*') as $file) echo "$file<br>";

//if ($test = floor (40/100)) echo 1;
//$mysql->query('SELECT @@global.time_zone, @@session.time_zone;');
//print_r($mysql->fetch());

//$validationStr = preg_match('/^[a-z0-9]*$/i', $_GET['validate'])?$_GET['validate']:FALSE;
//echo $validationStr;

//$str = '.asdf';
//echo preg_match('/^\w/i', $str);

//$text = 'Test [quote="Test"]Testing, testing[/quote] ';
/*$in = array('/\[quote(?:="(\w+?)")?](.*?)\[\/quote\]/ms');
$out = array('<blockquote><div>\1 says:</div>\2</blockquote>',);
$text = preg_replace($in, $out, $text);
$text = preg_replace('/\<div\> says:\<\/div\>/', '<div>&nbsp;</div>', $text);
echo $text;*/
//echo BBCode2Html($text);

//echo decToB26(52);

//echo(serialize(array(0 => array('forums' => array(), 'threads' => array(), 'markedRead' => 0))));
//echo(serialize(array(0 => 0)));
//echo(serialize(array()));

//echo dechex(51);

/*$test = array('a', 'b', 'c', 'd');
array_splice($test, 1, 0, array(10, 20));
print_r($test);*/

//echo substr_replace('abcd', '12', 2, 0);
//echo ~'0100' | ~'1000';

//preg_match_all('/\d+/', 'Barb 4, Rogue 16', $matches);
//print_r($matches);

/*$mysql->query('SELECT userID, username FROM users WHERE userID = 1');
$array1 = $mysql->fetch();
$mysql->query('SELECT threadID, forumID from threads where threadID = 1;');
//$array2 = $mysql->fetch();
//print_r($array1 + $mysql->fetch());
$array1 += $mysql->fetch();
print_r($array1);*/

//echo $mysql->setupInserts(array(array('test' => 1, 'test2' => 5, 'test3' => 3), array('test' => 8, 'test2' => 2, 'test3' => 13)));

//echo ucwords(mb_convert_case('intrument (flute)', MB_CASE_TITLE));

//print_r(unserialize('a:1:{i:0;a:3:{s:6:"forums";a:3:{i:1;a:3:{s:6:"forums";a:3:{i:3;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:2:{i:1;a:2:{s:8:"lastRead";s:1:"1";s:8:"lastPost";s:1:"1";}i:21;a:2:{s:8:"lastRead";s:3:"180";s:8:"lastPost";s:3:"180";}}s:10:"markedRead";i:0;}i:4;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:5;a:3:{s:6:"forums";a:2:{i:11;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:12;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:4:{i:2;a:2:{s:8:"lastRead";s:1:"2";s:8:"lastPost";s:1:"2";}i:23;a:2:{s:8:"lastRead";s:3:"182";s:8:"lastPost";s:3:"182";}i:24;a:2:{s:8:"lastRead";s:3:"183";s:8:"lastPost";s:3:"183";}i:25;a:2:{s:8:"lastRead";s:3:"185";s:8:"lastPost";s:3:"185";}}s:10:"markedRead";i:0;}}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:6;a:3:{s:6:"forums";a:3:{i:7;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:8;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:9;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:2;a:3:{s:6:"forums";a:4:{i:10;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:14;a:3:{s:6:"forums";a:2:{i:16;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:3:{i:16;a:2:{s:8:"lastRead";s:3:"178";s:8:"lastPost";s:3:"178";}i:17;a:2:{s:8:"lastRead";s:3:"179";s:8:"lastPost";s:3:"179";}i:18;a:2:{s:8:"lastRead";i:0;s:8:"lastPost";s:2:"68";}}s:10:"markedRead";i:0;}i:15;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:0:{}s:10:"markedRead";s:3:"177";}i:20;a:3:{s:6:"forums";a:0:{}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}i:17;a:3:{s:6:"forums";a:2:{s:8:"lastRead";s:3:"189";s:8:"lastPost";s:3:"189";}s:7:"threads";a:1:{i:22;a:2:{s:8:"lastRead";s:3:"181";s:8:"lastPost";s:3:"181";}}s:10:"markedRead";i:0;}}s:7:"threads";a:0:{}s:10:"markedRead";i:0;}}s:7:"threads";a:6:{i:1;a:1:{i:0;a:1:{s:7:"threads";a:1:{i:1;a:2:{s:8:"lastRead";i:1;s:8:"lastPost";i:1;}}}}i:21;a:1:{i:0;a:1:{s:7:"threads";a:1:{i:21;a:2:{s:8:"lastRead";s:3:"180";s:8:"lastPost";s:3:"180";}}}}i:22;a:1:{i:0;a:1:{s:7:"threads";a:1:{i:22;a:2:{s:8:"lastRead";s:3:"181";s:8:"lastPost";s:3:"181";}}}}i:23;a:1:{i:0;a:1:{s:7:"threads";a:1:{i:23;a:2:{s:8:"lastRead";s:3:"182";s:8:"lastPost";s:3:"182";}}}}i:24;a:1:{i:0;a:1:{s:7:"threads";a:1:{i:24;a:2:{s:8:"lastRead";s:3:"183";s:8:"lastPost";s:3:"183";}}}}i:25;a:1:{i:0;a:1:{s:7:"threads";a:1:{i:25;a:2:{s:8:"lastRead";i:185;s:8:"lastPost";i:185;}}}}}s:10:"markedRead";i:0;}}'));

/*function test (&$var1) {
	$var1 += 10;
}
$var1 = 5;
echo "$var1<br>";
test($var1);
echo "$var1<br>";*/

//echo str_pad(2, HERITAGE_PAD, 0, STR_PAD_LEFT);

/*$test = 'aewav';
ob_start();
include('games/process/newGameEmail.php');
$var = ob_get_contents();
ob_end_clean();
mail('dhvanit.mehta@gmail.com', 'test', $var, 'Content-type: text/html;');*/

//print_r(preg_grep('/001-.*/', array(1 => '001', '2' => '002', '3' => '001-003', 4 => '001-003-004')));

//echo @"test";

//echo preg_match('/^https?:///i', 'http://test.com');
	
/*	$name = 'donate_v2.jpg';
	$fileName = substr($name, 0, strrpos($name, '.'));
	$fileExt = substr($name, strrpos($name, '.'));
	echo $fileName;*/

//preg_match('/\[note(?:="(\w.+?)")?](.*)\[\/note\]/ms', '[note="Keleth Irdalth; Deus86"]hidden text[/note]', $matches);
//print_r($matches);
//print_r(preg_split('/[^\w]+/', $matches[1]));
//foreach (preg_split('/[^\w]+/', $matches[1]) as $info) echo "'$info'<br>";

//$isGM = TRUE;
//echo BBCode2Html('[note="test"]test[/note]');

//echo strtolower(preg_replace('/[^a-z0-9-]/i', '', str_replace(' ', '-', 'Pinot Noir')));

//print_r(array_fill(1, 54, 1));
//for ($count = 100; $count < 500; $count++) echo "($count, 0, 1), ";
//	require_once(FILEROOT.'/blog/wp-blog-header.php');
//	print_r(get_user_by('id', 1));

/*		$forumInfos = $mysql->query('SELECT forumID, heritage FROM forums WHERE forumID IN (4, 5, 6)');
		$allForumIDs = $forumIDs;
		while (list($indivForumID, $heritage) = $forumInfos->fetch()) {
			echo $indivForumID.','.$heritage.'<br>';
		}*/

//		echo crc32(strtolower('hunt.michael101@gmail.com')).strlen('hunt.michael101@gmail.com');

/*		$suit = array('H', 'S', 'D', 'C');
		$nums = array('A', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'J', 'Q', 'K');
		for ($sNum = 0; $sNum < 4; $sNum++) {
			for ($nNum = 0; $nNum < 13; $nNum++) {
?>
.pc_card.<?=$nums[$nNum]?>of<?=$suit[$sNum]?> {
	background-position: -<?=$sNum * 125?>px -<?=$nNum * 100?>px;
}

<?
			}
		}*/


//$sum = 0;
//for ($count = 1; $count <= 114; $count++) $sum += $count;
//	echo $sum;
//	echo intval('test');
/*	$skills = $mysql->query('SELECT * FROM skillsList');
	foreach ($skills as $skill) {
//		$name = mb_convert_case($skill['name'], MB_CASE_TITLE);
//		$name = str_replace('Of', 'of', $name);
		$name = searchFormat($skill['searchName']);
		echo $skill['skillID'].':'.$name.'<br>';
		$mysql->query('UPDATE skillsList SET searchName = "'.$name.'" WHERE modifierID = '.$skill['skillID']);
	}*/
?>
<script>
$(function () {
	var var1 = 1, var2 = 2;
	setTimeout(function (var1, var2) {
		console.log(var1, var2);
	}, 1000);
});
</script>
</body></html>