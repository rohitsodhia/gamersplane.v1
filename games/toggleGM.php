<?php
	$gameID = intval($pathOptions[0]);
	$playerID = intval($pathOptions[2]);
	$getGame = $mysql->query("SELECT users.userID, users.username, playerCheck.isGM FROM games INNER JOIN players gmCheck ON games.gameID = gmCheck.gameID INNER JOIN players playerCheck ON gamers.gameID = playerCheck.gameID INNER JOIN users ON playerCheck.userID = users.userID WHERE games.gameID = {$gameID} AND gmCheck.userID = {$currentUser->userID} AND gmCheck.isGM = 1 AND playerCheck.userID = {$playerID} LIMIT 1");
	if (!$getGame->rowCount()) { header('Location: /games/'.$gameID); exit; }
	$game = $getGame->fetch();
?>
<?php require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar"><?=$game['isGM'] ? 'Remove' : 'Add'?> as GM</h1>

<?php if ($game['isGM']) { ?>
		<p>Are you sure you want to remove <a href="<?='/user/' . $game['userID']?>" class="username"><?=$game['username']?></a> as a GM?</p>
		<p>Remember this person will have lose all GM rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being an admin for the game forum.</p>
<?php } else { ?>
		<p>Would you like to make <a href="<?='/user/' . $game['userID']?>" class="username"><?=$game['username']?></a> a GM?</p>
		<p>Remember this person will have full rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being an admin for the game forum.</p>
<?php } ?>
		<form method="post" action="/games/process/toggleGM/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<button type="submit" name="toggle" class="fancyButton"><?=$isGM ? 'Remove' : 'Add'?></button>
		</form>
<?php require_once(FILEROOT . '/footer.php'); ?>
