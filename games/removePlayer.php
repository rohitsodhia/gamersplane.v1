<?php
	$gameID = intval($pathOptions[0]);
	$playerID = intval($pathOptions[2]);
	$game = $mongo->games->findOne(
		['gameID' => $gameID],
		['projection' => ['title' => true, 'players' => true]]
	);
	$gmCheck = false;
	$player = null;
	foreach ($game['players'] as $rPlayer) {
		if ($rPlayer['user']['userID'] == $playerID) {
			$player = $rPlayer;
		} elseif ($rPlayer['user']['userID'] == $currentUser->userID && $rPlayer['isGM']) {
			$gmCheck = true;
		}
	}
	if (!$gmCheck || !$player) { header('Location: /games/'.$gameID); exit; }
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Remove Player from Game</h1>

		<p class="hbMargined">Are you sure you want to remove <a href="/user/<?=$playerID?>" class="username" target="_parent"><?=$player['user']['username']?></a> from "<a href="<?='/games/' . $gameID?>" target="_parent"><?=$game['title']?></a>"?</p>

		<form method="post" action="/games/process/removePlayer/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<button type="submit" name="remove" class="fancyButton">Remove</button>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
