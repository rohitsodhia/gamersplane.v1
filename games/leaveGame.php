<?
	$gameID = intval($pathOptions[0]);
	$userInfo = $mysql->query('SELECT p.approved, g.title FROM players p, games g WHERE p.userID = '.$currentUser->userID.' AND p.gameID = g.gameID AND g.gameID = '.$gameID);
	if ($userInfo ->rowCount() == 0) { header('Location: /403'); exit; }

	list($approved, $title) = $userInfo->fetch(PDO::FETCH_NUM);
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Leave Game</h1>
		<div class="hbMargined">
			<p>Are you sure you want to leave "<a href="<?='/games/'.$gameID?>"><?=$title?></a>"?</p>
<?	if ($approved) { ?>
			<p>If you have any characters currently in this game (approved or not), they will be removed.</p>
<?	} ?>
			<form method="post" action="/games/process/leaveGame/" class="alignCenter">
				<input type="hidden" name="gameID" value="<?=$gameID?>">
				<input type="hidden" name="playerID" value="<?=$currentUser->userID?>">
				<button type="submit" name="leave" class="fancyButton">Leave</button>
			</form>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>