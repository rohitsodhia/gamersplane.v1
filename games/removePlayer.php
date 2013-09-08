<?
	$loggedIn = checkLogin();
	
	$gameID = intval($pathOptions[0]);
	$userID = intval($_SESSION['userID']);
	$playerID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE gameID = $gameID AND userID = $userID and isGM = 1");
	$playerCheck = $mysql->query("SELECT u.userID, u.username, g.title, p.isGM FROM users u, games g, players p WHERE g.gameID = $gameID AND u.userID = p.userID AND p.gameID = $gameID AND p.userID = $playerID AND p.approved = 1");
	if ($gmCheck->rowCount() == 0 || $playerCheck->rowCount() == 0) { header('Location: '.SITEROOT.'/games/'.$gameID); exit; }

	list($playerID, $playerName, $title, $isGM) = $playerCheck->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Remove Player from Game</h1>
		
		<p>Are you sure you want to remove <a href="<?=SITEROOT?>/user/<?=$playerID?>" class="username"><?=$playerName?></a> from "<a href="<?=SITEROOT.'/games/'.$gameID?>"><?=$title?></a>"?</p>
		
		<form method="post" action="<?=SITEROOT?>/games/process/remove/" class="buttonPanel">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<div class="fancyButton"><button type="submit" name="remove">Remove</button></div>
<!--			<button type="submit" name="cancel" class="btn_cancel"></button>-->
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>