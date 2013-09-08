<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT custom.*, characters.label, characters.userID, gms.gameID IS NOT NULL isGM FROM custom_characters custom INNER JOIN characters ON custom.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE custom.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2>Custom</h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/custom/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div class="tr">
				<label id="charLabel" class="leftCol">Character Label</label>
				<div class="rightCol"><?=printReady($charInfo['label'])?></div>
			</div>
			
			<div class="tr">
				<div class="leftCol">Character Sheet</div>
				<div class="rightCol"><textarea name="charSheet"><?=printReady($charInfo['charSheet'], array('stripslashes'))?></textarea></div>
			</div>
			
			<div class="tr alignCenter"><button type="submit" name="save" class="btn_save"></button></div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>