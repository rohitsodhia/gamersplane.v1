<?php
 /*
	Gamers Plane Functions
	Created by RhoVisions, designer Rohit Sodhia
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

date_default_timezone_set('Etc/UTC');

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

function addStyle($script, $priority = 10)
{
	global $stylesToAdd, $styleVersions;
	if (!is_array($stylesToAdd[$priority])) {
		$stylesToAdd[$priority] = [];
	}
	$version = isset($styleVersions[$script]) ? $styleVersions[$script] : '1.0.0';
	$stylesToAdd[$priority][] = $script . '?v=' . $version;
}

function getStyleVersion($script)
{
	global $styleVersions;
	$version = isset($styleVersions[$script]) ? $styleVersions[$script] : '1.0.0';
	return $version;
}

function getJSVersion($script)
{
	global $jsVersions;
	$version = isset($jsVersions[$script]) ? $jsVersions[$script] : '1.0.0';
	return $version;
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

function randomFloat()
{
	return mt_rand() / mt_getrandmax();
}

function tabOrder($jump = 0)
{
	global $tabNum;

	$jump += 1;

	if (isset($tabNum)) {
		$tabNum += $jump;
	} else {
		$tabNum = 1;
	}

	return $tabNum;
}

function camelcase($strings)
{
	if (!is_array($strings)) {
		$strings = explode(' ', $strings);
	}
	$first = true;
	$finalString = '';

	foreach ($strings as $indivString) {
		$indivString = strtolower($indivString);

		if ($first) {
			$first = false;
		} else {
			$indivString[0] = strtoupper($indivString[0]);
		}

		$finalString .= $indivString;
	}

	return $finalString;
}

function sanitizeString($string)
{
	$options = func_get_args();
	array_shift($options);

	if (in_array('search_format', $options)) {
		$string = preg_replace('/[^A-za-z0-9]/', ' ', $string);
		$options = ['lower', 'rem_dup_spaces'];
	}

	$string = trim($string);

	if (!in_array('!strip_tags', $options)) {
		$string = str_replace(array("<",">"), array("&lt;", "&gt;"),  $string);
		$string = strip_tags($string);
	}
	if (in_array('lower', $options)) {
		$string = strtolower($string);
	}
	if (in_array('like_clean', $options)) {
		$string = str_replace(['%', '_'], ['\%', '\_'], strip_tags($string));
	}
	if (in_array('rem_dup_spaces', $options)) {
		$string = preg_replace('/\s+/', ' ', $string);
	}

	return $string;
}

function printReady($string, $options = ['stripslashes', 'nl2br'])
{
	if (in_array('nl2br', $options)) {
		$string = str_replace("\r\n", "\n", $string);
		$string = nl2br($string);
	}
	if (in_array('stripslashes', $options)) {
		$string = stripslashes($string);
	}

	return $string;
}

function filterString($string)
{
	$mysql = DB::conn('mysql');

	$filters = $mysql->query('SELECT word FROM wordFilter');
	$filterWords = [];
	$replacements = [];
	$stars = '';
	while ($word = $filters->fetchColumn()) {
		//			$filterWords[] = '/'.preg_replace('/(.*)(\[\^\\\w\\\d\]\*\\\s\?)/', '$1', preg_replace('/(.{1})/', '$1[^\w\d]*\s?', $word)).'/i';
		$filterWords[] = '/(\W|\s|^)' . preg_replace('/(.*)\\\s\?/', '$1', preg_replace('/(.*)(\[\^\\\w\\\d\]\*\\\s\?)/', '$1', preg_replace('/(.{1})/', '$1[^\w\d]*\s?', $word))) . '(\W|\s|$)/i';
		$stars = '$1';
		for ($count = 0; $count < strlen($word); $count++) {
			$stars .= '*';
		}
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
	for ($count = 0; $count < strlen($str); $count++) {
		$num += (ord($str[strlen($str) - 1 - $count]) - 96) * pow(26, $count);
	}

	return $num;
}

function addBodyClass($class)
{
	global $bodyClasses;

	if (strlen($class)) {
		$bodyClasses[] = $class;
	}
}

function addLoginRecord($userID, $success)
{
	$mysql = DB::conn('mysql');

	$userID = intval($userID);
	$success = $success ? 1 : 0;

	$mysql->query("INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES ({$userID}, NOW(), '{$_SERVER['REMOTE_ADDR']}', {$success})");

	return true;
}

function displayJSON($data, $exit = false)
{
	header('Content-Type: application/json');
	echo json_encode($data);
	if ($exit) {
		exit;
	}
}

function getMailObj() {
	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->Host = 'gamersplane.com';
	$mail->Port = 25;

	$mail->setFrom('contact@gamersplane.com', 'Gamers\' Plane');
	$mail->addReplyTo('contact@gamersplane.com', 'Gamers\' Plane');

	return $mail;
}

/*	Session Functions */
function startSession()
{
	session_start();

	//		putenv('TZ=GMT');
	date_default_timezone_set('GMT');
}

