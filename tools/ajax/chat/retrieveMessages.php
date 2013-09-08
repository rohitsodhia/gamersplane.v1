<?
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" ?>';
	
	$gameID = intval($_POST['gameID']);
	$messages = $mysql->query('SELECT chat_messages.chatID, chat_messages.posterID, users.username, chat_messages.postedOn, chat_messages.message FROM chat_messages, users WHERE chat_messages.chatID > '.$_SESSION['chatLastPull'].' AND chat_messages.posterID = users.userID AND chat_messages.gameID = '.$gameID.' ORDER BY chatID');
	echo '<messages>';
	if ($messages->rowCount()) { foreach ($messages as $chatInfo) {
		echo '<message>';
		echo '<chatID>'.$chatInfo['chatID'].'</chatID>';
		echo '<poster>'.$chatInfo['username'].'</poster>';
		echo '<date>'.date('H:i:s', strtotime($chatInfo['postedOn'])).'</date>';
		echo '<text>'.printReady($chatInfo['message']).'</text>';
		echo '</message>';
		$_SESSION['chatLastPull'] = $chatInfo['chatID'];
	} } else echo '<error>1</error>';
	echo '</messages>';
?>