<?
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Notifications</h1>
		<div class="hbMargined">
<?

	$charHistories = $mysql->query("SELECT c.characterID, c.label, c.systemID, h.enactedBy, h.enactedOn, h.action, u.userID, u.username, g.gameID, g.title FROM characterHistory h INNER JOIN characters c ON c.characterID = h.characterID INNER JOIN users u ON u.userID = h.enactedBy LEFT JOIN games g ON h.gameID = g.gameID WHERE c.userID = {$userID} ORDER BY enactedOn DESC LIMIT 30");
	$cNotification = $charHistories->fetch();
	$gameHistories = $mysql->query("SELECT g.gameID, g.title, g.systemID, h.enactedBy, h.enactedOn, h.action, u.userID, u.username, au.userID aUserID, au.username aUsername, c.characterID, c.label charLabel, d.deckID, d.label deckLabel FROM gameHistory h INNER JOIN games g ON g.gameID = h.gameID INNER JOIN players p ON p.gameID = g.gameID INNER JOIN users u ON u.userID = h.enactedBy LEFT JOIN users au ON h.affectedType = 'user' && h.affectedID = au.userID LEFT JOIN characters c ON h.affectedType = 'character' && h.affectedID = c.characterID LEFT JOIN decks d ON h.affectedType = 'deck' && h.affectedID = d.deckID WHERE p.userID = {$userID} ORDER BY enactedOn DESC LIMIT 30");
	$gNotification = $gameHistories->fetch();
	if ($cNotification['enactedOn'] > $gNotification['enactedOn']) $lastDate = date('Ymd', strtotime($cNotification['enactedOn']));
	else $lastDate = date('Ymd', strtotime($gNotification['enactedOn']));

	for ($count = 0; $count < 30; $count++) {
		if ($cNotification['enactedOn'] > $gNotification['enactedOn']) {
			$action = $cNotification['action'];
			$timestamp = strtotime($cNotification['enactedOn']);
			if (date('Ymd', $timestamp) != $lastDate) {
				$lastDate = date('Ymd', $timestamp);
				echo "\t\t\t\t<hr>\n";
			}
			$systemInfo = $systems->getSystemInfo($cNotification['systemID']);
?>
				<div class="timestamp"><?=date('M j, Y H:i:s', $timestamp)?> - </div>
<?
			if ($action == 'created') {
?>
				<div class="text">You created a new <span class="system"><?=$systemInfo['fullName']?></span> character: <a href="/characters/<?=$systemInfo['shortName']?>/<?=$cNotification['characterID']?>/"><?=$cNotification['label']?></a></div>
<?
			} elseif ($action == 'editedChar') {
?>
				<div class="text"><?=$cNotification['enactedBy'] == $userID?'You':"<a href=\"/ucp/{$cNotification['userID']}/\" class=\"username\">{$cNotification['username']}</a>"?> edited your <span class="system"><?=$systemInfo['fullName']?></span> character: <a href="/characters/<?=$systemInfo['shortName']?>/<?=$cNotification['characterID']?>/"><?=$cNotification['label']?></a></div>
<?
			} elseif ($action == 'deleted') {
?>
				<div class="text">You deleted your <span class="system"><?=$systemInfo['fullName']?></span> character: <a href="/characters/<?=$systemInfo['shortName']?>/<?=$cNotification['characterID']?>/"><?=$cNotification['label']?></a></div>
<?
			}
			$cNotification = $charHistories->fetch();
		} else {
			$action = $gNotification['action'];
			$timestamp = strtotime($gNotification['enactedOn']);
			if (date('Ymd', $timestamp) != $lastDate) {
				$lastDate = date('Ymd', $timestamp);
				echo "\t\t\t\t<hr>\n";
			}
			$systemInfo = $systems->getSystemInfo($gNotification['systemID']);
?>
				<div class="timestamp"><?=date('M j, Y H:i:s', $timestamp)?> - </div>
<?
			$gNotification = $gameHistories->fetch();
		}
?>
			<div class="notification">
			</div>
<?	} ?>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>