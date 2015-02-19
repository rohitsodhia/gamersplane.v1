<?
	$forumID = intval($pathOptions[2]);
	
	if ($forumID == 0) $lpPostID = $mysql->query('SELECT MAX(postID) FROM posts');
	else $lpPostID = $mysql->query("SELECT MAX(tp.lastPostID) lastPostID FROM threads_relPosts tp INNER JOIN threads t ON tp.threadID = t.threadID INNER JOIN forums c ON t.forumID = c.forumID INNER JOIN forums p ON c.heritage LIKE CONCAT(p.heritage, '%') WHERE p.forumID = $forumID");
	$lpPostID = $lpPostID->fetchColumn();

	$mysql->query('DELETE rdf FROM forums f INNER JOIN forums c INNER JOIN forums_readData_forums rdf WHERE f.forumID = '.$forumID.' AND c.forumID = rdf.forumID AND rdf.userID = '.$currentUser->userID.' AND c.heritage LIKE CONCAT(f.heritage, "%")');
	$mysql->query('DELETE rdt FROM forums f INNER JOIN forums c INNER JOIN threads t INNER JOIN forums_readData_threads rdt WHERE f.forumID = '.$forumID.' AND c.heritage LIKE CONCAT(f.heritage, "%") AND c.forumID = t.forumID AND t.threadID = rdt.threadID AND rdt.userID = '.$currentUser->userID);
	$mysql->query('INSERT forums_readData_forums SET userID = '.$currentUser->userID.', forumID = '.$forumID.', lastRead = '.$lpPostID);
	
	header('Location: /forums/'.($forumID?$forumID.'/':''));
?>