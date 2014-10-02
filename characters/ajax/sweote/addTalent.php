<?
	if (checkLogin(0)) {
		require_once(FILEROOT.'/includes/packages/sweoteCharacter.package.php');
		sweoteCharacter::talentEditFormat($_POST['key']);
	}
?>