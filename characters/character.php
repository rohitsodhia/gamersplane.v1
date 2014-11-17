<?
	$characterID = intval($pathOptions[1]);
	$noChar = true;

	define('SYSTEM', $pathOptions[0]);
	if ($systems->getSystemID(SYSTEM)) {
		require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
		$charClass = SYSTEM.'Character';
		$dispatchInfo['title'] = $systems->getFullName(SYSTEM).' Character Sheet';
		if ($character = new $charClass($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($currentUser->userID);
			if ($charPermissions) {
				$noChar = false;
				if ($charPermissions == 'library') $mysql->query("UPDATE characterLibrary SET viewed = viewed + 1 WHERE characterID = $characterID");
				$addJSFiles[] = 'characters/_sheet.js';
				if (file_exists(FILEROOT.'/javascript/characters/'.SYSTEM.'/sheet.js')) $addJSFiles[] = 'characters/'.SYSTEM.'/sheet.js';
			}
		}
	} else { header('Location: /404/'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
<?	if (!$noChar) { ?>
		<div class="clearfix"><div id="sheetActions" class="wingDiv hbMargined floatRight">
			<div>
<?		if ($charPermissions == 'edit') { ?>
				<a id="editCharacter" href="/characters/<?=SYSTEM?>/<?=$characterID?>/edit" class="sprite pencil"></a>
<?		} else { ?>
				<a href="/" class="favoriteChar sprite tassel off" title="Favorite" alt="Favorite"></a>
<?		} ?>
			</div>
			<div class="wing ulWing"></div>
			<div class="wing urWing"></div>
		</div></div>
<?	} ?>
<?	if (file_exists(FILEROOT.'/images/logos/'.SYSTEM.'.png')) { ?>
		<div id="charSheetLogo"><img src="/images/logos/<?=SYSTEM?>.png"></div>
<?	} ?>
		
<?	if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<?	} else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

		<div id="charDetails">
<?		$character->showSheet(); ?>
		</div>
<?	} ?>
<?	require_once(FILEROOT.'/footer.php'); ?>