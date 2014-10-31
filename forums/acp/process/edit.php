<?
	$forumID = intval($_POST['forumID']);
	
	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	if (!$isAdmin->rowCount()) { header('Location: /forums/'); exit; }
	
	$toDo = '';
	$actionKey = '';
	if (isset($_POST['update'])) {
		$newTitle = isset($_POST['title'])?sanitizeString($_POST['title']):$title;
		$newDesc = sanitizeString($_POST['description']);
		
		if ($newTitle != $title || $newDesc != $description) {
			$updateForum = $mysql->prepare("UPDATE forums SET title = :newTitle, description = :newDesc WHERE forumID = $forumID");
			$updateForum->bindValue(':newTitle', $newTitle);
			$updateForum->bindValue(':newDesc', $newDesc);
			$updateForum->execute();
		}
		
		header('Location: /forums/acp/'.$forumID);
	} else header('Location: /forums/');
?>