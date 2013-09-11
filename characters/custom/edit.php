<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.label, c.userID, gms.primaryGM IS NOT NULL isGM FROM custom_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<h2 class="headerbar hbDark">Custom</h2>
		
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
			
			<div class="tr alignCenter"><button type="submit" name="save" class="fancyButton">Save</button></div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>