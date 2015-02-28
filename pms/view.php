<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	addPackage('pms');
	
	$pmID = intval($pathOptions[1]);
	
	$pmManager = new PMManager($pmID);
	if (!$pmCheck->rowCount()) { header('Location: /pms/'); exit; }
	$pmInfo = $pmCheck->fetch();
	
	if ($pmInfo['viewed'] == 0 && $pmInfo['senderID'] != $currentUser->userID) $mysql->query("UPDATE pms SET viewed = 1 WHERE pmID = $pmID");
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Private Message</h1>
		
		<div id="buttonsDiv">
			<a href="/pms/reply/<?=$pmID?>/" class="fancyButton">Reply</a>
			<a href="/pms/delete/<?=$pmID?>/" class="fancyButton deletePM">Delete</a>
		</div>
		
		<div class="tr">
			<div class="leftCol">Title</div>
			<div class="rightCol"><?=printReady($pmInfo['title'])?></div>
		</div>
		<div class="tr">
			<div class="leftCol">From</div>
			<div class="rightCol"><a href="/user/<?=$pmInfo['senderID']?>/" class="username"><?=$pmInfo['senderName']?></a></div>
		</div>
		<div class="tr">
			<div class="leftCol">When</div>
			<div class="rightCol convertTZ"><?=date('F j, Y g:i a', strtotime($pmInfo['datestamp']))?></div>
		</div>
		<div id="messageDiv" class="tr"><?=BBCode2Html(printReady($pmInfo['message']))?></div>
<? require_once(FILEROOT.'/footer.php'); ?>