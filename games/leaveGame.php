<?php
	$gameID = intval($pathOptions[0]);
	$getGame = $mysql->query("SELECT games.title, players.approved FROM games INNER JOIN players ON games.gameID = players.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} LIMIT 1");
	if (!$getGame->rowCount()) { header('Location: /403'); exit; }
	$game = $getGame->fetch();
?>
<?php	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Leave Game</h1>
		<div class="hbMargined">
			<p>Are you sure you want to leave "<a href="<?='/games/' . $gameID?>"><?=$game['title']?></a>"?</p>
<?php	if ($game['approved']) { ?>
			<p>If you have any characters currently in this game (approved or not), they will be removed.</p>
<?php	} ?>
			<form method="post" action="/games/process/leaveGame/" class="alignCenter">
				<input type="hidden" name="gameID" value="<?=$gameID?>">
				<input type="hidden" name="playerID" value="<?=$currentUser->userID?>">
				<button type="submit" name="leave" class="fancyButton">Leave</button>
			</form>
		</div>
<?php	require_once(FILEROOT.'/footer.php'); ?>
