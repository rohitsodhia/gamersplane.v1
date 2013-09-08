<?
	$loggedIn = checkLogin();
	
	$gameID = intval($pathOptions[1]);
	$gameInfo = $mysql->query('SELECT title, open FROM games WHERE gameID = '.$gameID.' AND gmID = '.intval($_SESSION['userID']));
	if ($gameInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }
	$gameInfo = $gameInfo->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=$gameInfo['open']?'Close':'Open'?> Game</h1>
		
		<p class="alignCenter">Are you sure you want to <?=$gameInfo['open']?'close':'open'?> <strong><?=$gameInfo['title']?></strong>?</p>
		
		<form method="post" action="<?=SITEROOT?>/games/process/changeStatus/" class="cbf_basic buttonPanel">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<div class="fancyButton"><button type="submit" name="<?=$gameInfo['open']?'close':'open'?>"><?=$gameInfo['open']?'Close':'Open'?></button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>