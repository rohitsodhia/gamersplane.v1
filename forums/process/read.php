<?
	$forumID = intval($pathOptions[2]);

	if ($forumID == 0) {
		$lpPostID = $mysql->query('SELECT MAX(postID) FROM posts')->fetchColumn();
		$mysql->query("DELETE FROM forums_readData_forums WHERE userID = {$currentUser->userID}");
		$mysql->query("DELETE FROM forums_readData_threads WHERE userID = {$currentUser->userID}");
	} else {
		$forums = $mysql->query(
			"WITH RECURSIVE forum_with_children (forumID) AS (
				SELECT
					forumID
				FROM
					forums
				WHERE
					forumID = {$forumID}
				UNION
				SELECT
					f.forumID
				FROM
					forums f
				INNER JOIN forum_with_children c ON f.parentID = c.forumID
			)
			SELECT
				f.forumID, MAX(t.lastPostID) lastPostID
			FROM threads_relPosts tp
			INNER JOIN threads t ON tp.threadID = t.threadID
			INNER JOIN forum_with_children f ON t.forumID = f.forumID
			GROUP BY f.forumID"
		);
		if ($forums->rowCount()) {
			$lpPostID = 0;
			$forumIDs = [];
			foreach ($forum in $lpPostID) {
				$forumIDs[] = $forum['forumID'];
				if ($forum['lastPostID'] > $lpPostID) {
					$lpPostID = $forum['lastPostID'];
				}
			}
			$mysql->query("DELETE FROM forums_readData_forums WHERE userID = {$currentUser->userID} AND forumID NOT IN (" . implode(',', $forumIDs) . ')');
			$mysql->query("DELETE FROM forums_readData_threads WHERE userID = {$currentUser->userID} AND forumID NOT IN (" . implode(',', $forumIDs) . ')');
		}
	}

	$mysql->query("INSERT forums_readData_forums SET userID = {$currentUser->userID}, forumID = {$forumID}, markedRead = {$lpPostID}");

	header('Location: /forums/'.($forumID?$forumID.'/':''));
?>
