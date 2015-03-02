<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('pms');

	$pmManager = new PMManager();
	$reply = $pathOptions[0] == 'reply'?true:false;
	if ($reply) {
		$pmID = intval($pathOptions[1]);
		try { $replyManager = new PMManager($pmID); }
		catch (Exception $e) { header('Location: /pms/'); exit; }

		$recipients = $replyManager->pm->getRecipients();
		$allowed = false;
		if ($replyManager->pm->getSender('userID') == $currentUser->userID) 
			$allowed = true;
		else { foreach ($recipients as $recipient) {
			if ($recipient->userID == $currentUser->userID) {
				$allowed = true;
				break;
			}
		} }
		if (!$allowed) { header('Location: /pms/'); exit; }

		$title = $replyManager->pm->getTitle();
		if (substr($title, 0, 4) != 'Re: ') 
			$title = 'Re: '.$title;
		$pmManager->pm->setTitle($title);
		$pmManager->pm->addRecipient($replyManager->pm->getSender());
		$pmManager->pm->setReplyTo($pmID);
	} elseif ($_GET['userID']) {
		$recipient = $mysql->query('SELECT username FROM users WHERE userID = '.intval($_GET['userID']));
		if ($recipient->rowCount()) $recipient = $recipient->fetchColumn();

		$pmManager->pm->addRecipient(array('userID' => intval($_GET['userID']), 'username' => $recipient));
	}

	$errorVals = $formErrors->getErrors('pm');
	if ($errorVals) {
		$pmManager->pm->setTitle($errorVals['title']);
		$pmManager->pm->clearRecipients();
		$pmManager->pm->addRecipient((object) array('username' => $errorVals['username']));
		$pmManager->pm->setReplyTo($errorVals['replyTo']);
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=$reply?'Reply':'Send Private Message'?></h1>
		
<?	if ($formErrors->errorsExist()) { ?>
		<div class="alertBox_error"><ul>
<?
		if ($formErrors->checkError('invalidUser')) 
			echo "\t\t\t<li>No users match the one you're trying to contact.</li>\n";
		if ($formErrors->checkError('noTitle')) 
			echo "\t\t\t<li>Enter a title if you want to send a message!</li>\n";
		if ($formErrors->checkError('noMessage')) 
			echo "\t\t\t<li>Enter a message if you want to send one!</li>\n";
?>
		</ul></div>
<?	} ?>
		
		<form method="post" action="/pms/process/<?=($reply)?'reply':'send'?>/">
<?	if ($reply) { ?>
			<input id="replyTo" type="hidden" name="pmID" value="<?=$pmManager->pm->getReplyTo()?>">
<?	} ?>
			<div class="tr clearfix">
				<label class="textLabel">Username:</label>
<?
	$recipients = array();
	foreach ($pmManager->pm->getRecipients() as $recipient) 
		$recipients[] = $recipient->username;
?>
				<input id="username" type="text" name="username" maxlength="24" value="<?=implode(',', $recipients)?>">
				<div id="invalidUser" class="alert hideDiv">Invalid User</div>
			</div>
			<div class="tr">
				<label class="textLabel">Title:</label>
				<input id="title" type="text" name="title" maxlength="100" value="<?=$pmManager->pm->getTitle()?>">
			</div>
			<div id="titleRequired" class="tr alert hideDiv">Title required!</div>
			<textarea id="messageTextArea" name="message"></textarea>
			<div id="messageRequired" class="alert hideDiv">Message required!</div>
			
			<div id="submitDiv" class="alignCenter"><button type="submit" name="send" class="fancyButton">Send</button></div>
		</form>
<? if ($reply) { ?>
		
		<div id="history">
<?
		$first = true;
		foreach ($replyManager->history as $pm) {
?>
			<div class="historyPM<?=$first?' first':''?>">
<?			if ($pm->hasAccess()) { ?>
				<p class="title"><a href="/pms/view/<?=$pm->getPMID()?>/"><?=$pm->getTitle(true)?></a></p>
<?			} else { ?>
				<p class="title"><?=$pm->getTitle(true)?></p>
<?			} ?>
				<p class="user">from <a href="/user/<?=$pm->getSender('userID')?>/" class="username"><?=$pm->getSender('username')?></a> on <span class="convertTZ"><?=$pm->getDatestamp('F j, Y g:i a')?></span></p>
<?
			$recipients = array();
			foreach ($pm->getRecipients() as $recipient) 
				$recipients[] = "<a href=\"/user/{$recipient->userID}/\" class=\"username\">{$recipient->username}</a>";
?>
				<p class="user">to <?=implode(', ', $recipients)?></p>
				<div class="message">
<?=$pm->getMessage(true)?>
				</div>
			</div>
<?
			if ($first) $first = false;
		}
?>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>