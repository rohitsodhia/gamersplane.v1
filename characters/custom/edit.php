<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'custom');
	if ($charInfo) {
		if (allowCharView($characterID, $userID)) $noChar = FALSE;
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