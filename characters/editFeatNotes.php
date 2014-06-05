<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$featID = intval($pathOptions[3]);
	$noChar = TRUE;

	define('SYSTEM', $pathOptions[0]);
	if ($systems->getSystemID(SYSTEM)) {
		require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
		$charClass = SYSTEM.'Character';
		$dispatchInfo['title'] = $systems->getFullName(SYSTEM).' Edit Feat Notes';
		if ($character = new $charClass($characterID)) {
			$charPermissions = $character->checkPermissions($userID);
			if ($charPermissions == 'edit') {
				$featInfo = $mysql->query("SELECT fl.name, f.notes FROM ".SYSTEM."_feats f INNER JOIN featsList fl USING (featID) WHERE f.featID = $featID AND f.characterID = $characterID");
				if ($featInfo->rowCount()) $featInfo = $featInfo->fetch();

				$noChar = FALSE;
			}
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></h1>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } elseif (!isset($featInfo)) { ?>
		<h2 id="noFeat">This character does not have this feat/ability.</h2>
<? } else { ?>
		<form id="featDescForm" method="post" action="/characters/process/editFeatNotes/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<input id="system" type="hidden" name="system" value="<?=SYSTEM?>">
			<input id="featID" type="hidden" name="featID" value="<?=$featID?>">
			<textarea id="notes" name="notes" class="hbMargined"><?=$featInfo['notes']?></textarea>
			<div id="submitDiv" class="alignCenter">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>