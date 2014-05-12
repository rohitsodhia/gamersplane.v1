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
		$dispatchInfo['title'] = $systems->getFullName(SYSTEM).' Feat Notes';
		if ($character = new $charClass($characterID)) {
			$charPermissions = $character->checkPermissions();
			if ($charPermissions) {
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
		<div id="notes" class="hbMargined"><?=strlen($featInfo['notes'])?printReady($featInfo['notes']):'No notes.'?></div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>