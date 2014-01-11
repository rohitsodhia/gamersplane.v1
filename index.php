<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	require_once(FILEROOT.'/header.php');
?>
		<div class="clearfix">
			<div id="announcements">
<?
	$posts = $mysql->query('SELECT t.threadID, p.title, u.userID, u.username, p.message, p.datePosted FROM threads t, threads_relPosts rp, posts p, users u WHERE t.threadID = rp.threadID AND t.forumID = 3 AND rp.firstPostID = p.postID AND p.authorID = u.userID ORDER BY datePosted DESC LIMIT 1');
	$postInfo = $posts->fetch();
	$postInfo['datePosted'] = switchTimezone($_SESSION['timezone'], $postInfo['datePosted']);
	echo "\t\t\t\t<h2 class=\"headerbar\"><a href=\"".SITEROOT.'/forums/thread/'.$postInfo['threadID'].'">'.$postInfo['title']."</a></h2>\n";
	echo "\t\t\t\t<h4>".date('F j, Y g:i a', $postInfo['datePosted']).' by <a href="'.SITEROOT.'/users/'.$postInfo['userID'].'" class="username">'.$postInfo['username']."</a></h4>\n";
	echo "\t\t\t\t<hr>\n";
	echo BBCode2Html(filterString(printReady($postInfo['message'])));
	if ($loggedIn) echo "\t\t\t\t<div class=\"readMore\">To comment to this post or to read what others thought, please <a href=\"".SITEROOT.'/forums/thread/'.$postInfo['threadID']."\">click here</a>.</div>\n";
?>
			</div>
			<div class="sideWidget">
<?
	if ($loggedIn) {
		$usersGames = $mysql->query("SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame FROM games g INNER JOIN systems s ON g.systemID = s.systemID INNER JOIN users u ON g.gmID = u.userID INNER JOIN players p ON g.gameID = p.gameID AND p.userID = $userID LEFT JOIN (SELECT gameID, COUNT(*) - 1 playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID ORDER BY gameID DESC LIMIT 3");
		echo "				<div class=\"loggedIn".($usersGames->rowCount()?'':' noGames')."\">\n";
		echo "					<h2>Your Games</h2>\n";
		if ($usersGames->rowCount()) {
			echo "					<div class=\"games\">\n";
			foreach ($usersGames as $gameInfo) {
				$gameInfo['started'] = switchTimezone($_SESSION['timezone'], $gameInfo['started']);
				$gameInfo['numPlayers'] = intval($gameInfo['numPlayers']);
				$gameInfo['playersInGame'] = intval($gameInfo['playersInGame']);
				$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
				echo "\t\t\t\t\t\t<div class=\"gameInfo\">\n";
				echo "\t\t\t\t\t\t\t<p class=\"title\"><a href=\"".SITEROOT."/games/{$gameInfo['gameID']}\">{$gameInfo['title']}</a> (".($slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}").")</p>\n";
				echo "\t\t\t\t\t\t\t<p class=\"details\"><u>{$gameInfo['system']}</u> run by <a href=\"".SITEROOT."/users/{$gameInfo['gmID']}\" class=\"username\">{$gameInfo['username']}</a></p>\n";
				echo "\t\t\t\t\t\t</div>\n";
			}
			echo "\t\t\t\t\t</div>\n";
		} else {
			echo "\t\t\t\t\t\t<p>You're not in any games yet.</p>\n";
			echo "\t\t\t\t\t\t<div class=\"noGameLink\"><a href=\"".SITEROOT."/games/list\">Join a game!</a></div>\n";
		}
		echo "\t\t\t\t\t</div>\n";
	} else {
?>
				<div class="loggedOut">
					We're a gaming community<br>
					Can't have community...<br>
					Without you!

					<div class="tr clearfix">
						<a href="<?=SITEROOT?>/login" class="login loginLink">Login</a>
						<a href="<?=SITEROOT?>/register" class="register">Register</a>
					</div>
				</div>
<? } ?>
			</div>
		</div>

		<div class="clearfix">
			<div id="latestGames" class="homeWidget">
				<h3 class="headerbar">Latest Games</h3>
				<div class="widgetBody">
<?
	if ($loggedIn) $latestGames = $mysql->query('SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame - 1 playersInGame FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, COUNT(*) playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID LEFT JOIN characters c ON g.gameID = c.gameID AND c.userID = '.$userID.' WHERE c.characterID IS NULL ORDER BY gameID DESC LIMIT 5');
	else $latestGames = $mysql->query('SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame - 1 playersInGame FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, COUNT(*) playersInGame FROM players WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID ORDER BY gameID DESC LIMIT 5');
	$first = TRUE;
	foreach ($latestGames as $gameInfo) {
		$gameInfo['started'] = switchTimezone($_SESSION['timezone'], $gameInfo['started']);
		$gameInfo['numPlayers'] = intval($gameInfo['numPlayers']);
		$gameInfo['playersInGame'] = intval($gameInfo['playersInGame']);
		$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
		if (!$first) echo "\t\t\t\t\t<hr>\n";
		else $first = FALSE;
		echo "\t\t\t\t<div class=\"gameInfo\">\n";
		echo "\t\t\t\t\t<p class=\"title\"><a href=\"".SITEROOT."/games/{$gameInfo['gameID']}\">{$gameInfo['title']}</a> (".($slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}").")</p>\n";
		echo "\t\t\t\t\t<p class=\"details\"><u>{$gameInfo['system']}</u> run by <a href=\"".SITEROOT."/users/{$gameInfo['gmID']}\" class=\"username\">{$gameInfo['username']}</a></p>\n";
