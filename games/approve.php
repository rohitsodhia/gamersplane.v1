<?
	$loggedIn = checkLogin();
	
	$gameID = intval($pathOptions[0]);
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT `primary` FROM gms WHERE gameID = $gameID AND userID = $userID");
	if ($gmCheck->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }
	
	$charInfo = $mysql->query('SELECT users.username, users.userID, games.gameID, games.title FROM characters INNER JOIN users ON characters.userID = users.userID INNER JOIN games ON characters.gameID = games.gameID WHERE characters.characterID = '.$characterID);
	if ($charInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }
	list($playerName, $playerID, $gameID, $title) = $charInfo->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Approve Player</h1>
		
		<p class="alignCenter">Are you sure you want to approve <a href="<?=SITEROOT?>/pms/send?userID=<?=$playerID?>" class="username"><?=$playerName?></a> to join "<a href="<?=SITEROOT.'/games/'.$gameID?>"><?=$title?></a>"?</p>
		
		<form method="post" action="<?=SITEROOT?>/games/process/approve/" class="buttonPanel">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<button type="submit" name="approve" class="btn_approve"></button>
			<button type="submit" name="cancel" class="btn_cancel"></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>