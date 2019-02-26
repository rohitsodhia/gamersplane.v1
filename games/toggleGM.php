<?php
	$gameID = intval($pathOptions[0]);
	$playerID = intval($pathOptions[2]);
	$game = $mongo->games->findOne(
		['gameID' => $gameID],
		['projection' => ['forumID' => true, 'players' => true]]
	);
	$gmCheck = false;
	$player = null;
	foreach ($game['players'] as $iPlayer) {
		if ($iPlayer['user']['userID'] == $playerID) {
			$player = $iPlayer;
		} elseif ($iPlayer['user']['userID'] == $currentUser->userID && $iPlayer['isGM']) {
			$gmCheck = true;
		}
	}
	if (!$gmCheck || $player == null) { header('Location: /games/'.$gameID); exit; }

	$isGM = $player['isGM'];
?>
<?php require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar"><?=$isGM ? 'Remove' : 'Add'?> as GM</h1>

<?php if ($isGM) { ?>
		<p>Are you sure you want to remove <a href="<?='/user/' . $player['user']['userID']?>" class="username"><?=$player['player']['username']?></a> as a GM?</p>
		<p>Remember this person will have lose all GM rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being an admin for the game forum.</p>
<?php } else { ?>
		<p>Would you like to make <a href="<?='/user/' . $player['user']['userID']?>" class="username"><?=$player['user']['username']?></a> a GM?</p>
		<p>Remember this person will have full rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being an admin for the game forum.</p>
<?php } ?>
		<form method="post" action="/games/process/toggleGM/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<button type="submit" name="toggle" class="fancyButton"><?=$isGM ? 'Remove' : 'Add'?></button>
		</form>
<?php require_once(FILEROOT . '/footer.php'); ?>
