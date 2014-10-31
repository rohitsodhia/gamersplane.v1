<?
	$gameID = intval($pathOptions[0]);
	
	$gmCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = $gameID AND userID = {$currentUser->userID} AND isGM = 1");
	if ($gmCheck->rowCount() == 0) { header('Location: /games/list'); exit; }
//	$gameInfo = $mysql->fetch();
	
	$players = $mysql->query('SELECT u.userID, u.username, c.characterID, c.label FROM users u, characters c WHERE u.userID = c.userID AND c.gameID = '.$gameID.' AND c.approved = 1');
	$allPlayers = array();
	foreach ($players as $userInfo) $allPlayers[$userInfo['userID']] = $userInfo;
	
	$decks = $mysql->query('SELECT deckID, label, type, deck, position FROM decks WHERE gameID = '.$gameID);
	$decks = $decks->fetchAll();
	$temp = array();
	foreach ($decks as $key => $value) $temp[$value['deckID']] = $value;
	$decks = $temp;
	
	$deckTypes = array();
	foreach ($mysql->query('SELECT short, name FROM deckTypes') as $deckType) $deckTypes[$deckType['short']] = $deckType['name'];
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Game Decks</h1>
		
<?
	if (isset($_GET['success']) && in_array($_GET['success'], array('delete', 'create', 'edit', 'shuffle'))) echo "\t\t<div class=\"alertBox_success\">Your deck was successfully {$_GET['success']}".(substr($_GET['success'], -1) != 'e'?'e':'')."d</div>\n";
	if (isset($_GET['new'])) {
?>
		
		<hr>
<?
	} elseif (isset($_GET['shuffle'])) {
		$deckID = intval($_GET['shuffle']);
		if (isset($decks[$deckID])) {
?>
		<div class="alertBox_error">
			You don't own the deck you're trying to edit.
		</div>
<?
		} else {
			$deckInfo = $decks[$deckID];
			$totalNumCards = sizeof(explode('~', $deckInfo['deck']));
			$numCardsLeft = $totalNumCards - $deckInfo['position'] + 1;
			$lastShuffleAgo = time() - strtotime($deckInfo['lastShuffle']);
?>
		<form method="post" action="/games/process/deck">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			<p class="alignCenter">There are <b><?=$numCardsLeft?></b> cards still left in this deck<? if ($deckInfo['lastShuffle'] != '0000-00-00 00:00:00') echo ' and it was last shuffled on <b>'.date('F j, Y g:i:s a', strtotime($deckInfo['lastShuffle'])).'</b>'.(($lastShuffleAgo < 3600)?', '.intval(date('i', $lastShuffleAgo)).' minutes ago':''); ?>.</p>
			<p class="alignCenter">Are you sure you want to shuffle <b><?=$deckInfo['label']?></b>?</p>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="shuffle" class="btn_shuffleDeck"></button></div>
		</form>
		
		<hr>
<?
		}
	} elseif (isset($_GET['edit'])) {
		$deckID = intval($_GET['edit']);
		if (isset($decks[$deckID])) {
?>
		<div class="alertBox_error">
			You don't own the deck you're trying to edit.
		</div>
<?
		} else {
			$deckInfo = $decks[$deckID];
?>
		<form method="post" action="/games/process/deck">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			<div class="tr">
				<label class="textLabel">Deck Label</label>
				<div class="inputCol"><input id="deckLabel" type="text" name="deckLabel" value="<?=printReady($deckInfo['label'])?>" maxlength="100"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Deck Type</label>
				<div class="inputCol">
<? foreach ($deckTypes as $short => $full) echo "\t\t\t\t\t".'<p><input type="radio" name="deckType" value="'.$short.'"'.(($deckInfo['type'] == $short)?' checked="checked"':'').'>'.$full."</p>\n"; ?>
				</div>
				<p class="inputCol">Changing the deck type will reset the deck</p>
			</div>
			<div class="tr">
				<div class="title">&nbsp;</div>
				<div class="checkCol">Allow Access</div>
			</div>
<?
			$accessCheck = $mysql->query('SELECT users.userID, users.username, characters.characterID, characters.label, deckPermissions.access FROM users, characters LEFT JOIN (SELECT 1 as access, userID FROM deckPermissions WHERE deckPermissions.deckID = '.$deckID.') as deckPermissions USING (userID) WHERE characters.gameID = '.$gameID.' AND characters.userID = users.userID');
			foreach ($accessCheck as $userInfo) {
				echo "\t\t\t<div class=\"tr user\">\n";
				echo "\t\t\t\t<label class=\"textLabel\">".$userInfo['label'].' ('.$userInfo['username'].")</label>\n";
				echo "\t\t\t\t".'<div class="checkCol"><input type="checkbox" name="addUser_'.$userInfo['userID'].'"'.(($userInfo['access'] == 1)?' checked="checked"':'')."></div>\n";
				echo "\t\t\t</div>\n";
			}
?>
			<div id="submitDiv" class="inputCol"><button type="submit" name="submit" class="btn_submit"></button></div>
		</form>
		
		<hr>
<?
		}
	} elseif (isset($_GET['delete'])) {
		$deckID = intval($_GET['delete']);
		if (isset($decks[$deckID])) {
?>
		<div class="alertBox_error">
			You don't own the deck you're trying to delete.
		</div>
<?
		} else {
?>
		<form method="post" action="/games/process/deck">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			
			<p class="alignCenter">Are you sure you want to delete the deck labeled <b><?=$decks[$deckID]['label']?></b>?</p>
			<div class="alignCenter"><button type="submit" name="delete" class="btn_delete"></button></div>
		</form>
		<hr>
<?
		}
	}
?>
		
		<p>You currently have <b><?=sizeof($decks).'</b> deck'.((sizeof($decks) != 1)?'s':'')?> created for this game.</p>
		<p>Remember that you need to make sure you give your players permission to add draws to forum posts via the Forum Admin Panel.</p>
<?
	if (sizeof($decks)) {
?>
		<div id="deckList">
			<div id="headers" class="tr">
				<div class="deckLabel">Label</div>
				<div class="deckType">Type</div>
				<div class="deckRemaining">Cards Remaining</div>
				<div class="deckActions">Actions</div>
			</div>
<?
		foreach ($decks as $deckInfo) {
			$cardsRemaining = sizeof(explode('~', $deckInfo['deck'])) - $deckInfo['position'] + 1;
?>
			<div class="tr">
				<div class="deckLabel"><?=$deckInfo['label']?></div>
				<div class="deckType"><?=$deckTypes[$deckInfo['type']]?></div>
				<div class="deckRemaining"><?=$cardsRemaining?></div>
				<div class="deckActions">
					<a href="?edit=<?=$deckInfo['deckID']?>">Edit Deck</a>
					<a href="?shuffle=<?=$deckInfo['deckID']?>">Shuffle Deck</a>
					<a href="?delete=<?=$deckInfo['deckID']?>">Delete Deck</a>
				</div>
			</div>
<?
		}
		echo "\t\t</div>\n";
	}
?>
		<p><a href="?new=1">Create New Deck</a></p>
<? require_once(FILEROOT.'/footer.php'); ?>