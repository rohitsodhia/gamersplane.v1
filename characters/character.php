<?
	$characterID = intval($pathOptions[1]);
	$noChar = true;

	define('SYSTEM', $pathOptions[0]);
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT."/includes/packages/".SYSTEM."Character.package.php");
		$charClass = Systems::systemClassName(SYSTEM).'Character';
		if ($character = new $charClass($characterID)) {
			$active = $character->load();
			if ($active) {
				$angular = $mysql->query("SELECT angular FROM systems WHERE shortName = '".SYSTEM."' LIMIT 1")->fetchColumn();
				if ($angular) 
					$dispatchInfo['ngController'] = 'viewCharacter_'.SYSTEM;
				$dispatchInfo['title'] = $character->getLabel().' | '.$dispatchInfo['title'];
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions) {
					$noChar = false;
					if ($charPermissions == 'library') {
						$mysql->query("UPDATE characterLibrary SET viewed = viewed + 1 WHERE characterID = $characterID");
						$mongo->characters->update(array('characterID' => $characterID), array('$inc' => array('library.views' => 1)));
						$favorited = $mysql->query("SELECT updateDate FROM characterLibrary_favorites WHERE userID = {$currentUser->userID} AND characterID = {$characterID}")->rowCount();
					}
					$addJSFiles[] = 'characters/_sheet.js';
					if (file_exists(FILEROOT.'/javascript/characters/'.SYSTEM.'/sheet.js')) 
						$addJSFiles[] = 'characters/'.SYSTEM.'/sheet.js';
				}
			} else { header('Location: /characters/my/'); exit; }
		}
	} else { header('Location: /404/'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
<?	if (!$noChar) { ?>
		<div class="clearfix"><div id="sheetActions" class="trapezoid facingUp hbMargined floatRight">
<?		if ($charPermissions == 'edit') { ?>
			<a id="editCharacter" href="/characters/<?=SYSTEM?>/<?=$characterID?>/edit/" class="sprite pencil"></a>
<?		} else { ?>
			<a href="/" class="favoriteChar sprite tassel<?=$favorited?'':' off'?>" title="Favorite" alt="Favorite"></a>
<?		} ?>
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