<?
	$gameID = intval($pathOptions[0]);
	$deckID = intval($pathOptions[2]);
	$gmCheck = $mongo->games->findOne(array('gameID' => $gameID, 'players' => array('$elemMatch' => array('user.userID' => $currentUser->userID, 'isGM ' => true))), array('players.$' => true));
	if (!$gmCheck) { header('Location: /tools/decks/'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Delete Deck</h1>
		
<?
	$deckDetails = $mysql->query("SELECT label FROM decks where deckID = $deckID");
	$deckDetails = $deckDetails->fetch();
	extract($deckDetails);
?>
		<form method="post" action="/games/process/decks/delete/" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			
			<p class="alignCenter">Are you sure you want to delete the deck <strong><?=$label?></strong>?</p>
			<div class="alignCenter"><button type="submit" name="delete" class="fancyButton">Delete</button></div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>