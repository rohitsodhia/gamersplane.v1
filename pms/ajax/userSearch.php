<?
	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" ?>\n\n";
	if (sizeof($_POST) > 0) {
		$userInfo = $mysql->query('SELECT userID, username, email FROM users WHERE LOWER(username) = "'.strtolower(sanitizeString($_POST['username'])).'" AND userID != '.intval($_SESSION['userID']));
		
		echo "<users>\n";
		foreach ($userInfo as $info) {
			echo "<user id=\"{$info['userID']}\">\n";
			echo "<username>{$info['username']}</username>\n";
			echo "<email>{$info['email']}</email>\n";
			echo "</user>\n";
		}
		echo "</users>";
	} else echo '<error>Failed</error>';
?>