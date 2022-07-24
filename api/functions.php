<?php
 /*
	Gamers Plane Functions
	Created by RhoVisions, designer Rohit Sodhia
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if (!function_exists('is_countable')) {
	function is_countable($c)
	{
		return is_array($c) || $c instanceof Countable;
	}
}

/* General Functions */
function addPackage($package)
{
	include_once(FILEROOT . "/includes/packages/{$package}.package.php");
}

function utf8ize($input)
{
	if (is_array($input)) {
		foreach ($input as $key => $value)
			$input[$key] = utf8ize($value);
	} elseif (is_string($input))
		//			return utf8_encode($input);
		return mb_convert_encoding($input, 'UTF-8');
	return $input;
}

function displayJSON($data, $exit = true)
{
	header('Content-Type: application/json');
	echo json_encode(utf8ize($data));
	// header('Content-Type: application/json; charset=UTF-8');
	// echo json_encode($data, JSON_UNESCAPED_UNICODE);
	if ($exit)
		exit;
}

function randomAlphaNum($length)
{
	$validChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$randomStr = "";
	for ($count = 0; $count < $length; $count++) {
		$randomStr .= $validChars[mt_rand(0, strlen($validChars) - 1)];
	}

	return $randomStr;
}

function sanitizeString($string)
{
	$options = func_get_args();
	array_shift($options);

	if (in_array('search_format', $options)) {
		$string = preg_replace('/[^A-za-z0-9]/', ' ', $string);
		$options = array('lower', 'rem_dup_spaces');
	}

	$string = trim($string);
	$string = str_replace(array("<",">"), array("&lt;", "&gt;"),  $string);
	$string = strip_tags($string);
	//		$string = utf8_decode($string);
	if (in_array('lower', $options)) $string = strtolower($string);
	if (in_array('like_clean', $options)) $string = str_replace(array('%', '_'), array('\%', '\_'), strip_tags($string));
	if (in_array('rem_dup_spaces', $options)) $string = preg_replace('/\s+/', ' ', $string);

	return $string;
}

function printReady($input, $options = array('stripslashes', 'nl2br'))
{
	if (is_string($input)) {
		if (in_array('nl2br', $options)) {
			$input = str_replace("\r\n", "\n", $input);
			$input = nl2br($input);
		}
		if (in_array('stripslashes', $options)) {
			$input = stripslashes($input);
		}
	} elseif (is_array($input)) {
		foreach ($input as $key => $value) {
			$input[$key] = printReady($value);
		}
	}
	return $input;
}

function filterString($string)
{
	global $mysql;
	$filters = $mysql->query('SELECT word FROM wordFilter');
	$filterWords = array();
	$replacements = array();
	$stars = '';
	while ($word = $filters->fetchColumn()) {
		//			$filterWords[] = '/'.preg_replace('/(.*)(\[\^\\\w\\\d\]\*\\\s\?)/', '$1', preg_replace('/(.{1})/', '$1[^\w\d]*\s?', $word)).'/i';
		$filterWords[] = '/(\W|\s|^)' . preg_replace('/(.*)\\\s\?/', '$1', preg_replace('/(.*)(\[\^\\\w\\\d\]\*\\\s\?)/', '$1', preg_replace('/(.{1})/', '$1[^\w\d]*\s?', $word))) . '(\W|\s|$)/i';
		$stars = '$1';
		for ($count = 0; $count < strlen($word); $count++) $stars .= '*';
		$stars .= '$2';
		$replacements[] = $stars;
	}
	//		print_r($filterWords); echo '<br>';
	do {
		$string = preg_replace($filterWords, $replacements, $string);
	} while ($string != preg_replace($filterWords, $replacements, $string));
	return $string;
}

function showSign($num)
{
	return ($num >= 0 ? '+' : '') . $num;
}

function decToB26($num)
{
	$str = '';
	while ($num > 0) {
		$charNum = ($num - 1) % 26;
		$str = chr($charNum + 97) . $str;
		$num = floor(($num - $charNum) / 26);
	}

	return $str;
}

function b26ToDec($str)
{
	$num = 0;
	$str = strtolower($str);
	for ($count = 0; $count < strlen($str); $count++) $num += (ord($str[strlen($str) - 1 - $count]) - 96) * pow(26, $count);

	return $num;
}

function randomFloat()
{
	return mt_rand() / mt_getrandmax();
}

function getMailObj() {
	$mail = new PHPMailer();
	$mail->isSMTP();
	// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->Host = 'postfix';
	$mail->Port = 25;

	$mail->setFrom('contact@gamersplane.com', 'Gamers\' Plane');
	$mail->addReplyTo('contact@gamersplane.com', 'Gamers\' Plane');

	return $mail;
}

/* Session Functions */
function startSession()
{
	session_start();

	//		putenv('TZ=GMT');
	date_default_timezone_set('GMT');
}

/* DB Functions */
function sql_forumIDPad($forumID)
{
	return str_pad($forumID, HERITAGE_PAD, 0, STR_PAD_LEFT);
}

function mongo_getNextSequence($key)
{
	$mongo = DB::conn('mongo');

	$counter = $mongo->counters->findOneAndUpdate(
		['_id' => $key],
		['$inc' => ['seq' => 1]],
		['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
	);

	return $counter['seq'];
}

function genMongoId($id = null)
{
	return new MongoDB\BSON\ObjectID($id);
}

function genMongoDate($seconds = null)
{
	return new MongoDB\BSON\UTCDateTime($seconds ? $seconds * 1000 : null);
}

function getMongoSeconds($mongoDateTime)
{
	return $mongoDateTime->toDateTime()->getTimestamp();
}

/* Character Functions */
