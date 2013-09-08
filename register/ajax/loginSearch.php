<?
	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" ?>\n\n";
	if (sizeof($_POST) > 0) {
		if ($_POST['username'] && sanitizeString($_POST['username']) != filterString(sanitizeString($_POST['username']))) echo '<error>Dirty</error>';
		else {
			$values = array();
			$query = 'SELECT userID, username, email FROM users WHERE ';
			if (isset($_POST['username'])) {
				$query .= 'LOWER(username) = ?';
				$values[] = sanitizeString($_POST['username'], '+lower');
			} elseif (isset($_POST['email'])) {
				$query .= 'LOWER(email) = ?';
				$values[] .= sanatizeString($_POST['email'], '+lower');
			}
			
			$userInfo = $mysql->prepare($query);
			$userInfo->execute($values);
			
			echo "<users>\n";
			foreach ($userInfo as $info) {
				echo "<user id=\"{$info['userID']}\">\n";
				echo "<username>{$info['username']}</username>\n";
				echo "<email>{$info['email']}</email>\n";
				echo "</user>\n";
			}
			echo "</users>";
		}
	} else echo '<error>Failed</error>';
?>