<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	
	$pmID = intval($pathOptions[1]);
	$userID = intval($_SESSION['userID']);
	
	$pmCheck = $mysql->query('SELECT pms.pmID, pms.recipientID, recipients.username recipientName, pms.senderID, senders.username senderName, pms.title, pms.message, pms.datestamp, pms.viewed FROM pms LEFT JOIN users AS recipients ON pms.recipientID = recipients.userID LEFT JOIN users AS senders ON pms.senderID = senders.userID WHERE (recipientID = '.$userID." OR senderID = $userID) AND pmID = $pmID");
	if (!$pmCheck->rowCount()) { header('Location: /pms/'); exit; }
	$pmInfo = $pmCheck->fetch();
	$pmInfo['datestamp'] = switchTimezone($_SESSION['timezone'], $pmInfo['datestamp'], $_SESSION['dst']);
	
	if ($pmInfo['viewed'] == 0 && $pmInfo['senderID'] != $userID) $mysql->query("UPDATE pms SET viewed = 1 WHERE pmID = $pmID");
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Private Message</h1>
		
		<div id="buttonsDiv">
			<a href="/pms/reply/<?=$pmID?>" class="fancyButton">Reply</a>
			<a href="/pms/delete/<?=$pmID?>" class="fancyButton deletePM">Delete</a>
		</div>
		
		<div class="tr">
			<div class="leftCol">Title</div>
			<div class="rightCol"><?=printReady($pmInfo['title'])?></div>
		</div>
		<div class="tr">
			<div class="leftCol">From</div>
			<div class="rightCol"><a href="<?='/'.$pmInfo['senderID']?>" class="username"><?=$pmInfo['senderName']?></a></div>
		</div>
		<div class="tr">
			<div class="leftCol">When</div>
			<div class="rightCol"><?=date('F j, Y g:i a', $pmInfo['datestamp'])?></div>
		</div>
		<div id="messageDiv" class="tr"><?=BBCode2Html(printReady($pmInfo['message']))?></div>
<? require_once(FILEROOT.'/footer.php'); ?>