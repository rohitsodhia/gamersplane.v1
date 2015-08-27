<?
	DEFINE(SYSTEM, $pathOptions[1]);
	$characterID = intval($pathOptions[2]);
	$charPermissions = false;
	if ($systems->verifySystem(SYSTEM)) {
		require_once(FILEROOT."/includes/packages/".SYSTEM."Character.package.php");
		$charClass = Systems::systemClassName(SYSTEM).'Character';
		if ($character = new $charClass($characterID)) {
			$character->load();
			$dispatchInfo['title'] = 'Edit '.$character->getLabel().' Avatar | '.$dispatchInfo['title'];
			$charPermissions = $character->checkPermissions($currentUser->userID);
		}
	}

	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Character Avatar</h1>

		<form method="post" action="/characters/process/avatar/" enctype="multipart/form-data" class="hbMargined">
<?	if (!$charPermissions) { ?>
			<p>Seems like you're trying to change a character that isn't yours!</p>
<?	} ?>
<?	if ($character->getAvatar()) { ?>
			<img id="avatar" src="<?=$character->getAvatar()?>">
<?	} else { ?>
			<div id="avatar"<?=$character->getAvatar()?'':' class="noAvatar"'?>>
				<p>No Avatar</p>
			</div>
<?	} ?>
<?	if ($character->getAvatar()) { ?>
			<p id="delete" class="alignCenter"><input id="deleteAvatar" type="checkbox" name="delete"> <label for="deleteAvatar">Delete avatar<label></p>
<?	} ?>
			<p class="alignCenter"><input type="file" name="avatar"></p>
			<input type="hidden" name="system" value="<?=SYSTEM?>">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<p class="alignCenter"><button type="submit" name="submit" class="fancyButton">Upload</button></p>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>