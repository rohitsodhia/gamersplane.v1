<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT c.*, s.shortName, s.fullName, u.username FROM characterLibrary_favorites f, characters c, systems s, users u WHERE c.systemID = s.systemID AND c.userID = u.userID AND f.userID = $userID AND c.characterID = $characterID AND f.characterID = c.characterID");
	if ($charInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }
	$charInfo = $charInfo->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Unfavorite Character</h1>
		
		<p class="alignCenter">Are you sure you want to unfavortite <a href="<?=SITEROOT?>/characters/<?=$charInfo['shortName']?>/<?=$characterID?>" target="_parent"><?=$charInfo['label']?></a>?</p>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/favorite/" class="alignCenter">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<button type="submit" name="unfavorite" class="fancyButton">Unfavorite</button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>