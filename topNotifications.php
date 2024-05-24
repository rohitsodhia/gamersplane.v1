<?php
	$pendingPlayers = $mysql->query("SELECT games.gameID, games.title, COUNT(*) as numberPlayers FROM players pendingPlayers INNER JOIN games ON pendingPlayers.gameID = games.gameID INNER JOIN players gms ON games.gameID = gms.gameID AND gms.isGM = TRUE AND gms.userID = {$currentUser->userID} WHERE pendingPlayers.approved = FALSE GROUP BY games.gameID");
	$pendingCharacters = $mysql->query("SELECT games.gameID, games.title, COUNT(*) as numberCharacters FROM characters pendingCharacters INNER JOIN games ON pendingCharacters.gameID = games.gameID INNER JOIN players gms ON games.gameID = gms.gameID AND gms.isGM = TRUE AND gms.userID = {$currentUser->userID} WHERE pendingCharacters.approved = FALSE GROUP BY games.gameID");
	$pending = [];
	if ($pendingPlayers->rowCount() || $pendingCharacters->rowCount()) {
		foreach ($pendingPlayers->fetchAll() as $players) {
			$pending[$players['gameID']] = [
				'game' => ['gameID' => $players['gameID'], 'title' => $players['title']],
				'pending' => ['players' => $players['numberPlayers'], 'characters' => 0]
			];
		}
		foreach ($pendingCharacters->fetchAll() as $characters) {
			if (!in_array($characters['gameID'])) {
				$pending[$characters['gameID']] = [
					'game' => ['gameID' => $characters['gameID'], 'title' => $characters['title']],
					'pending' => ['players' => 0, 'characters' => $characters['numberCharacters']]
				];
			} else {
				$pending[$characters['gameID']]['pending']['characters'] = $characters['numberCharacters'];
			}
		}
	}

	if ($pending || $pmCount > 0) {
?>
		<div id="topNotifications" class="alertBox_info"><ul>
<?php		if ($pmCount) { ?>
			<li>You have <?=$pmCount?> unread <a href="/pms/">message<?=$pmCount > 1 ? 's' : ''?></a></li>
<?php
		}
		if ($pending) { foreach ($pending as $game) {
			$gameID = $game['game']['gameID'];
			$title = $game['game']['title'];
			$numPlayers = $game['pending']['players'];
			$numCharacters = $game['pending']['characters'];
?>
			<li>You have <?php if ($numPlayers > 0) { ?><?=$numPlayers?> player<?=$numPlayers > 1 ? 's' : ''?><?php } if ($numPlayers && $numCharacters) echo ' and '; if ($numCharacters > 0) { ?><?=$numCharacters?> character<?=$characters > 1 ? 's' : ''?><?php } ?> pending in <a href="/games/<?=$gameID?>/"><?=$title?></a></li>
<?php
		} }
	}
?>
		</ul></div>
<?php
	$invitedTo = $mysql->query("SELECT games.gameID, games.title FROM invites INNER JOIN games ON invites.gameID = games.gameID WHERE invites.userID = {$currentUser->userID}");
	if ($invitedTo->rowCount()) {
?>
		<div id="topNotifications" class="alertBox_info"><ul>
<?php			foreach ($invitedTo as $game) { ?>
				<li>You have been invited to join <a href="/games/<?=$game['gameID']?>/"><?=$game['title']?></a></li>
<?php			} ?>
		</ul></div>
<?php	}?>
