<?
	$drawID = intval($_POST['drawID']);
	$drawInfo = $mysql->query("SELECT posts.authorID, posts.postID, posts.threadID, deckDraws.reveals FROM posts, deckDraws WHERE posts.postID = deckDraws.postID AND deckDraws.drawID = $drawID");
	$drawInfo = $drawInfo->fetch();
	if ($currentUser->userID == $drawInfo['authorID']) {
		$position = $_POST['position'];
		if ($drawInfo['reveals'][$position] == 1) $drawInfo['reveals'] = substr_replace($drawInfo['reveals'], '0', $position, 1);
		else $drawInfo['reveals'] = substr_replace($drawInfo['reveals'], '1', $position, 1);
		$mysql->query("UPDATE deckDraws SET reveals = '{$drawInfo['reveals']}' WHERE drawID = $drawID");
	}
	
	header("Location: /forums/thread/{$drawInfo['threadID']}/?p={$drawInfo['postID']}#p{$drawInfo['postID']}");
?>