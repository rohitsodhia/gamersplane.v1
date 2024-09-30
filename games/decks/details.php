<?php
	$gameID = intval($pathOptions[0]);
	$deckID = intval($pathOptions[2]);
	if (sizeof($pathOptions) == 3 && $pathOptions[2] == 'new') {
		$action = $pathOptions[2];
	} else {
		$action = $pathOptions[3];

		$getDeck = $mysql->query("SELECT decks.label, decks.type FROM games INNER JOIN players ON games.gameID = players.gameID INNER JOIN decks ON games.gameID = decks.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} AND players.isGM = 1 AND decks.deckID = {$deckID} LIMIT 1");
		if (!$getDeck->rowCount()) { header('Location: /tools/decks/'); exit; }

		$deck = $getDeck->fetch();
	}

	require_once('includes/DeckTypes.class.php');
	$deckTypes = DeckTypes::getInstance()->getAll();
	$getPlayers = $mysql->query("SELECT users.userID, users.username, players.isGM, IF(deckPermissions.userID, 1, 0) hasPermission FROM players INNER JOIN users ON players.userID = users.userID LEFT JOIN deckPermissions ON players.userID = deckPermissions.userID AND deckPermissions.deckID = {$deckID} WHERE players.gameID = {$gameID}");
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar"><?=ucwords($action)?> Deck</h1>

		<form method="post" action="/games/process/decks/details/" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
<?php	if ($action == 'edit') {?>
			<input type="hidden" name="deckID" value="<?=$deckID?>">
<?php	} ?>
			<div class="tr clearfix">
				<label class="textLabel">Deck Label</label>
				<div class="inputCol"><input id="deckLabel" type="text" name="deckLabel" value="<?=$deck['label']?>" maxlength="50"></div>
			</div>
			<div class="tr clearfix">
				<div>
					<label class="textLabel">Deck Type</label>
					<div class="notice">Changing deck types will shuffle the deck.</div>
				</div>
				<div class="inputCol">
<?php
	foreach ($deckTypes as $deckType) {
		echo "\t\t\t\t\t" . '<p><input type="radio" name="deckType" value="' . $deckType['id'] . '"' . ($deck['type'] == $deckType['id'] ? ' checked="checked"' : '') . '>' . $deckType['name'] . "</p>\n";
	}
?>
				</div>
			</div>
			<div id="allowAccess">Allow Access</div>
			<div>
				<a id="checkAll" href="">[ Check All ]</a>
				<a id="uncheckAll" href="">[ Uncheck All ]</a>
			</div>
			<div id="users" class="clearfix">
<?php	foreach ($getPlayers->fetchAll() as $player) { ?>
				<div class="tr user">
					<input type="checkbox" name="addUser[<?=$player['userID']?>]"<?=($player['hasPermission'] || $player['isGM'] ? ' checked="checked"' : '') . ($player['isGM'] ? ' data-disabled="disabled"' : '')?>>
					<label><?=$player['username']?></label>
				</div>
<?php	} ?>
			</div>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="<?=$action == 'edit' ? 'edit' : 'create'?>" class="fancyButton"><?=$action == 'edit' ? 'Edit' : 'Create'?></button></div>
		</form>
<?php require_once(FILEROOT . '/footer.php'); ?>
