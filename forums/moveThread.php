<?php
	$responsivePage=true;
	function showOptions($forumID, $indent = 0) {
		global $forumManager;
		$forum = $forumManager->forums[$forumID];
		if ($forum == null)
			return;
?>
				<option value="<?=$forumID?>"<?=$forum->getPermissions('admin') === true ? '' : ' disabled="disabled"'?>><?=str_repeat('-', $indent)?><?=$forum->title?></option>
<?php
		foreach ($forum->getChildren() as $cForumID) {
			showOptions($cForumID, $indent + 1);
		}
	}

	addPackage('forum');
	$threadID = intval($pathOptions[1]);
	if (!$threadID) { header('Location: /forums'); exit; }

	$threadManager = new ThreadManager($threadID);
	if ($threadManager->getPermissions('admin') == false) { header('Location: /403'); exit; }

	if ($threadManager->isGameForum()) {
		$gameID = $threadManager->getForumProperty('gameID');
		$startForum = $mysql->query("SELECT forumID FROM games WHERE gameID = {$gameID} LIMIT 1")->fetchColumn();
	} else {
		$startForum = 0;
	}
	$forumManager = new ForumManager($startForum, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);

	require_once(FILEROOT . '/header.php');
?>
		<h1 class="headerbar">Move Thread</h1>
		<div class="hbMargined">
			<p>Where would you like to move the thread to?</p>

			<form method="post" action="/forums/process/moveThread/">
				<input type="hidden" name="threadID" value="<?=$threadID?>">
				<select name="forumID">
<?php	showOptions($startForum); ?>
				</select>
				<div class="tr">
					<button type="submit" name="add" class="fancyButton">Move</button>
					<button type="submit" name="cancel" class="fancyButton">Cancel</button>
				</div>
			</form>
		</div>
<?php	require_once(FILEROOT . '/footer.php'); ?>
