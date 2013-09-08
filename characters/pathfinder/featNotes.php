<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$featID = intval($pathOptions[3]);
	$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
	if ($charCheck->rowCount()) {
		$featInfo = $mysql->query("SELECT featsList.name, pathfinder_feats.notes FROM pathfinder_feats INNER JOIN featsList USING (featID) WHERE pathfinder_feats.featID = $featID AND pathfinder_feats.characterID = $characterID");
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
		<div id="notes"><?=printReady($featInfo['notes'])?></div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>