/* Character Functions */
function getCharacterClass($characterID)
{
	$mongo = DB::conn('mongo');

	$characterID = intval($characterID);
	$system = $mongo->characters->findOne(
		['characterID' => $characterID],
		['projection' => ['system' => true]]
	)['system'];

	return $system ? $system : null;
}

/* Tools Functions */
function rollDice($roll, $rerollAces = 0)
{
	list($numDice, $diceType) = explode('d', str_replace(' ', '', trim($roll)));
	$numDice = intval($numDice);
	if (strpos($diceType, '-')) {
		list($diceType, $modifier) = explode('-', $diceType);
		$modifier = intval('-' . $modifier);
	} elseif (strpos($diceType, '+')) {
		list($diceType, $modifier) = explode('+', $diceType);
	} else {
		$modifier = 0;
	}
	$diceType = intval($diceType);
	if ($numDice > 0 && $diceType > 1 && $numDice <= 1000 && $diceType <= 1000) {
		$totalRoll = $modifier;
		$indivRolls = ['dice' => [], 'mod' => intval($modifier)];
		for ($rollCount = 0; $rollCount < $numDice; $rollCount++) {
			$curRoll = mt_rand(1, $diceType);
			$totalRoll += $curRoll;

			if (isset($indivRolls['dice'][$rollCount]) && is_array($indivRolls['dice'][$rollCount])) {
				$indivRolls['dice'][$rollCount][] = $curRoll;
			} elseif ($curRoll == $diceType && $rerollAces) {
				$indivRolls['dice'][$rollCount] = [$curRoll];
			} else {
				$indivRolls['dice'][$rollCount] = $curRoll;
			}

			if ($curRoll == $diceType && $rerollAces && $diceType>1) {
				$rollCount -= 1;
			}
		}

		return ['result' => $totalRoll, 'indivRolls' => $indivRolls, 'numDice' => $numDice, 'diceType' => $diceType, 'modifier' => $modifier];
	} else {
		return false;
	}
}

function displayIndivDice($dice)
{
	$diceString = '( ';

	foreach ($dice as &$die) {
		if (is_array($die)) $die = '[ ' . implode(', ', $die) . ' ]';
	}
	$diceString .= implode(', ', $dice) . ' )';

	return $diceString;
}

function newGlobalDeck($deckType)
{
	$mysql = DB::conn('mysql');

	$deckCheck = $mysql->prepare("SELECT short, name, deckSize FROM deckTypes WHERE short = :short");
	$deckCheck->execute(array(':short' => $deckType));
	if ($deckCheck->rowCount()) {
		$deckInfo = $deckCheck->fetch();
		$_SESSION['deckShort'] = $deckType;
		$_SESSION['deckName'] = $deckInfo['name'];
		$_SESSION['deck'] = array_fill(1, $deckInfo['deckSize'], 1);

		return [$deckShort, $deckInfo['name'], $deckInfo['deckSize']];
	} else {
		return false;
	}
}

function clearGlobalDeck($deckType)
{
	unset($_SESSION['deckShort'], $_SESSION['deckName'], $_SESSION['deck']);

	return true;
}

