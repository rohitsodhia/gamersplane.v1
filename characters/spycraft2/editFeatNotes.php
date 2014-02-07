<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$featID = intval($pathOptions[3]);
	if (allowCharEdit($characterID, $userID)) {
		$featInfo = $mysql->query("SELECT featsList.name, spycraft2_feats.notes FROM spycraft2_feats INNER JOIN featsList USING (featID) WHERE spycraft2_feats.featID = $featID AND spycraft2_feats.characterID = $characterID");
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
		<form method="post" action="<?=SITEROOT?>/characters/process/spycraft2/editFeatNotes/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<input id="featID" type="hidden" name="featID" value="<?=$featID?>">
			<textarea id="notes" name="notes"><?=$featInfo['notes']?></textarea>
			<div id="submitDiv" class="alignCenter">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>