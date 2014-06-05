<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	
	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	if (!$isAdmin->rowCount()) { header('Location: /forums/'); exit; }
	
	if (isset($_POST['delete'])) {
		$parentID = $mysql->query('SELECT parentID FROM forums WHERE forumID = '.$forumID);
		$parentID = $parentID->fetchColumn();

		$mysql->query("DELETE f, c, t, p, po, popt, pv, pge, pgr, pu, rdf, rdt, r, d FROM forums f INNER JOIN forums c ON c.heritage LIKE CONCAT(f.heritage, '%') LEFT JOIN threads t ON c.forumID = t.forumID LEFT JOIN posts p ON t.threadID = p.threadID LEFT JOIN forums_polls po ON t.threadID = po.threadID LEFT JOIN forums_pollOptions popt ON t.threadID = popt.threadID LEFT JOIN forums_pollVotes pv ON popt.pollOptionID = pv.pollOptionID LEFT JOIN forums_permissions_general pge ON c.forumID = pge.forumID LEFT JOIN forums_permissions_groups pgr ON c.forumID = pgr.forumID LEFT JOIN forums_permissions_users pu ON c.forumID = pu.forumID LEFT JOIN forums_readData_forums rdf ON c.forumID = rdf.forumID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID LEFT JOIN rolls r ON p.postID = r.postID LEFT JOIN deckDraws d ON p.postID = d.postID WHERE f.forumID = {$forumID}");
		echo 1;
	} else echo 0;
?>