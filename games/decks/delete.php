<?
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$deckID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT p.primaryGM FROM players p, decks d WHERE p.isGM = 1 AND p.gameID = $gameID AND d.gameID = p.gameID AND d.deckID = $deckID AND p.userID = $userID");
	if (!$gmCheck->rowCount()) { header('Location: /tools/maps'); exit; }
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Delete Deck</h1>
		
<?
	$deckDetails = $mysql->query("SELECT label FROM decks where deckID = $deckID");
	$deckDetails = $deckDetails->fetch();
	extract($deckDetails);
?>
		<form method="post" action="/games/process/decks/delete" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			
			<p class="alignCenter">Are you sure you want to delete the deck <strong><?=$label?></strong>?</p>
			<div class="alignCenter"><button type="submit" name="delete" class="fancyButton">Delete</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>