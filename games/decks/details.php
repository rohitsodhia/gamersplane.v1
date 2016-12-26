<?php
	$gameID = intval($pathOptions[0]);
	$game = $mongo->games->findOne(['gameID' => $gameID], ['projection' => ['gm' => true, 'players' => true, 'decks' => true]]);
	$gmCheck = false;
	foreach ($game['players'] as $player) {
		if ($player['user']['userID'] == $currentUser->userID) {
			if ($player['isGM']) {
				$gmCheck = true;
			}
			break;
		}
	}
	if (!$gmCheck) { header('Location: /tools/decks/'); exit; }

	$action = $pathOptions[3];
	require_once('includes/DeckTypes.class.php');
	$deckTypes = DeckTypes::getInstance()->getAll();
	$permissions = [];
	if ($action == 'edit') {
		$deck = [];
		$deckID = intval($pathOptions[2]);
		foreach ($game['decks'] as $iDeck) {
			if ($iDeck['deckID'] == $deckID) {
				$deck = $iDeck;
				break;
			}
		}
		foreach ($deck['permissions'] as $user) {
			$permissions[] = $user['userID'];
		}
	}
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
		echo "\t\t\t\t\t" . '<p><input type="radio" name="deckType" value="' . $deckType['_id'] . '"' . ($deck['type'] == $deckType['_id'] ? ' checked="checked"' : '') . '>' . $deckType['name'] . "</p>\n";
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
<?php	foreach ($game['players'] as $player) { ?>
				<div class="tr user">
					<input type="checkbox" name="addUser[<?=$player['user']['userID']?>]"<?=(in_array($player['user']['userID'], $permissions) || $player['user']['userID'] == $game['gm']['userID'] ? ' checked="checked"' : '') . ($player['user']['userID'] == $game['gm']['userID'] ? ' data-disabled="disabled"' : '')?>>
					<label><?=$player['user']['username']?></label>
				</div>
<?php	} ?>
			</div>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="<?=$action == 'edit' ? 'edit' : 'create'?>" class="fancyButton"><?=$action == 'edit' ? 'Edit' : 'Create'?></button></div>
		</form>
<?php require_once(FILEROOT . '/footer.php'); ?>
