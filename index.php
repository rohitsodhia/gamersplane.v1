<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');

	require_once(FILEROOT.'/header.php');
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
<?=BBCode2Html(filterString($post->getMessage(true)))?>
<?	if ($loggedIn) { ?>
				<div class="readMore">To comment to this post or to read what others thought, please <a href="/forums/thread/<?=$post->getThreadID()?>/">click here</a>.</div>
<?	} ?>
			</div>
			<div class="sideWidget">
<?
	if ($loggedIn) {
		$usersGames = $mysql->query("SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame FROM games g INNER JOIN systems s ON g.systemID = s.systemID INNER JOIN users u ON g.gmID = u.userID INNER JOIN players p ON g.gameID = p.gameID AND p.userID = {$currentUser->userID} LEFT JOIN (SELECT gameID, COUNT(*) - 1 playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID ORDER BY gameID DESC LIMIT 3");
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
							<p class="details"><u><?=$gameInfo['system']?></u> run by <a href="/user/<?=$gameInfo['gmID']?>/" class="username"><?=$gameInfo['username']?></a></p>
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
	if ($loggedIn) $latestGames = $mysql->query("SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame - 1 playersInGame FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, COUNT(*) playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID LEFT JOIN characters c ON g.gameID = c.gameID AND c.userID = {$currentUser->userID} WHERE g.retired = 0 AND c.characterID IS NULL AND g.open = 1 ORDER BY gameID DESC LIMIT 5");
	else $latestGames = $mysql->query('SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame - 1 playersInGame FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, COUNT(*) playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID WHERE g.retired = 0 AND g.open = 1 ORDER BY gameID DESC LIMIT 5');
	$first = true;
	foreach ($latestGames as $gameInfo) {
		$gameInfo['numPlayers'] = intval($gameInfo['numPlayers']);
		$gameInfo['playersInGame'] = intval($gameInfo['playersInGame']);
		$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
		if (!$first) echo "					<hr>\n";
		else $first = false;
?>
					<div class="gameInfo">
						<p class="title"><a href="/games/<?=$gameInfo['gameID']?>/"><?=$gameInfo['title']?></a> (<?=$slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}"?>)</p>
						<p class="details"><u><?=$gameInfo['system']?></u> run by <a href="/user/<?=$gameInfo['gmID']?>/" class="username"><?=$gameInfo['username']?></a></p>
					</div>
<?	} ?>
				</div>
			</div>
			
			<div id="latestPosts" class="homeWidget">
				<h3 class="headerbar">Latest Posts</h3>
				<div class="widgetBody">
<?
	$coreForums = $mysql->query('SELECT forumID FROM forums WHERE heritage LIKE "'.sql_forumIDPad(1).'-%" OR heritage LIKE "'.sql_forumIDPad(6).'-%" OR heritage LIKE "%-'.sql_forumIDPad(10).'%"');
	$forumIDs = array();
	foreach ($coreForums as $forum) $forumIDs[] = $forum['forumID'];
	if ($loggedIn) {
		$gameForums = $mysql->query("SELECT g.forumID FROM players p, games g WHERE p.userID = {$currentUser->userID} AND p.approved = 1 AND p.gameID = g.gameID");
		if ($gameForums->rowCount()) {
			$gameSForums = $mysql->prepare('SELECT forumID FROM forums WHERE heritage LIKE CONCAT("'.sql_forumIDPad(2).'-", LPAD(:forumID, '.HERITAGE_PAD.', 0), "%")');
			$gameForumIDs = array();
			foreach ($gameForums as $gameForum) {
				$gameSForums->bindValue(':forumID', $gameForum['forumID']);
				$gameSForums->execute();
				foreach ($gameSForums as $gameSForum) $gameForumIDs[] = $gameSForum['forumID'];
			}
			$permissions = retrievePermissions($currentUser->userID, $gameForumIDs, 'read');
			foreach ($permissions as $pForumID => $permission) {
				if ($permission['read']) $forumIDs[] = $pForumID;
			}
		}
	}
	$latestPosts = $mysql->query('SELECT t.threadID, p.title, u.userID, u.username, p.datePosted, f.forumID, f.title fTitle, IF(np.newPosts IS NULL, 0, 1) newPosts FROM threads t INNER JOIN threads_relPosts rp ON t.threadID = rp.threadID INNER JOIN posts p ON rp.lastPostID = p.postID LEFT JOIN users u ON p.authorID = u.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = '.($loggedIn?$currentUser->userID:'NULL').' LEFT JOIN forums f ON t.forumID = f.forumID LEFT JOIN forums_readData_newPosts np ON t.threadID = np.threadID AND np.userID = '.($loggedIn?$currentUser->userID:'NULL').' WHERE t.forumID IN ('.implode(', ', $forumIDs).') ORDER BY rp.lastPostID DESC LIMIT 3');

	$first = true;
	foreach ($latestPosts as $latestPost) {
		if (!$first) echo "					<hr>\n";
		else $first = false;
?>
					<div class="post">
						<div class="forumIcon<?=$latestPost['newPosts']?' newPosts':''?>"></div>
						<div class="title"><a href="/forums/thread/<?=$latestPost['threadID']?>/?view=NewPost"><?=$latestPost['title']?></a></div>
						<div class="byLine">by <a href="/user/<?=$latestPost['userID']?>/" class="username"><?=$latestPost['username']?></a>, <span class="convertTZ"><?=date('M j, Y g:i a', strtotime($latestPost['datePosted']))?></div>
						<div class="forum">in <a href="/forums/<?=$latestPost['forumID']?>/"><?=$latestPost['fTitle']?></a></div>
					</div>
<?	} ?>
				</div>
			</div>

			<div id="affiliates" class="homeWidget">
				<h3 class="headerbar">Affiliates</h3>
<?
	$rand = $mongo->execute('Math.random()');
	$rand = $rand['retval'];
	$affiliate = $mongo->links->findOne(array('level' => 2, 'random' => array('$gte' => $rand)));
	if ($affiliate == null) $affiliate = $mongo->links->findOne(array('level' => 2, 'random' => array('$lte' => $rand)));
?>
				<div class="widgetBody">
					<a href="<?=$affiliate['url']?>" target="_blank"><img src="/images/links/<?=$affiliate['_id']?>.<?=$affiliate['image']?>"></a>
					<p><a href="<?=$affiliate['url']?>" target="_blank"><?=$affiliate['title']?></a></p>
				</div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>