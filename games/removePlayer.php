<?php
	$gameID = intval($pathOptions[0]);
	$playerID = intval($pathOptions[2]);
	$getGame = $mysql->query("SELECT games.title, users.username FROM games INNER JOIN players gmCheck ON games.gameID = gmCheck.gameID INNER JOIN players playerCheck ON gamers.gameID = playerCheck.gameID INNER JOIN users ON playerCheck.userID = users.userID WHERE games.gameID = {$gameID} AND gmCheck.userID = {$currentUser->userID} AND gmCheck.isGM = 1 AND playerCheck.userID = {$playerID} LIMIT 1");
	if (!$getGame->rowCount()) { header('Location: /games/'.$gameID); exit; }
	$game = $getGame->fetch();
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Remove Player from Game</h1>

		<p class="hbMargined">Are you sure you want to remove <a href="/user/<?=$playerID?>" class="username" target="_parent"><?=$game['username']?></a> from "<a href="<?='/games/' . $gameID?>" target="_parent"><?=$game['title']?></a>"?</p>

		<form method="post" action="/games/process/removePlayer/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<button type="submit" name="remove" class="fancyButton">Remove</button>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
