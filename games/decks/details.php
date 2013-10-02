<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = $userID");
	if (!$gmCheck->rowCount()) { header('Location: '.SITEROOT.'/tools/maps'); exit; }

	if ($pathOptions[2] == 'edit') {
		
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">New Deck</h1>
		
		<form method="post" action="<?=SITEROOT?>/games/process/decks/details" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<div class="tr clearfix">
				<label class="textLabel">Deck Label</label>
				<div class="inputCol"><input id="deckLabel" type="text" name="deckLabel" maxlength="50"></div>
			</div>
			<div class="tr clearfix">
				<label class="textLabel">Deck Type</label>
				<div class="inputCol">
<?
	$first = TRUE;
	foreach ($mysql->query('SELECT short, name FROM deckTypes') as $deckType) {
		echo "\t\t\t\t\t".'<p><input type="radio" name="deckType" value="'.$deckType['short'].'"'.($first?' checked="checked"':'').'>'.$deckType['name']."</p>\n";
		if ($first) $first = FALSE;
	}
?>
				</div>
			</div>
			<div>Allow Access</div>
			<div>
				<a id="checkAll" href="">[ Check All ]</a>
				<a id="uncheckAll" href="">[ Uncheck All ]</a>
			</div>
			<div id="users" class="clearfix">
<?
	$players = $mysql->query('SELECT u.userID, u.username FROM users u, players p WHERE u.userID = p.userID AND p.gameID = '.$gameID.' AND p.approved = 1');
	foreach ($players as $player) {
?>
				<div class="tr user">
					<input type="checkbox" name="addUser[<?=$player['userID']?>]">
					<label><?=$player['username']?></label>
				</div>
<?	} ?>
			</div>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="create" class="fancyButton">Create</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>