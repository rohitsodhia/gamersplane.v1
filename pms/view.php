<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('pms');
	
	$pmID = intval($pathOptions[1]);
	
	try {
		$pmManager = new PMManager($pmID);
	} catch (Exception $e) { header('Location: /pms/'); exit; }
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Private Message</h1>
		
		<div id="buttonsDiv">
			<a href="/pms/reply/<?=$pmManager->getPMID()?>/" class="fancyButton">Reply</a>
			<a href="/pms/delete/<?=$pmManager->getPMID()?>/" class="fancyButton deletePM">Delete</a>
		</div>
		
		<div class="tr">
			<div class="leftCol">Title</div>
			<div class="rightCol"><?=$pmManager->pm->getTitle(true)?></div>
		</div>
		<div class="tr">
			<div class="leftCol">From</div>
			<div class="rightCol"><a href="/user/<?=$pmManager->pm->getSender('userID')?>/" class="username"><?=$pmManager->pm->getSender('username')?></a></div>
		</div>
		<div class="tr">
			<div class="leftCol">When</div>
			<div class="rightCol convertTZ"><?=$pmManager->pm->getDatestamp('F j, Y g:i a')?></div>
		</div>
		<div id="messageDiv" class="tr"><?=BBCode2Html($pmManager->pm->getMessage(true))?></div>
<?	if (sizeof($pmManager->history)) { ?>

		<div id="history">
<?
		$first = true;
		foreach ($pmManager->history as $pm) {
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
<?	} ?>
<? require_once(FILEROOT.'/footer.php'); ?>