//		if ($slotsLeft == 0) echo "\t\t\t\t<p class=\"details\">No Slots Remaining</p>\n";
//		else echo "\t\t\t\t<p class=\"details\">{$slotsLeft} Slots Still Open</p>\n";
//		echo "\t\t\t\t\t<p class=\"details\">Started on ".date('M j, Y g:i a', $gameInfo['started'])."</p>\n";
		echo "\t\t\t\t</div>\n";
	}
?>
				</div>
			</div>
			
			<div id="latestPosts" class="homeWidget">
				<h3 class="headerbar">Latest Posts</h3>
				<div class="widgetBody">
<?
	$coreForums = $mysql->query('SELECT forumID FROM forums WHERE heritage LIKE "'.sql_forumIDPad(1).'-%" OR heritage LIKE "'.sql_forumIDPad(6).'-%"');
	$forumIDs = array();
	foreach ($coreForums as $forum) $forumIDs[] = $forum['forumID'];
	if ($loggedIn) {
		$gameForums = $mysql->query('SELECT g.forumID FROM players p, games g WHERE p.userID = '.$userID.' AND p.approved = 1 AND p.gameID = g.gameID');
		if ($gameForums->rowCount()) {
			$gameSForums = $mysql->prepare('SELECT forumID FROM forums WHERE heritage LIKE CONCAT("'.sql_forumIDPad(2).'-", LPAD(:forumID, '.HERITAGE_PAD.', 0), "%")');
			$gameForumIDs = array();
			foreach ($gameForums as $gameForum) {
				$gameSForums->bindValue(':forumID', $gameForum['forumID']);
				$gameSForums->execute();
				foreach ($gameSForums as $gameSForum) $gameForumIDs[] = $gameSForum['forumID'];
			}
			$permissions = retrievePermissions($userID, $gameForumIDs, 'read');
			foreach ($permissions as $pForumID => $permission) {
				if ($permission['read']) $forumIDs[] = $pForumID;
			}
		}
	}
	$latestPosts = $mysql->query('SELECT t.threadID, p.title, u.userID, u.username, p.datePosted, f.forumID, f.title fTitle, IF(np.newPosts IS NULL, 0, 1) newPosts FROM threads t INNER JOIN threads_relPosts rp ON t.threadID = rp.threadID INNER JOIN posts p ON rp.lastPostID = p.postID LEFT JOIN users u ON p.authorID = u.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = '.($loggedIn?$userID:'NULL').' LEFT JOIN forums f ON t.forumID = f.forumID LEFT JOIN forums_readData_newPosts np ON t.threadID = np.threadID AND np.userID = '.($loggedIn?$userID:'NULL').' WHERE t.forumID IN ('.implode(', ', $forumIDs).') ORDER BY rp.lastPostID DESC LIMIT 3');

	$first = TRUE;
	foreach ($latestPosts as $latestPost) {
		$latestPost['datePosted'] = switchTimezone($_SESSION['timezone'], $latestPost['datePosted']);
		if (!$first) echo "\t\t\t\t\t<hr>\n";
		else $first = FALSE;
		echo "\t\t\t\t\t<div class=\"post\">\n";
		echo "\t\t\t\t\t\t<div class=\"forumIcon".($latestPost['newPosts']?' newPosts':'')."\"></div>\n";
		echo "\t\t\t\t\t\t<div class=\"title\"><a href=\"".SITEROOT."/forums/thread/{$latestPost['threadID']}\">{$latestPost['title']}</a></div>\n";
		echo "\t\t\t\t\t\t<div class=\"byLine\">by <a href=\"".SITEROOT."/users/{$latestPost['userID']}\" class=\"username\">{$latestPost['username']}</a>, ".date('M j, Y g:i a', $latestPost['datePosted'])."</div>\n";
		echo "\t\t\t\t\t\t<div class=\"forum\">in <a href=\"".SITEROOT."/forums/{$latestPost['forumID']}\">{$latestPost['fTitle']}</a></div>\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div>
			</div>

			<div id="availSystems" class="homeWidget">
				<h3 class="headerbar">Available Systems</h3>
				<p>Gamers Plane has a number of systems built into our site, including:</p>
				<ul>
<?
	$systems = $mysql->query('SELECT fullName FROM (SELECT fullName FROM systems WHERE systemID != 0 ORDER BY RAND() LIMIT 5) s ORDER BY fullName');
	foreach ($systems as $info) echo "\t\t\t\t\t<li>{$info['fullName']}</li>\n";
?>
				</ul>
				<p>And many more availabe and coming!</p>
<?=$loggedIn?"				<p>If you have a system you want added, <a href=\"".SITEROOT."/forums/thread/2\">let us know</a>!</p>\n":''?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>