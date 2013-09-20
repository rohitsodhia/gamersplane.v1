<?
	$loggedIn = checkLogin();
	
	$gameID = intval($pathOptions[0]);
	$userID = intval($_SESSION['userID']);
	$playerID = intval($pathOptions[2]);
	$pendingAction = $pathOptions[1] == 'approvePlayer'?'approve':'reject';
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = $userID");
	if ($gmCheck->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); exit; }
	
	$playerInfo = $mysql->query('SELECT u.username, g.title FROM users u, games g WHERE g.gameID = '.$gameID.' AND u.userID = '.$playerID);
	if ($playerInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); exit; }
	list($playerName, $title) = $playerInfo->fetch(PDO::FETCH_NUM);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=ucwords($pendingAction)?> Player</h1>
		
		<p class="alignCenter">Are you sure you want to <?=$pendingAction?> <a href="<?=SITEROOT?>/pms/send?userID=<?=$playerID?>" class="username" target="_parent"><?=$playerName?></a> <?=$pendingAction == 'approve'?'to join':'from'?> "<a href="<?=SITEROOT.'/games/'.$gameID?>" target="_parent"><?=$title?></a>"?</p>
		
		<form method="post" action="<?=SITEROOT?>/games/process/pendingPlayer/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="playerID" value="<?=$playerID?>">
			<input type="hidden" name="pendingAction" value="<?=$pendingAction?>">
			<button type="submit" name="submit" class="fancyButton"><?=ucwords($pendingAction)?></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>