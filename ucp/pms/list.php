<?
	$loggedIn = checkLogin();
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
		<h1>Private Messages</h1>
		
		<div id="controls" class="clearfix">
			<a href="<?=SITEROOT?>/ucp/pms/send"><img src="<?=SITEROOT?>/images/buttons/newPM.jpg" class="floatRight"></a>
			<a href="<?=SITEROOT?>/ucp/pms/">Inbox</a>
			<a href="<?=SITEROOT?>/ucp/pms/?outbox=1">Outbox</a>
		</div>
		
		<table>
			<tr>
				<th class="delCol">Delete</th>
				<th class="titleCol">Title</th>
				<th class="fromCol">From</th>
				<th class="whenCol">When</th>
			</tr>
<?
	$pms = $mysql->query('SELECT pms.pmID, pms.recipientID, recipients.username recipientName, pms.senderID, senders.username senderName, pms.title, pms.datestamp, pms.viewed FROM pms LEFT JOIN users AS recipients ON pms.recipientID = recipients.userID LEFT JOIN users AS senders ON pms.senderID = senders.userID WHERE '.($_GET['outbox'] == 1?'senderID':'recipientID').' = '.intval($_SESSION['userID']).' ORDER BY datestamp DESC');
	
	if ($pms->rowCount()) { foreach ($pms as $pmInfo) {
		$pmInfo['datestamp'] = switchTimezone($_SESSION['timezone'], $pmInfo['datestamp'], $_SESSION['dst']);
?>
				<tr>
				<td class="delCol"><?=$_GET['outbox'] == 1?'':'<a href="'.SITEROOT.'/ucp/pms/delete/'.$pmInfo['pmID'].'"><img src="'.SITEROOT.'/images/buttons/pmDelete.jpg"></a>'?></td>
				<td class="titleCol"><a href="<?=SITEROOT?>/ucp/pms/view/<?=$pmInfo['pmID']?>"><?=(!$pmInfo['viewed']?'<b>':'').printReady($pmInfo['title']).(!$pmInfo['viewed']?'</b>':'')?></a></td>
				<td class="fromCol"><a href="<?=SITEROOT.'/ucp/'.$pmInfo['senderID']?>" class="username"><?=$pmInfo['senderName']?></a></td>
				<td class="whenCol"><?=date('F j, Y<\b\r>g:i a', $pmInfo['datestamp'])?></td>
			</tr>
<? } } else { ?>
			<tr><td id="noPMs" colspan="4">Doesn't seem like anyone has contacted you yet...</td></tr>
<? } ?>
		</table>
<? require_once(FILEROOT.'/footer.php'); ?>