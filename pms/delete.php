<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	
	$pmID = intval($pathOptions[1]);
	
	$recipientCheck = $mysql->query('SELECT recipientID, senderID FROM pms WHERE (recipientID = '.$userID.' OR senderID = '.$userID.') AND pmID = '.$pmID);
	$recipientID = $recipientCheck->fetchColumn();

	if (!$recipientID) { header('Location: '.SITEROOT.'/403'); exit; }
?>
<? require_once(FILEROOT.'/header.php'); ?>
			<h1 class="headerbar">Delete Message</h1>
			
			<p class="alignCenter">
				Are you sure you wanna delete this PM?
			</p>
			<form method="post" action="<?=SITEROOT?>/pms/process/delete" class="alignCenter">
				<input id="pmID" type="hidden" name="pmID" value="<?=$pmID?>">
				<button type="submit" name="delete" class="fancyButton">Delete</button>
				<button type="submit" name="cancel" class="fancyButton">Cancel</button>
			</form>
<? require_once(FILEROOT.'/footer.php'); ?>