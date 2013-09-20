<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$playerID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE gameID = $gameID AND userID = $userID and isGM = 1");
	$playerCheck = $mysql->query("SELECT u.userID, u.username, p.isGM FROM users u, players p WHERE u.userID = p.userID AND p.gameID = $gameID AND p.userID = $playerID AND p.approved = 1");
	if ($gmCheck->rowCount() == 0 || $playerCheck->rowCount() == 0) { header('Location: '.SITEROOT.'/games/'.$gameID); exit; }

	$playerInfo = $playerCheck->fetch();
	$isGM = $playerInfo['isGM'];
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=$isGM?'Remove':'Add'?> as GM</h1>
		
<? if ($isGM) { ?>
		<p>Are you sure you want to remove <a href="<?=SITEROOT.'/user/'.$playerInfo['userID']?>" class="username"><?=$playerInfo['username']?></a> as a GM?</p>
		<p>Remember this person will have lose all GM rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being an admin for the game forum.</p>
<? } else { ?>
		<p>Would you like to make <a href="<?=SITEROOT.'/user/'.$playerInfo['userID']?>" class="username"><?=$playerInfo['username']?></a> a GM?</p>
		<p>Remember this person will have full rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being an admin for the game forum.</p>
<? } ?>
		<form method="post" action="<?=SITEROOT?>/games/process/toggleGM" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<button type="submit" name="toggle" class="fancyButton"><?=$isGM?'Remove':'Add'?></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>