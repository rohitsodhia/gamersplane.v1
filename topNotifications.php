<?
	$pending = $mysql->query("SELECT g.gameID, g.title, IFNULL(pp.pendingPlayers, 0) pendingPlayers, IFNULL(pc.pendingCharacters, 0) pendingCharacters FROM games g INNER JOIN players gm ON g.gameID = gm.gameID AND isGM = 1 LEFT JOIN (SELECT gameID, COUNT(*) pendingPlayers FROM players WHERE approved = 0 GROUP BY gameID) pp ON g.gameID = pp.gameID LEFT JOIN (SELECT gameID, COUNT(*) pendingCharacters FROM characters WHERE approved = 0 GROUP BY gameID) pc ON g.gameID = pc.gameID WHERE gm.userID = {$currentUser->userID} HAVING pendingPlayers > 0 OR pendingCharacters > 0");
	if ($pending->rowCount() > 0 || $pmCount > 0) {
?>
		<div id="topNotifications" class="alertBox_info"><ul>
<?		if ($pmCount) { ?>
			<li>You have <?=$pmCount?> unread <a href="/pms/">message<?=$pmCount > 1?'s':''?></a></li>
<?
		}
		if ($pending->rowCount()) { foreach ($pending as $game) {
?>
			<li>You have <? if ($game['pendingPlayers'] > 0) { ?><?=$game['pendingPlayers']?> player<?=$game['pendingPlayers'] > 1?'s':''?><? } if ($game['pendingPlayers'] && $game['pendingCharacters']) echo ' and '; if ($game['pendingCharacters'] > 0) { ?><?=$game['pendingCharacters']?> character<?=$game['pendingCharacters'] > 1?'s':''?><? } ?> pending in <a href="/games/<?=$game['gameID']?>/"><?=$game['title']?></a></li>
<?		} } ?>
		</div></ul>
<?	} ?>