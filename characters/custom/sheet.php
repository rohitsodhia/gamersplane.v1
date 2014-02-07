<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'custom');
	if ($charInfo) {
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
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/custom/<?=$characterID?>/edit" class="fancyButton">Edit Character</a></div>
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