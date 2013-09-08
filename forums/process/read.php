<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($pathOptions[2]);
	
	if ($forumID == 0) $lpPostID = $mysql->query('SELECT MAX(postID) FROM posts');
	else $lpPostID = $mysql->query('SELECT postID FROM (SELECT parent.forumID, postID FROM forums parent, forums child, (SELECT postID, forumID FROM posts, threads WHERE posts.threadID = threads.threadID ORDER BY posts.datePosted DESC) lastPost WHERE child.heritage LIKE CONCAT("%", LPAD(parent.forumID, 3, "0"), "%") AND lastPost.forumID = child.forumID ORDER BY postID DESC) lastPost WHERE forumID = '.$forumID.' GROUP BY forumID');
	$lpPostID = $mysql->fetchColumn();
	
	$mysql->query('DELETE rdf FROM forums f INNER JOIN forums c INNER JOIN forums_readData_forums rdf WHERE f.forumID = '.$forumID.' AND c.forumID = rdf.forumID AND rdf.userID = '.$userID.' AND c.heritage LIKE CONCAT(f.heritage, "%")');
	$mysql->query('DELETE rdt FROM forums f INNER JOIN forums c INNER JOIN threads t INNER JOIN forums_readData_threads rdt WHERE f.forumID = '.$forumID.' AND c.heritage LIKE CONCAT(f.heritage, "%") AND c.forumID = t.forumID AND t.threadID = rdt.threadID AND rdt.userID = '.$userID);
	$mysql->query('INSERT forums_readData_forums SET userID = '.$userID.', forumID = '.$forumID.', lastRead = '.$lpPostID);
	
	header('Location: '.SITEROOT.'/forums/'.($forumID?$forumID:''));
?>