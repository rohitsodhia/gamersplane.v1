<?
	$gameID = intval($pathOptions[0]);
	$lockCheck = $mysql->query('SELECT locked FROM chat_sessions WHERE locked = 0 AND gameID = '.$gameID);
	$validChat = $gameID && $lockCheck->rowCount()?TRUE:FALSE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Chat</h1>
		
		<div class="alertBox_error jsHide">
			Your browser is blocking Javascript.<br>
			This page will not work.
		</div>
		
<?
	if ($validChat) {
		$mysql->query("INSERT INTO chat_users (userID, gameID, lastActive) VALUES ({$currentUser->userID}, $gameID, '".date('Y-m-d H:i:s')."')");
		$chatID = $mysql->query('SELECT MAX(chatID) AS maxID FROM chat_messages WHERE gameID = '.$gameID);
		$chatID = $chatID->fetchColumn();
		$_SESSION['chatLastPull'] = $chatID?$chatID:0;
?>
		<input id="gameID" type="hidden" name="gameID" value="<?=$gameID?>">
		<div id="chatDiv">
			<p id="notice">This chat system works on a slight delay, so if you don't see your message immediately, don't worry and give it a second.</p>
			<div id="users">
			</div>
			<div id="chatArea">
			</div>
			<div id="message"><input type="text" autocomplete="off" tabindex="1"></div>
			<button id="send" type="submit" value="Send" class="btn_send" tabindex="2"></button>
		</div>
<? } else { ?>
		<div class="alertBox_error">
			<p>Invalid chatroom</p>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>