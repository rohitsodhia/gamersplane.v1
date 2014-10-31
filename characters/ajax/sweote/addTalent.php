<?
	if ($loggedIn) {
		require_once(FILEROOT.'/includes/packages/sweoteCharacter.package.php');
		sweoteCharacter::talentEditFormat($_POST['key']);
	}
?>