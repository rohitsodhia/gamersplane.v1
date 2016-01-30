<?
	$pending = $mongo->games->find(
		array(
			'players' => array(
				'$elemMatch' => array(
					'user.userID' => $currentUser->userID,
					'isGM' => true
				)
			)
		), array(
			'gameID' => true,
			'title' => true,
			'players' => true
		)
	);
	if ($pending->count()) {
		$pendingIDs = array();
		$pendingPlayers = array();
		$pendingChars = array();
		foreach ($pending as $game) {
			$pendingIDs[] = $game['gameID'];
			foreach ($game['players'] as $player) {
				if (!$player['approved']) {
					if (!isset($pendingPlayers[$game['gameID']])) 
						$pendingPlayers[$game['gameID']] = 0;
					$pendingPlayers[$game['gameID']]++;
				}
				if (sizeof($player['characters'])) {
					foreach ($player['characters'] as $character) {
						if (!$character['approved']) {
							if (!isset($pendingChars[$game['gameID']])) 
								$pendingChars[$game['gameID']] = 0;
							$pendingChars[$game['gameID']]++;
						}
					}
				}
			}
		}
	}

	if (sizeof($pendingPlayers) > 0 || sizeof($pendingChars) > 0 || $pmCount > 0) {
?>
		<div id="topNotifications" class="alertBox_info"><ul>
<?		if ($pmCount) { ?>
			<li>You have <?=$pmCount?> unread <a href="/pms/">message<?=$pmCount > 1?'s':''?></a></li>
<?
		}
		if (sizeof($pendingPlayers) || sizeof($pendingChars)) { foreach ($pending as $game) {
			$gameID = $game['gameID'];
			if ($pendingPlayers[$gameID] || $pendingChars[$gameID]) {
?>
			<li>You have <? if ($pendingPlayers[$gameID] > 0) { ?><?=$pendingPlayers[$gameID]?> player<?=$pendingPlayers[$gameID] > 1?'s':''?><? } if ($pendingPlayers[$gameID] && $pendingChars[$gameID]) echo ' and '; if ($pendingChars[$gameID] > 0) { ?><?=$pendingChars[$gameID]?> character<?=$pendingChars[$gameID] > 1?'s':''?><? } ?> pending in <a href="/games/<?=$gameID?>/"><?=$game['title']?></a></li>
<?
			}
		} }
?>
		</div></ul>
<?	} ?>