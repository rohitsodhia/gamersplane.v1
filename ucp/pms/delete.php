<?
	$loggedIn = checkLogin();
	
	$pmID = intval($pathOptions[1]);
	
	$recipientCheck = $mysql->query('SELECT recipientID FROM pms WHERE recipientID = '.intval($_SESSION['userID']).' AND pmID = '.$pmID);
	$recipientID = $recipientCheck->fetchColumn();
	
	if (!$recipientID) { header('Location: '.SITEROOT.'/403'); }
?>
<? require_once(FILEROOT.'/header.php'); ?>
			<h1>Delete Message</h1>
			
			<p class="alignCenter">
				Are you sure you wanna delete this PM?
			</p>
			<form method="post" action="<?=SITEROOT?>/process/ucp/pms/delete" class="alignCenter">
				<input type="hidden" name="pmID" value="<?=$pmID?>">
				<button type="submit" name="delete" class="btn_delete"></button>
				<button type="submit" name="cancel" class="btn_cancel"></button>
			</form>
<? require_once(FILEROOT.'/footer.php'); ?>