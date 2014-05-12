<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;

	define('SYSTEM', $pathOptions[0]);
	if ($systems->getSystemID(SYSTEM)) {
		require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
		$charClass = SYSTEM.'Character';
		$dispatchInfo['title'] = 'Edit '.$systems->getFullName(SYSTEM).' Character Sheet';
		if ($character = new $charClass($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions();
			if ($charPermissions == 'edit') {
				$noChar = FALSE;
				if ($charPermissions == 'library') $mysql->query("UPDATE characterLibrary SET viewed = viewed + 1 WHERE characterID = $characterID");
				$addJSFiles[] = 'characters/_edit.js';
				if (file_exists(FILEROOT.'/javascript/characters/'.SYSTEM.'/edit.js')) $addJSFiles[] = 'characters/'.SYSTEM.'/edit.js';

			}
		}
	} else { header('Location: '.SITEROOT.'/404/'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
<?	if (file_exists(SITEROOT.'/images/logos/'.SYSTEM.'.png')) { ?>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/<?=SYSTEM?>.png"></div>
<?	} ?>
		
<?	if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<?	} else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/editCharacter/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<input id="system" type="hidden" name="system" value="<?=$character::SYSTEM?>">
			
			<div id="charDetails">
<?		$character->showEdit(); ?>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<?	} ?>
<?	require_once(FILEROOT.'/footer.php'); ?>