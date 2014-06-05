<? if ($isGM || $isInGame) { ?>
		<div id="gameMenu">
<? 	if ($isGM) { ?>
			<a href="/forums/<?=$gameInfo['forumID']?>">Game Forums</a>
			<a href="/games/<?=$gameInfo['gameID']?>/edit">Edit Game Details</a>
			<a href="/games/<?=$gameInfo['gameID']?>/decks">Modify Game Decks</a>
			<a href="/chat/<?=$gameInfo['gameID']?>">Chat</a>
			<a href="/tools/maps/?gameID=<?=$gameInfo['gameID']?>">Game Maps</a>
<? 	} elseif ($isInGame && $userCharInfo['approved']) { ?>
			<a href="/forums/<?=$gameInfo['forumID']?>">Game Forums</a>
			<a href="/games/leave/<?=$gameInfo['gameID']?>">Leave Game</a>
<? 	} elseif ($isInGame) { ?>
			<a href="/chat/<?=$gameInfo['gameID']?>">Chat</a>
			<a href="/tools/maps/?gameID=<?=$gameInfo['gameID']?>">Game Maps</a>
			<a href="/games/leave/<?=$gameInfo['gameID']?>">Withdraw Character</a>
<? 	} else { ?>
<? 	} ?>
		</div>

<? } ?>