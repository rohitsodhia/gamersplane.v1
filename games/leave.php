<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[2]);
	$userInfo = $mysql->query('SELECT users.username, users.userID, characters.approved, games.gameID, games.gmID, games.title FROM characters, users, games WHERE characters.userID = users.userID AND characters.gameID = games.gameID AND characters.characterID = '.$characterID);
	if ($userInfo ->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }

	list($username, $playerID, $approved, $gameID, $gmID, $title) = $userInfo->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1><?=($approved)?'Leave Game':'Withdraw Character'?></h1>
		
		<p class="alignCenter">Are you sure you want to <?=($approved)?'leave':'withdraw your character from'?> "<a href="<?=SITEROOT.'/games/'.$gameID?>"><?=$title?></a>"?</p>
		
		<form method="post" action="<?=SITEROOT?>/games/process/leave/" class="buttonPanel">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<button type="submit" name="leave" class="btn_leave"></button>
			<button type="submit" name="cancel" class="btn_cancel"></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>