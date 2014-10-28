<?
	$box = $pathOptions[0] == 'outbox'?'outbox':'inbox';
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if ($_GET['deleteSuc'] || $_GET['sent']) { ?>
		<div class="alertBox_success">
<?
	if ($_GET['deleteSuc']) { echo "\t\t\tPM successfully deleted.\n"; }
	if ($_GET['sent']) { echo "\t\t\tPM successfully sent.\n"; }
?>
		</div>
<? } ?>
		<h1 class="headerbar">Private Messages - <?=ucwords($box)?></h1>
		
		<div id="controlsContainer" class="clearfix">
			<a id="newPM" href="/pms/send" class="fancyButton">New PM</a>
			<div id="controls" class="wingDiv sectionControls" data-ratio=".8">
				<div class="wingDivContent clearfix">
					<a href="/pms/" class="borderBox<?=$box == 'inbox'?' current':''?>">Inbox</a>
					<a href="/pms/outbox" class="borderBox<?=$box == 'outbox'?' current':''?>">Outbox</a>
				</div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</div>
		</div>
		<div id="pms">
			<div class="tr headerTR headerbar hbDark">
				<div class="delCol"></div>
				<div class="titleCol">Title</div>
				<div class="fromCol">From</div>
				<div class="whenCol">When</div>
			</div>
<?
	$pms = $mysql->query('SELECT pms.pmID, pms.recipientID, recipients.username recipientName, pms.senderID, senders.username senderName, pms.title, pms.datestamp, pms.viewed FROM pms LEFT JOIN users AS recipients ON pms.recipientID = recipients.userID LEFT JOIN users AS senders ON pms.senderID = senders.userID WHERE '.($box == 'outbox'?'senderID':'recipientID').' = '.intval($_SESSION['userID']).' ORDER BY datestamp DESC');
	
	if ($pms->rowCount()) {
		$count = 0;
		foreach ($pms as $pmInfo) {
			$count++;
?>
			<div id="pm_<?=$pmInfo['pmID']?>" class="pm tr<?=$pms->rowCount() == $count?' lastTR':''?><?=$pmInfo['viewed']?'':' new'?>">
				<div class="delCol"><?=$box == 'outbox' && $pmInfo['viewed']?'':'<a href="/pms/delete/'.$pmInfo['pmID'].'" class="deletePM sprite cross"></a>'?></div>
				<div class="titleCol"><a href="/pms/view/<?=$pmInfo['pmID']?>"><?=(!$pmInfo['viewed']?'<b>':'').printReady($pmInfo['title']).(!$pmInfo['viewed']?'</b>':'')?></a></div>
				<div class="fromCol"><a href="<?='/'.$pmInfo['senderID']?>" class="username"><?=$pmInfo['senderName']?></a></div>
				<div class="whenCol convertTZ"><?=date('F j, Y<\b\r>g:i a', strtotime($pmInfo['datestamp']))?></div>
			</div>
<?
		}
	}
?>
			<div id="noPMs" class="<?=$pms->rowCount()?'hideDiv':''?>">Doesn't seem like <?=$box == 'inbox'?'anyone has contacted you':'you have contacted anyone'?> yet...</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>