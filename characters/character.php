<?php
	$characterID = intval($pathOptions[1]);
	$noChar = true;

	define('SYSTEM', $pathOptions[0]);
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT . "/includes/packages/" . SYSTEM . "Character.package.php");
		$charClass = Systems::systemClassName(SYSTEM) . 'Character';
		if ($character = new $charClass($characterID)) {
			$active = $character->load();
			if ($active) {
				$angular = $mongo->systems->findOne(['_id' => SYSTEM], ['angular' => true])['angular'];
				if ($angular) {
					$dispatchInfo['ngController'] = 'viewCharacter';
					$angular = 'viewCharacter_' . SYSTEM;
				} else {
					$dispatchInfo['ngController'] = null;
					$angular = null;
				}
				$dispatchInfo['title'] = $character->getLabel() . ' | ' . $dispatchInfo['title'];
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions) {
					$noChar = false;
					if ($charPermissions == 'library') {
						$mongo->characters->updateOne(['characterID' => $characterID], ['$inc' => ['library.views' => 1]]);
						$favorited = $mongo->characterLibraryFavorites->findOne(['userID' => $currentUser->userID, 'characterID' => $characterID]) ? true : false;
					}
					$addJSFiles[] = 'characters/_sheet.js';
					if (file_exists(FILEROOT . '/javascript/characters/' . SYSTEM . '/sheet.js')) {
						$addJSFiles[] = 'characters/' . SYSTEM . '/sheet.js';
					}
				}
			} else { header('Location: /characters/my/'); exit; }
		}
	} else { header('Location: /404/'); exit; }
?>
<?php	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
<?php	if (!$noChar) { ?>
		<div class="clearfix"><div id="sheetActions" class="trapezoid facingUp hbMargined floatRight">
<?php		if ($charPermissions == 'edit') { ?>
			<a id="editCharacter" href="/characters/<?=SYSTEM?>/<?=$characterID?>/edit/" class="sprite pencil"></a>
<?php		} else { ?>
			<a href="/" class="favoriteChar sprite tassel<?=$favorited?'':' off'?>" title="Favorite" alt="Favorite"></a>
<?php		} ?>
		</div></div>
<?php	} ?>
<?php	if (file_exists(FILEROOT . '/images/logos/' . SYSTEM . '.png')) { ?>
		<div id="charSheetLogo"><img src="/images/logos/<?=SYSTEM?>.png"></div>
<?php	} ?>

<?php	if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<?php	} else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

		<div id="charDetails"<?=$angular?" ng-controller=\"{$angular}\"":''?>>
<?php		$character->showSheet(); ?>
		</div>
<?php	} ?>
<?php	require_once(FILEROOT.'/footer.php'); ?>
