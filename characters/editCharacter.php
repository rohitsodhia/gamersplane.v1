<?
	$characterID = intval($pathOptions[1]);
	$noChar = true;

	define('SYSTEM', $pathOptions[0]);
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT."/includes/packages/".SYSTEM."Character.package.php");
		$charClass = $systems->systemClassName(SYSTEM).'Character';
		$dispatchInfo['title'] = 'Edit '.$systems->getFullName(SYSTEM).' Character Sheet';
		if ($character = new $charClass($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($currentUser->userID);
			if ($charPermissions == 'edit') {
				$noChar = false;
				$addJSFiles[] = 'characters/_edit.js';
				if (is_subclass_of($character, 'd20Character')) $addJSFiles[] = 'characters/_d20Character.js';
				if (file_exists(FILEROOT.'/javascript/characters/'.SYSTEM.'/edit.js')) $addJSFiles[] = 'characters/'.SYSTEM.'/edit.js';
			}
		}
	} else { header('Location: /404/'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
<?	if (file_exists(FILEROOT.'/images/logos/'.SYSTEM.'.png')) { ?>
		<div id="charSheetLogo"><img src="/images/logos/<?=SYSTEM?>.png"></div>
<?	} ?>
		
<?	if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<?	} else { ?>
		<form method="post" action="/characters/process/editCharacter/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<input id="system" type="hidden" name="system" value="<?=$character::SYSTEM?>">
			
			<div id="charDetails">
				<div id="charAvatar"><a href="/characters/avatar/<?=SYSTEM?>/<?=$characterID?>/">Change Avatar</a> (Avatar Set: <div class="sprite <?=$character->getAvatar($characterID)?'check green':'cross'?> small"></div>)</div>
<?		$character->showEdit(); ?>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<?	} ?>
<?	require_once(FILEROOT.'/footer.php'); ?>