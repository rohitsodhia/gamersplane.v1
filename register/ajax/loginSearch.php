<?
	$mysql->setTable('users');
	if (isset($_POST['username'])) { $mysql->setWhere('LOWER(username) = "'.strtolower(sanatizeString($_POST['username'])).'"'); }
	elseif (isset($_POST['email'])) { $mysql->setWhere('LOWER(email) = "'.strtolower(sanatizeString($_POST['email'])).'"'); }
	
	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" ?>\n\n";
	if (sizeof($_POST) > 0) {
		if ($_POST['username'] && sanatizeString($_POST['username']) != filterString(sanatizeString($_POST['username']))) { echo '<error>Dirty</error>'; }
		else {
			$mysql->stdQuery('select', 'where');
			
			echo "<users>\n";
			while ($info = $mysql->fetch()) {
				echo "<user id=\"{$info['userID']}\">\n";
				echo "<username>{$info['username']}</username>\n";
				echo "<email>{$info['email']}</email>\n";
				echo "</user>\n";
			}
			echo "</users>";
		}
	} else { echo '<error>Failed</error>'; }
?>