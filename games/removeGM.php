<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$gmID = intval($pathOptions[2]);
	
	$mainGMCheck = $mysql->query("SELECT `primary` FROM gms WHERE gameID = $gameID AND userID = $userID AND `primary` = 1");
	if ($mainGMCheck->rowCount() == 0) { header('Location: /games/'.$gameID); }
	
	$gmInfo = $mysql->query("SELECT users.userID, users.username FROM users, gms WHERE users.userID = $gmID AND users.userID = gms.userID AND gms.gameID = $gameID");
	if ($gmInfo->rowCount() == 0) { header('Location: /games/'.$gameID); }
	$gmInfo = $gmInfo->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Remove Player as GM</h1>
		
		<p class="alignCenter">Are you sure you want to remove <a href="/pms/send?userID=<?=$gmInfo['userID']?>" class="username"><?=$gmInfo['username']?></a> as a GM?</p>
		
		<form method="post" action="/games/process/removeGM" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="gmID" value="<?=$gmID?>">
			<button type="submit" name="remove" class="btn_remove"></button>
<!--			<button type="submit" name="cancel" class="btn_cancel"></button>-->
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>