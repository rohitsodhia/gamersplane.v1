<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');

	require_once(FILEROOT.'/header.php');
	if ($loggedIn) 
		require_once(FILEROOT.'/topNotifications.php');
?>
		<div class="clearfix">
			<div id="announcements">
<?
	addPackage('forum');
	$post = $mysql->query('SELECT t.firstPostID FROM threads t WHERE t.forumID = 3 ORDER BY threadID DESC LIMIT 1');
	$post = new Post($post->fetchColumn());
?>
				<h2 class="headerbar"><a href="/forums/thread/<?=$post->getThreadID()?>/"><?=$post->getTitle()?></a></h2>
				<h4><span class="convertTZ"><?=$post->getDatePosted('F j, Y g:i a')?></span> by <a href="/user/<?=$post->getAuthor('userID')?>/" class="username"><?=$post->getAuthor('username')?></a></h4>
				<hr>
<?=printReady(BBCode2Html(filterString($post->getMessage())))?>
<?	if ($loggedIn) { ?>
				<div class="readMore">To comment to this post or to read what others thought, please <a href="/forums/thread/<?=$post->getThreadID()?>/">click here</a>.</div>
<?	} ?>
			</div>
			<div class="sideWidget">
<?
	if ($loggedIn) {
		$usersGames = $mysql->query("SELECT g.gameID, g.title, g.system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame FROM games g INNER JOIN users u ON g.gmID = u.userID INNER JOIN players p ON g.gameID = p.gameID AND p.userID = {$currentUser->userID} LEFT JOIN (SELECT gameID, COUNT(*) - 1 playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID WHEre g.retired IS NULL ORDER BY gameID DESC LIMIT 3");
?>
				<div class="loggedIn<?=$usersGames->rowCount()?'':' noGames'?>">
					<h2>Your Games</h2>
<?		if ($usersGames->rowCount()) { ?>
					<div class="games">
<?
			foreach ($usersGames as $gameInfo) {
				$gameInfo['numPlayers'] = intval($gameInfo['numPlayers']);
				$gameInfo['playersInGame'] = intval($gameInfo['playersInGame']);
				$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
?>
						<div class="gameInfo">
							<p class="title"><a href="/games/<?=$gameInfo['gameID']?>"><?=$gameInfo['title']?></a> (<?=$slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}"?>)</p>
							<p class="details"><u><?=$systems->getFullName($gameInfo['system'], true)?></u> run by <a href="/user/<?=$gameInfo['gmID']?>/" class="username"><?=$gameInfo['username']?></a></p>
						</div>
<?			} ?>
					</div>
<?		} else { ?>
					<p>You're not in any games yet.</p>
					<div class="noGameLink"><a href="/games/list/">Join a game!</a></div>
<?		} ?>
				</div>
<?	} else { ?>
				<div class="loggedOut">
					We're a gaming community<br>
					Can't have community...<br>
					Without you!

					<div class="tr clearfix">
						<a href="/login" class="login loginLink">Login</a>
						<a href="/register" class="register">Register</a>
					</div>
				</div>
<?	} ?>
			</div>
		</div>

		<div class="clearfix">
			<div id="latestGames" class="homeWidget">
				<h3 class="headerbar">Latest Games</h3>
				<div class="widgetBody">
<?
	if ($loggedIn) 
		$latestGames = $mysql->query("SELECT g.gameID, g.title, g.system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame - 1 playersInGame FROM games g LEFT JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, COUNT(*) playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID LEFT JOIN characters c ON g.gameID = c.gameID AND c.userID = {$currentUser->userID} WHERE g.retired IS NULL AND c.characterID IS NULL AND g.status = 1 ORDER BY gameID DESC LIMIT 5");
	else 
		$latestGames = $mysql->query("SELECT g.gameID, g.title, g.system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame - 1 playersInGame FROM games g LEFT JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, COUNT(*) playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID WHERE g.retired IS NULL AND g.status = 1 ORDER BY gameID DESC LIMIT 5");
	$first = true;
	foreach ($latestGames as $gameInfo) {
		$gameInfo['numPlayers'] = intval($gameInfo['numPlayers']);
		$gameInfo['playersInGame'] = intval($gameInfo['playersInGame']);
		$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
		if (!$first) 
			echo "					<hr>\n";
		else 
			$first = false;
?>
					<div class="gameInfo">
						<p class="title"><a href="/games/<?=$gameInfo['gameID']?>/"><?=$gameInfo['title']?></a> (<?=$slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}"?>)</p>
						<p class="details"><u><?=$systems->getFullName($gameInfo['system'])?></u> run by <a href="/user/<?=$gameInfo['gmID']?>/" class="username"><?=$gameInfo['username']?></a></p>
					</div>
<?	} ?>
				</div>
			</div>
			
			<div id="latestPosts" class="homeWidget">
				<h3 class="headerbar">Latest Posts</h3>
				<div class="widgetBody">
<?
	$forumSearch = new ForumSearch('homepage');
	$forumSearch->findThreads();
	$forumSearch->displayLatestHP();
?>
				</div>
			</div>

			<div id="partners" class="homeWidget">
				<h3 class="headerbar">Partners</h3>
<?
	$rand = $mongo->execute('Math.random()');
	$partner = $mongo->links->findOne(array('level' => 'Partner', 'random' => array('$gte' => $rand['retval'])));
	if ($partner == null) 
		$partner = $mongo->links->findOne(array('level' => 'Partner', 'random' => array('$lte' => $rand['retval'])));
?>
				<div class="widgetBody">
					<a href="<?=$partner['url']?>" target="_blank"><img src="/images/links/<?=$partner['_id']?>.<?=$partner['image']?>"></a>
					<p><a href="<?=$partner['url']?>" target="_blank"><?=$partner['title']?></a></p>
				</div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>