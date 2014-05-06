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
				$addJSFiles[] = 'characters/_sheet.js';
			}
		}
	} else { header('Location: '.SITEROOT.'/404/'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
<?	if (!$noChar) { ?>
		<div class="clearfix"><div id="sheetActions" class="wingDiv hbMargined floatRight">
			<div>
<?		if ($charPermissions == 'edit') { ?>
				<a id="editCharacter" href="<?=SITEROOT?>/characters/<?=SYSTEM?>/<?=$characterID?>/edit" class="sprite pencil"></a>
<?		} else { ?>
				<a href="/" class="favoriteChar sprite tassel off" title="Favorite" alt="Favorite"></a>
<?		} ?>
			</div>
			<div class="wing ulWing"></div>
			<div class="wing urWing"></div>
		</div></div>
<?	} ?>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/<?=SYSTEM?>.png"></div>
		
<?	if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<?	} else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

<?
		$character->showSheet();
	}
?>
<?	require_once(FILEROOT.'/footer.php'); ?>