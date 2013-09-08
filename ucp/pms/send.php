<?
	$loggedIn = checkLogin();
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	
	if ($pathOptions[1] == 'reply') {
		$pmID = intval($pathOptions[2]);
		
		$pmCheck = $mysql->query('SELECT pms.pmID, pms.recipientID, recipients.username recipientName, pms.senderID, senders.username senderName, pms.title, pms.message FROM pms LEFT JOIN users AS recipients ON pms.recipientID = recipients.userID LEFT JOIN users AS senders ON pms.senderID = senders.userID WHERE recipientID = '.intval($_SESSION['userID']).' AND pmID = '.$pmID);
		
		if (!$pmCheck->rowCount()) { header('Location: '.SITEROOT.'/ucp/pms'); exit; }
		
		$pmInfo = $pmCheck->fetch();
	} elseif ($_GET['userID']) {
		$userCheck = $mysql->query('SELECT username senderName FROM users WHERE userID = '.intval($_GET['userID']));
		
		$pmInfo = $userCheck->fetch();
	}
	
	if ($_SESSION['errors']) {
		if (preg_match('/ucp/pms\/.*$/', $_SESSION['lastURL'])) {
			$errors = $_SESSION['errors'];
			foreach ($_SESSION['errorVals'] as $key => $value) { $$key = $value; }
			
			$pmInfo = array('senderName' => $username, 'title' => $title, 'message' => $message);
		}
		if (!preg_match('/ucp/pms\/.*$/', $_SESSION['lastURL']) || time() > $_SESSION['errorTime']) {
			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Send Private Message</h1>
		
<?
	if ($errors) {
		echo "\t\t<div class=\"alertBox_error\"><ul>\n";
		if ($errors['invalidUser']) { echo "\t\t\t<li>No users match the one you're trying to contact.</li>\n"; }
		if ($errors['noTitle']) { echo "\t\t\t<li>Enter a title if you want to send a message!</li>\n"; }
		if ($errors['noMessage']) { echo "\t\t\t<li>Enter a message if you want to send one!</li>\n"; }
		echo "\t\t</ul></div>\n";
	}
?>
		
		<form method="post" action="<?=SITEROOT?>/ucp/process/pms/<?=($pathOptions[1] == 'reply')?'reply':'send'?>">
			<input id="pmID" type="hidden" name="pmID" value="<?=$pmID?>">
			<div class="tr">
				<label class="textLabel">Username:</label>
				<input id="username" type="text" name="username" maxlength="24" value="<?=$pmInfo['senderName']?>">
			</div>
			<div class="tr">
				<label class="textLabel">Title:</label>
				<input id="title" type="text" name="title" maxlength="100" value="<?=(($pathOptions[1] == 'reply' && substr($pmInfo['title'], 0, 4) != 'Re: ')?'Re: ':'').$pmInfo['title']?>">
			</div>
			<textarea id="messageTextArea" name="message"></textarea>
			
			<div id="submitDiv" class="alignCenter"><button type="submit" name="post" class="btn_send"></button></div>
		</form>
<? if ($pathOptions[1] == 'reply') { ?>
		
		<hr>
		<div class="tr">
			<b>From:</b> <?=$pmInfo['senderName']?>
		</div>
		<div class="tr">
			<b>Message:</b>
			<div><?=BBCode2Html(printReady($pmInfo['message']))?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>