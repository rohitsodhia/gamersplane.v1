<?php
	$gameID = intval($pathOptions[0]);
	$playerID = intval($pathOptions[2]);
	$pendingAction = $pathOptions[1] == 'approvePlayer' ? 'approve' : 'reject';
	$getGame = $mysql->query("SELECT games.title FROM games INNER JOIN players ON games.gameID = players.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} AND players.isGM = 1 LIMIT 1");
	if (!$getGame->rowCount()) { header('Location: /403'); exit; }

	$getPlayer = $mysql->query("SELECT username FROM users WHERE userID = {$playerID} LIMIT 1");
	if ($getPlayer->rowCount() == 0) { header('Location: /403'); exit; }
	$playerName = $getPlayer->fetchColumn();
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar"><?=ucwords($pendingAction)?> Player</h1>

		<p class="alignCenter">Are you sure you want to <?=$pendingAction?> <a href="/pms/send?userID=<?=$playerID?>" class="username" target="_parent"><?=$playerName?></a> <?=$pendingAction == 'approve' ? 'to join' : 'from'?> "<a href="<?='/games/' . $gameID?>" target="_parent"><?=$game['title']?></a>"?</p>

		<form method="post" action="/games/process/pendingPlayer/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<input type="hidden" name="pendingAction" value="<?=$pendingAction?>">
			<button type="submit" name="submit" class="fancyButton"><?=ucwords($pendingAction)?></button>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
