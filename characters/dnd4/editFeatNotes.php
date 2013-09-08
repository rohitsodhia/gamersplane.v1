<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$featID = intval($pathOptions[3]);
	$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
	if ($charCheck->rowCount()) {
		$featInfo = $mysql->query("SELECT featsList.name, dnd4_feats.notes FROM dnd4_feats INNER JOIN featsList USING (featID) WHERE dnd4_feats.featID = $featID AND dnd4_feats.characterID = $characterID");
		if ($featInfo->rowCount()) $featInfo = $featInfo->fetch();
	} else $noChar = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></h1>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } elseif (!isset($featInfo)) { ?>
		<h2 id="noFeat">This character does not have this feat/ability.</h2>
<? } else { ?>
		<form id="featDescForm" method="post" action="<?=SITEROOT?>/characters/process/dnd4/editFeatNotes/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<input id="featID" type="hidden" name="featID" value="<?=$featID?>">
			<textarea id="notes" name="notes"><?=$featInfo['notes']?></textarea>
			<div id="submitDiv" class="buttonPanel">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>