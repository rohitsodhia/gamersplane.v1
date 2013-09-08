<?
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" ?>';
	
	$gameID = intval($_POST['gameID']);
	
	$mysql->query('UPDATE chat_users SET lastActive = "'.date('Y-m-d H:i:s').'" WHERE userID = '.intval($_SESSION['userID']).' AND gameID = '.$gameID);
	$users = $mysql->query('SELECT users.userID, users.username FROM chat_users, users WHERE chat_users.userID = users.userID AND chat_users.lastActive >= NOW() - INTERVAL 5 SECOND AND gameID = '.$gameID);
	echo '<users>';
	if ($users->getResult()) { foreach ($users as $userInfo) {
			echo '<user userID="'.$userInfo['userID'].'">'.$userInfo['username'].'</user>';
	} } else echo '<error>1</error>';
	echo '</users>';
?>