<?
	$loggedIn = checkLogin();
	
	$gameID = intval($pathOptions[0]);
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[2]);
	$pendingAction = $pathOptions[1] == 'approveChar'?'approve':'remove';
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = $userID");
	$charInfo = $mysql->query('SELECT c.label, c.userID, u.username, g.title, s.shortName FROM characters c, users u, games g, systems s WHERE c.userID = u.userID AND g.systemID = s.systemID AND c.characterID = '.$characterID.' AND g.gameID = '.$gameID);
	if ($gmCheck->rowCount() == 0 && $charInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); exit; }
	list($label, $playerID, $playerName, $title, $shortName) = $charInfo->fetch(PDO::FETCH_NUM);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=ucwords($pendingAction)?> Character</h1>
		
		<p class="alignCenter">Are you sure you want to <?=$pendingAction?> <a href="<?=SITEROOT?>/pms/send?userID=<?=$playerID?>" class="username"><?=$playerName?></a>'s character "<a href="<?=SITEROOT?>/characters/<?=$shortName?>/<?=$characterID?>"><?=$label?></a>" <?=$pendingAction == 'approve'?'to':'from'?> "<a href="<?=SITEROOT.'/games/'.$gameID?>"><?=$title?></a>"?</p>
		
		<form method="post" action="<?=SITEROOT?>/games/process/pendingChar/" class="buttonPanel">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<input type="hidden" name="pendingAction" value="<?=$pendingAction?>">
			<button type="submit" name="submit" class="fancyButton"><?=ucwords($pendingAction)?></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>