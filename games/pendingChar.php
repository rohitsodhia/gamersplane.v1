<?
	$gameID = intval($pathOptions[0]);
	$characterID = intval($pathOptions[2]);
	$pendingAction = $pathOptions[1] == 'approveChar'?'approve':'remove';
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = {$currentUser->userID}");
	$charInfo = $mysql->query("SELECT c.label, c.userID, u.username, g.title, g.charsPerPlayer, g.system FROM characters c, users u, games g WHERE c.userID = u.userID AND c.characterID = {$characterID} AND g.gameID = {$gameID}");
	if ($gmCheck->rowCount() == 0 && $charInfo->rowCount() == 0) { header('Location: /403'); exit; }
	list($label, $playerID, $playerName, $title, $charsPerPlayer, $shortName) = $charInfo->fetch(PDO::FETCH_NUM);
	$numApprovedChars = $mysql->query("SELECT COUNT(characterID) FROM characters WHERE userID = $playerID AND gameID = $gameID AND approved = 1");
	$numApprovedChars = $numApprovedChars->fetchColumn();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=ucwords($pendingAction)?> Character</h1>
		
		<p class="hbMargined">Are you sure you want to <?=$pendingAction?> <a href="/pms/send?userID=<?=$playerID?>" class="username"><?=$playerName?></a>'s character "<a href="/characters/<?=$shortName?>/<?=$characterID?>"><?=$label?></a>" <?=$pendingAction == 'approve'?'to':'from'?> "<a href="<?='/games/'.$gameID?>"><?=$title?></a>"?</p>
		
		<form method="post" action="/games/process/pendingChar/" class="alignCenter">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<input type="hidden" name="pendingAction" value="<?=$pendingAction?>">
			<button type="submit" name="submit" class="fancyButton"><?=ucwords($pendingAction)?></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>