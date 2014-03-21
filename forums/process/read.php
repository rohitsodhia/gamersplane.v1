<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($pathOptions[2]);
	
	if ($forumID == 0) $lpPostID = $mysql->query('SELECT MAX(postID) FROM posts');
	else $lpPostID = $mysql->query("SELECT IF(mrdt.forumID IS NULL OR rdf.cLastRead > mrdt.cLastRead, rdf.cLastRead, mrdt.cLastRead) lastRead FROM forums_readData_forums_c rdf LEFT JOIN (SELECT forumID, MAX(lastRead) cLastRead FROM forums_readData_threads rdt, threads t WHERE rdt.threadID = t.threadID AND rdt.userID = $userID) mrdt ON rdf.forumID = mrdt.forumID WHERE rdf.userID = $userID AND rdf.forumID = $forumID");
	$lpPostID = $lpPostID->fetchColumn();
	
	$mysql->query('DELETE rdf FROM forums f INNER JOIN forums c INNER JOIN forums_readData_forums rdf WHERE f.forumID = '.$forumID.' AND c.forumID = rdf.forumID AND rdf.userID = '.$userID.' AND c.heritage LIKE CONCAT(f.heritage, "%")');
	$mysql->query('DELETE rdt FROM forums f INNER JOIN forums c INNER JOIN threads t INNER JOIN forums_readData_threads rdt WHERE f.forumID = '.$forumID.' AND c.heritage LIKE CONCAT(f.heritage, "%") AND c.forumID = t.forumID AND t.threadID = rdt.threadID AND rdt.userID = '.$userID);
	$mysql->query('INSERT forums_readData_forums SET userID = '.$userID.', forumID = '.$forumID.', lastRead = '.$lpPostID);
	
	header('Location: '.SITEROOT.'/forums/'.($forumID?$forumID:''));
?>