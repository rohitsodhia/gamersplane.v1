<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
	$loggedIn = checkLogin(0);

	require_once(FILEROOT.'/header.php');
?>
		<div id="widgets" class="sidebar">
			<div id="latestGames" class="widget">
				<h3>Latest Games</h3>
				<div class="widgetBody">
<?
	$latestGames = $mysql->query('SELECT g.gameID, g.title, s.fullName system, g.gmID, u.username, g.created started, g.numPlayers, np.playersInGame FROM games g INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN users u ON g.gmID = u.userID LEFT JOIN (SELECT gameID, COUNT(*) playersInGame FROM characters WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) np ON g.gameID = np.gameID ORDER BY gameID DESC LIMIT 5');
	foreach ($latestGames as $gameInfo) {
		$gameInfo['started'] = switchTimezone($_SESSION['timezone'], $gameInfo['started']);
		$gameInfo['numPlayers'] = intval($gameInfo['numPlayers']);
		$gameInfo['playersInGame'] = intval($gameInfo['playersInGame']);
		$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
		echo "\t\t\t\t\t<div class=\"gameInfo\">\n";
		echo "\t\t\t\t\t\t<p class=\"title\"><a href=\"".SITEROOT."/games/{$gameInfo['gameID']}\">{$gameInfo['title']}</a> (".($slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}").")</p>\n";
		echo "\t\t\t\t\t\t<p class=\"details\"><u>{$gameInfo['system']}</u> run by <a href=\"".SITEROOT."/users/{$gameInfo['gmID']}\" class=\"username\">{$gameInfo['username']}</a></p>\n";
//		if ($slotsLeft == 0) echo "\t\t\t\t\t<p class=\"details\">No Slots Remaining</p>\n";
//		else echo "\t\t\t\t\t<p class=\"details\">{$slotsLeft} Slots Still Open</p>\n";
//		echo "\t\t\t\t\t\t<p class=\"details\">Started on ".date('M j, Y g:i a', $gameInfo['started'])."</p>\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div>
			</div>
			
			<div id="availSystems" class="widget">
				<h3>Available Systems</h3>
				<div class="widgetBody">
				<p>Gamers Plane has a number of systems built into our site, including:</p>
					<ul>
<?
	$systems = $mysql->query('SELECT fullName FROM (SELECT fullName FROM systems WHERE systemID != 0 ORDER BY RAND() LIMIT 4) s ORDER BY fullName');
	foreach ($systems as $info) echo "\t\t\t\t\t\t<li>{$info['fullName']}</li>\n";
//	foreach ($systemNames as $system) if ($system != 'Custom') echo "\t\t\t\t\t\t<li>$system</li>\n";
?>
					</ul>
					<p>And many more availabe and coming!</p>
<?=$loggedIn?"					<p>If you have a system you want added, <a href=\"".SITEROOT."/forums/thread/2\">let us know</a>!</p>\n":''?>
				</div>
			</div>
		</div>
		
<!--		<div id="siteInfo">
			<h1>Gamers Plane</h1>
			<p><b>Gamers Plane</b> is the 
			<hr>
		</div>-->
		
		<div id="announcements" class="mainColumn">
			<h1>Announcements</h1>
<?
/*	$mysql->setTable('threads', 'posts', 'users');
	$mysql->setSelectCols('threads.threadID', 'posts.title', 'users.userID', 'users.username', 'posts.message', 'posts.datePosted', 'users.timezone', 'users.dst');
	$mysql->setWhere('threads.forumID = 3 && threads.firstPostID = posts.postID && posts.authorID = users.userID');
	$mysql->setOrder('posts.datePosted DESC');
	$mysql->setLimit(5);
	$mysql->stdQuery('select', 'selectCols', 'where', 'order', 'limit');*/
	$posts = $mysql->query('SELECT t.threadID, p.title, u.userID, u.username, p.message, p.datePosted FROM threads t, threads_relPosts rp, posts p, users u WHERE t.threadID = rp.threadID AND t.forumID = 3 AND rp.firstPostID = p.postID AND p.authorID = u.userID ORDER BY datePosted DESC LIMIT 5');
	foreach ($posts as $postInfo) {
		$postInfo['datePosted'] = switchTimezone($_SESSION['timezone'], $postInfo['datePosted']);
		echo "\t\t\t<div class=\"postDiv\">\n";
		echo "\t\t\t\t<h2><a href=\"".SITEROOT.'/forums/thread/'.$postInfo['threadID'].'">'.$postInfo['title']."</a></h2>\n";
		echo "\t\t\t\t<h4>".date('F j, Y g:i a', $postInfo['datePosted']).' by <a href="'.SITEROOT.'/users/'.$postInfo['userID'].'" class="username">'.$postInfo['username']."</a></h4>\n";
		echo "\t\t\t\t<hr>\n";
		echo BBCode2Html(filterString(printReady($postInfo['message'])));
		if ($loggedIn) echo "\t\t\t\t<div class=\"readMore\">To comment to this post or to read what others thought, please <a href=\"".SITEROOT.'/forums/thread/'.$postInfo['threadID']."\">click here</a>.</div>\n";
		echo "\t\t\t</div>\n";
	}
?>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>