<?
	$gameID = intval($pathOptions[0]);
	$playerID = intval($pathOptions[2]);
	$game = $mongo->games->findOne(array('gameID' => $gameID), array('forumID' => true, 'players' => true));
	$gmCheck = false;
	$player = null;
	foreach ($game['players'] as $player) {
		if ($player['user']['userID'] == $playerID) 
			$player = $player;
		elseif ($player['user']['userID'] == $currentUser->userID && $player['isGM']) 
			$gmCheck = true;
	}
	if (!$gmCheck || $player == null) { header('Location: /games/'.$gameID); exit; }

	$isGM = $player['isGM'];
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=$isGM?'Remove':'Add'?> as GM</h1>
		
<? if ($isGM) { ?>
		<p>Are you sure you want to remove <a href="<?='/user/'.$player['user']['userID']?>" class="username"><?=$player['player']['username']?></a> as a GM?</p>
		<p>Remember this person will have lose all GM rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being an admin for the game forum.</p>
<? } else { ?>
		<p>Would you like to make <a href="<?='/user/'.$player['user']['userID']?>" class="username"><?=$player['user']['username']?></a> a GM?</p>
		<p>Remember this person will have full rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being an admin for the game forum.</p>
<? } ?>
		<form method="post" action="/games/process/toggleGM/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<button type="submit" name="toggle" class="fancyButton"><?=$isGM?'Remove':'Add'?></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>