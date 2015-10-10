<?
	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar" skew-element>{{user.username}}</h1>
		<div id="leftCol">
			<img ng-src="{{user.avatar.url}}" class="avatar">
			<div id="actions">
				<a href="/pms/send/?userID={{user.userID}}">Send Private Message</a>
			</div>
		</div>
		<div id="rightCol">
			<div id="userInfo" class="userInfoBox">
				<h2 class="headerbar hbDark">User Information</h2>
				<div class="details">
					<div class="tr">
						<div class="title">Member Since</div>
						<div>{{user.joinDate | amUtc | amLocal | amDateFormat:'MMMM D, YYYY h:mm a'}}</div>
					</div>
					<div ng-if="user.lastActivity" class="tr">
						<div class="title">Inactive</div>
						<div ng-bind-html="user.lastActivity | trustHTML"></div>
					</div>
					<div ng-if="user.gender != 'n'" class="tr">
						<div class="title">Gender</div>
						<div>{{user.gender == 'm'?'Male':'Female'}}</div>
					</div>
<?
	if ($user->showAge == 1) {
		$thisYear = strtotime(date('Y').substr($user->birthday, 4));
?>
					
					<div ng-if="user.birthday.showAge" class="tr">
						<div class="title">Age</div>
						<div>{{user.birthday.date}}</div>
					</div>
<?	} ?>
<?	foreach ($profFields as $field => $label) { if (strlen($userInfo[$field])) { ?>
					
					<div class="tr">
						<div class="title"><?=$label?></div>
						<div><?=$userInfo[$field]?></div>
					</div>
<?	} } ?>
				</div>
			</div>
<? /*
			
			<div id="charStats" class="userInfoBox">
				<h2 class="headerbar hbDark">Characters Stats</h2>
<?
	$characters = $mysql->query("SELECT c.characterID, c.system, c.gameID, c.retired, COUNT(c.characterID) numChars FROM characters c INNER JOIN systems s ON c.system = s.shortName WHERE c.userID = {$profileID} AND retired IS NULL GROUP BY c.system ORDER BY numChars DESC, s.fullName");
	echo "				<div class=\"details clearfix".($characters->rowCount()?'':' noInfo')."\">\n";
	if ($characters->rowCount()) {
		$charStats = array();
		$numChars = 0;
		foreach ($characters as $info) {
			$charStats[] = $info;
			$numChars += $info['numChars'];
		}
		$count = 0;
		echo "\t\t\t\t\t<p>{$user->username} has made $numChars character".($numChars == 1?'':'s')." so far.</p>\n";
		foreach ($charStats as $game) {
			$count++;
?>
					<div class="game<?=$count % 3 == 0?' third':''?>">
						<div class="gameLogo"><img src="/images/logos/<?=$game['system']?>.png"></div>
						<div class="gameInfo">
							<p><?=$systems->getFullName($game['system'])?></p>
							<p><?=$game['numChars']?> Char"<?=($game['numChars'] == 1?'':'s')." - ".round($game['numChars'] / $numChars * 100, 2)?>%</p>
						</div>
					</div>
<?
		}
	} else 
		echo "\t\t\t\t<div>{$user->username} has not yet made any characters.</div>\n";
?>
				</div>
			</div>
			
			<div id="gmStats" class="userInfoBox">
				<h2 class="headerbar hbDark">GM Stats</h2>
<?
	$games = $mysql->query("SELECT g.gameID, g.system, g.retired, COUNT(g.gameID) numGames FROM games g INNER JOIN systems s ON g.system = s.shortName INNER JOIN players p USING (gameID) WHERE p.userID = {$profileID} AND p.isGM = 1 GROUP BY g.system ORDER BY numGames DESC, s.fullName");
	echo "				<div class=\"details clearfix".($games->rowCount()?'':' noInfo')."\">\n";
	if ($games->rowCount()) {
		$gameStats = array();
		$numGames = 0;
		foreach ($games as $info) {
			$gameStats[] = $info;
			$numGames += $info['numGames'];
		}
		$count = 0;
		echo "\t\t\t\t\t<p>{$user->username} has run $numGames game".($numGames == 1?'':'s')." so far.</p>\n";
		foreach ($gameStats as $game) {
			$count++;
?>
					<div class="game<?=$count % 3 == 0?' third':''?>">
						<div class="gameLogo"><img src="/images/logos/<?=$game['system']?>.png"></div>
						<div class="gameInfo">
							<p><?=$systems->getFullName($game['system'])?></p>
							<p><?=$game['numGames']?> Game<?=($game['numGames'] == 1?'':'s')." - ".round($game['numGames'] / $numGames * 100, 2)?>%</p>
						</div>
					</div>
<?
		}
	} else 
		echo "\t\t\t\t<div>{$user->username} has not yet run any games.</div>\n";
?>
				</div>
			</div>
*/ ?>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>