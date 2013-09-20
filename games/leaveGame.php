<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$userInfo = $mysql->query('SELECT p.approved, g.title FROM players p, games g WHERE p.userID = '.$userID.' AND p.gameID = g.gameID AND g.gameID = '.$gameID);
	if ($userInfo ->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }

	list($approved, $title) = $userInfo->fetch(PDO::FETCH_NUM);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Leave Game</h1>
		
		<p>Are you sure you want to leave "<a href="<?=SITEROOT.'/games/'.$gameID?>"><?=$title?></a>"?</p>
<? if ($approved) { ?>
		
		<p>If you have any characters currently in this game (approved or not), they will be removed.</p>
<? } ?>
		
		<form method="post" action="<?=SITEROOT?>/games/process/leaveGame/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$userID?>">
			<button type="submit" name="leave" class="fancyButton">Leave</button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>