<?
	$loggedIn = checkLogin();
	
	$gameID = intval($pathOptions[1]);
	$gameInfo = $mysql->query('SELECT title, open FROM games WHERE gameID = '.$gameID.' AND gmID = '.intval($_SESSION['userID']));
	if ($gameInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }
	$gameInfo = $gameInfo->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1><?=$gameInfo['open']?'Close':'Open'?> Game</h1>
		
		<p class="alignCenter">Are you sure you want to <?=$gameInfo['open']?'close':'open'?> <a href="<?=SITEROOT.'/games/'.$gameID?>"><?=$gameInfo['title']?></a>?</p>
		
		<form method="post" action="<?=SITEROOT?>/games/process/changeStatus/" class="cbf_basic buttonPanel">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<button type="submit" name="<?=$gameInfo['open']?'close':'open'?>" class="btn_<?=$gameInfo['open']?'close':'open'?>"></button>
<!--			<button type="submit" name="cancel" class="btn_cancel"></button>-->
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>