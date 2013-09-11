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
		<h1 class="headerbar">Character Sheet</h1>
		<h2 class="headerbar hbDark">Custom</h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/custom/<?=$characterID?>/edit" class="button">Edit Character</a></div>
		<div class="tr">
			<label id="charLabel" class="leftCol">Character Label</label>
			<div class="rightCol"><?=printReady($charInfo['label'])?></div>
		</div>
		
		<div class="tr">
			<div id="charSheetLabel" class="leftCol">Character Sheet</div>
			<div class="rightCol"><?=printReady($charInfo['charSheet'])?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>