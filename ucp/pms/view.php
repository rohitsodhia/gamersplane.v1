<?
	$loggedIn = checkLogin();
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	
	$pmID = intval($pathOptions[2]);
	
	$pmCheck = $mysql->query('SELECT pms.pmID, pms.recipientID, recipients.username recipientName, pms.senderID, senders.username senderName, pms.title, pms.message, pms.datestamp, pms.viewed FROM pms LEFT JOIN users AS recipients ON pms.recipientID = recipients.userID LEFT JOIN users AS senders ON pms.senderID = senders.userID WHERE recipientID = '.intval($_SESSION['userID'])." AND pmID = $pmID");
	if (!$pmCheck->rowCount()) { header('Location: '.SITEROOT.'/ucp/pms'); exit; }
	$pmInfo = $pmCheck->fetch();
	$pmInfo['datestamp'] = switchTimezone($_SESSION['timezone'], $pmInfo['datestamp'], $_SESSION['dst']);
	
	if ($pmInfo['viewed'] == 0) $mysql->query("UPDATE pms SET viewed = 1 WHERE pmID = $pmID");
	
	if ($_SESSION['errors']) {
		if (preg_match('/ucp/pms\/.*$/', $_SERVER['HTTP_REFERER'])) {
			$errors = $_SESSION['errors'];
			foreach ($_SESSION['errorVals'] as $key => $value) { $$key = $value; }
		}
		if (!preg_match('/ucp/pms\/.*$/', $_SERVER['HTTP_REFERER']) || time() > $_SESSION['errorTime']) {
			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Private Message</h1>
		
		<div id="buttonsDiv">
			<a href="<?=SITEROOT?>/ucp/pms/reply/<?=$pmID?>"><img src="<?=SITEROOT?>/images/buttons/pmReply.jpg"></a>
			<a href="<?=SITEROOT?>/ucp/pms/delete/<?=$pmID?>"><img src="<?=SITEROOT?>/images/buttons/pmDelete.jpg"></a>
		</div>
		
		<div class="tr">
			<div class="leftCol">Title</div>
			<div class="rightCol"><?=printReady($pmInfo['title'])?></div>
		</div>
		<div class="tr">
			<div class="leftCol">From</div>
			<div class="rightCol"><a href="<?=SITEROOT.'/ucp/'.$pmInfo['senderID']?>" class="username"><?=$pmInfo['senderName']?></a></div>
		</div>
		<div class="tr">
			<div class="leftCol">When</div>
			<div class="rightCol"><?=date('F j, Y g:i a', $pmInfo['datestamp'])?></div>
		</div>
		<div id="messageDiv" class="tr"><?=BBCode2Html(printReady($pmInfo['message']))?></div>
<? require_once(FILEROOT.'/footer.php'); ?>