function cardText($card, $deck)
{
	if ($deck == 'pc') {
		if ($card <= 52) {
			$suit = ['Hearts', 'Spades', 'Diamonds', 'Clubs'];
			$cardNum = $card - (floor(($card - 1) / 13) * 13);

			if ($cardNum == 1) {
				$cardNum = 'Ace';
			} elseif ($cardNum == 11) {
				$cardNum = 'Jack';
			} elseif ($cardNum == 12) {
				$cardNum = 'Queen';
			} elseif ($cardNum == 13) {
				$cardNum = 'King';
			}

			return $cardNum . ' of ' . $suit[floor(($card - 1) / 13)];
		} elseif ($card == 53) {
			return 'Black Joker';
		} elseif ($card == 54) {
			return 'Red Joker';
		}
	}
}

function getCardImg($cardNum, $deckType, $size = '')
{
	$mysql = DB::conn('mysql');

	$validSizes = ['', 'mid', 'mini'];
	if (!in_array($size, $validSizes)) {
		$size = '';
	}
	if ($size != '') {
		$size = ' ' . $size;
	}

	$deckInfo = $mysql->prepare("SELECT class, image FROM deckTypes WHERE short = :short");
	$deckInfo->execute([':short' => $deckType]);
	$deckInfo = $deckInfo->fetch();

	$classes = '';

	if ($deckInfo['class'] == 'pc') {
		if ($cardNum <= 52) {
			$suit = ['hearts', 'spades', 'diamonds', 'clubs'];
			$classes = $cardNum - (floor(($cardNum - 1) / 13) * 13);

			if ($classes == 1) {
				$classes = 'A';
			} elseif ($classes == 11) {
				$classes = 'J';
			} elseif ($classes == 12) {
				$classes = 'Q';
			} elseif ($classes == 13) {
				$classes = 'K';
			}

			$classes = 'num_' . $classes;

			$classes .= ' ' . $suit[floor(($cardNum - 1) / 13)];
		} elseif ($cardNum == 53) {
			$classes = 'blackJoker';
		} elseif ($cardNum == 54) {
			$classes = 'redJoker';
		}
	}

	return '<div class="cardWindow deck_' . $deckInfo['class'] . $size . '"><img src="/images/tools/cards/' . $deckInfo['image'] . '.png" title="' . cardText($cardNum, $deckInfo['class']) . '" alt="' . cardText($cardNum, $deckInfo['class']) . '" class="' . $classes . '"></div>';
}

/* Forum Functions */
function buildForumStructure($rawForums)
{
	$forums = [['info' => $rawForums[0], 'children' => []]];
	foreach ($rawForums as $key => $forum) {
		if ($key != 0) {
			$heritage = array_map('intval', explode('-', $forum['heritage']));
			$currentParent = &$forums[0];
			for ($count = 0; $count + 1 < sizeof($heritage); $count++) {
				$currentParent = &$currentParent['children'][$heritage[$count]];
			}
			$currentParent['children'][$forum['forumID']] = ['info' => $forum, 'children' => []];
		}
	}

	return $forums;
}

function retrieveHeritage($forumID, $parent = 0)
{
	global $mysql;
	$level = 0;
	$family = [];

	if ($parent == 1) {
		$children = $mysql->query('SELECT forumID FROM forums WHERE parentID = ' . $forumID);
		while ($hForumID = $children->fetchColumn()) {
			$family[$hForumID] = 0;
		}
		$level = 1;
	}

	$heritage = $mysql->query('SELECT heritage FROM forums WHERE forumID = ' . $forumID);
	$heritage = $heritage->fetchColumn();
	$heritage = array_reverse(explode('-', $heritage));
	foreach ($heritage as $hForumID) {
		$family[$hForumID] = $level;
		$level++;
	}

	return $family;
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

function genMongoDate()
{
	return new MongoDB\BSON\UTCDateTime();
}

function getMongoSeconds($mongoDateTime)
{
	return $mongoDateTime->toDateTime()->getTimestamp();
}

function getUserTheme()
{
	global $currentUser,$loggedIn;
	if (isset($currentUser) && $loggedIn) {
		return $theme=$currentUser->usermeta['theme']??'';
	}

	return "";
}

function getUserThemeCss()
{
	$theme=getUserTheme();
	if($theme=='dark'){
		return '<link id="darkmodecss" href="/styles/themeDark.css?v='.getStyleVersion('/styles/themeDark.css').'" rel="stylesheet">';
	}

	return "";
}
