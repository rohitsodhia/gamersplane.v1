<? if ($isGM || $isInGame) { ?>
		<div id="gameMenu">
<? 	if ($isGM) { ?>
			<a href="<?=SITEROOT?>/forums/<?=$gameInfo['forumID']?>">Game Forums</a>
			<a href="<?=SITEROOT?>/games/<?=$gameInfo['gameID']?>/edit">Edit Game Details</a>
			<a href="<?=SITEROOT?>/games/<?=$gameInfo['gameID']?>/decks">Modify Game Decks</a>
			<a href="<?=SITEROOT?>/chat/<?=$gameInfo['gameID']?>">Chat</a>
			<a href="<?=SITEROOT?>/tools/maps/?gameID=<?=$gameInfo['gameID']?>">Game Maps</a>
<? 	} elseif ($isInGame && $userCharInfo['approved']) { ?>
			<a href="<?=SITEROOT?>/forums/<?=$gameInfo['forumID']?>">Game Forums</a>
			<a href="<?=SITEROOT?>/games/leave/<?=$gameInfo['gameID']?>">Leave Game</a>
<? 	} elseif ($isInGame) { ?>
			<a href="<?=SITEROOT?>/chat/<?=$gameInfo['gameID']?>">Chat</a>
			<a href="<?=SITEROOT?>/tools/maps/?gameID=<?=$gameInfo['gameID']?>">Game Maps</a>
			<a href="<?=SITEROOT?>/games/leave/<?=$gameInfo['gameID']?>">Withdraw Character</a>
<? 	} else { ?>
<? 	} ?>
		</div>

<? } ?>