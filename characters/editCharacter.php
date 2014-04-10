<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;

	define('SYSTEM', $pathOptions[0]);
	if ($systems->getSystemID(SYSTEM)) {
		includeSystemInfo(SYSTEM);
		$charClass = SYSTEM.'Character';
		$dispatchInfo['title'] = $systems->getFullName(SYSTEM).' Character Sheet';
		if ($character = new $charClass($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions();
			if ($charPermissions) {
				$noChar = FALSE;
				if ($charPermissions == 'library') $mysql->query("UPDATE characterLibrary SET viewed = viewed + 1 WHERE characterID = $characterID");
			}
		}
	} else { header('Location: '.SITEROOT.'/404/'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/<?=SYSTEM?>.png"></div>
		
<?	if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<?	} else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">


<?
		$character->showEdit();
	}
?>
<?	require_once(FILEROOT.'/footer.php'); ?>