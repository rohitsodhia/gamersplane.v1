<?
	$loggedIn = checkLogin();
	
	$gameID = intval($pathOptions[0]);
	$userID = intval($_SESSION['userID']);
	$playerID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE gameID = $gameID AND userID = $userID and isGM = 1");
	$playerCheck = $mysql->query("SELECT u.userID, u.username, g.title, p.isGM FROM users u, games g, players p WHERE g.gameID = $gameID AND u.userID = p.userID AND p.gameID = g.gameID AND p.userID = $playerID AND p.primaryGM IS NULL AND p.approved = 1");
	if ($gmCheck->rowCount() == 0 || $playerCheck->rowCount() == 0) { header('Location: '.SITEROOT.'/games/'.$gameID); exit; }

	list($playerID, $playerName, $title, $isGM) = $playerCheck->fetch(PDO::FETCH_NUM);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Remove Player from Game</h1>
		
		<p>Are you sure you want to remove <a href="<?=SITEROOT?>/user/<?=$playerID?>" class="username" target="_parent"><?=$playerName?></a> from "<a href="<?=SITEROOT.'/games/'.$gameID?>" target="_parent"><?=$title?></a>"?</p>
		
		<form method="post" action="<?=SITEROOT?>/games/process/removePlayer/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<button type="submit" name="remove" class="fancyButton">Remove</button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>