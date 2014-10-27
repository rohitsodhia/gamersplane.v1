<?
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = $userID");
	if (!$gmCheck->rowCount()) { header('Location: /tools/maps'); exit; }

	$action = $pathOptions[3];
	$deckDetails = array('label' => '', 'type' => 'pcwj');
	$deckPermissions = array();

	if ($action == 'edit') {
		$deckID = intval($pathOptions[2]);
		$deckDetails = $mysql->query("SELECT label, type FROM decks where deckID = $deckID");
		$deckDetails = $deckDetails->fetch();
		foreach ($mysql->query("SELECT userID FROM deckPermissions WHERE deckID = $deckID") as $permission) $deckPermissions[] = $permission['userID'];
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=ucwords($action)?> Deck</h1>
		
		<form method="post" action="/games/process/decks/details" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
<? if ($action == 'edit') {?>
			<input type="hidden" name="deckID" value="<?=$deckID?>">
<? } ?>
			<div class="tr clearfix">
				<label class="textLabel">Deck Label</label>
				<div class="inputCol"><input id="deckLabel" type="text" name="deckLabel" value="<?=$deckDetails['label']?>" maxlength="50"></div>
			</div>
			<div class="tr clearfix">
				<div>
					<label class="textLabel">Deck Type</label>
					<div class="notice">Changing deck types will shuffle the deck.</div>
				</div>
				<div class="inputCol">
<?
	foreach ($mysql->query('SELECT short, name FROM deckTypes') as $deckType) 
		echo "\t\t\t\t\t".'<p><input type="radio" name="deckType" value="'.$deckType['short'].'"'.($deckDetails['type'] == $deckType['short']?' checked="checked"':'').'>'.$deckType['name']."</p>\n";
?>
				</div>
			</div>
			<div id="allowAccess">Allow Access</div>
			<div>
				<a id="checkAll" href="">[ Check All ]</a>
				<a id="uncheckAll" href="">[ Uncheck All ]</a>
			</div>
			<div id="users" class="clearfix">
<?
	$players = $mysql->query("SELECT u.userID, u.username, p.primaryGM FROM users u, players p WHERE u.userID = p.userID AND p.gameID = $gameID AND p.approved = 1");
	foreach ($players as $player) {
?>
				<div class="tr user">
					<input type="checkbox" name="addUser[<?=$player['userID']?>]"<?=(in_array($player['userID'], $deckPermissions) || $player['primaryGM']?' checked="checked"':'').($player['primaryGM']?' data-disabled="disabled"':'')?>>
					<label><?=$player['username']?></label>
				</div>
<?	} ?>
			</div>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="<?=$action == 'edit'?'edit':'create'?>" class="fancyButton"><?=$action == 'edit'?'Edit':'Create'?></button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>