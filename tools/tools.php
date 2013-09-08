<?
	$loggedIn = checkLogin(0);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Tools</h1>
		
		<div id="toolsList">
			<a href="<?=SITEROOT?>/tools/dice">
				<img src="<?=SITEROOT?>/images/tools/dice.png">
				<p>Dice Roller</p>
			</a>
			<a href="<?=SITEROOT?>/tools/cards">
				<img src="<?=SITEROOT?>/images/tools/fannedCards.png">
				<p>Deck of Cards</p>
			</a>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>