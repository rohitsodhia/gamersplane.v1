<?
	$currentUser->checkACP('users');

	$userID = intval($_POST['userID']);
	if (!$userID) return false;
	if (isset($_POST['suspend'])) {
		$suspendUntilTS = strtotime("{$_POST['year']}-{$_POST['month']}-{$_POST['day']} {$_POST['hour']}:{$_POST['minutes']}");
		if ($suspendUntilTS > time()) {
			$suspendUntil = date('Y-m-d H:i:s', $suspendUntilTS);
			$mysql->query("UPDATE users SET suspendedUntil = '{$suspendUntil}' WHERE userID = {$userID}");
			if (isset($_POST['ajax'])) echo 'suspended';
			else header('Location: /acp/users/');
		}
	}
?>