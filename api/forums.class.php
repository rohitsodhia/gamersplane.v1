<?php
	require_once(FILEROOT . '/includes/characters/Character.class.php');

	class forums {
		function __construct() {
			global $pathOptions;

			addPackage('forum');

			if ($pathOptions[0] == 'acp') {
				require(APIROOT.'/forumACP.class.php');
				$subcontroller = new forumACP();
			} elseif ($pathOptions[0] == 'getSubscriptions') {
				$this->getSubscriptions();
			} elseif ($pathOptions[0] == 'unsubscribe') {
				$this->unsubscribe();
			} elseif ($pathOptions[0] == 'setLastPostUnread') {
				$this->setLastPostUnread($_POST['threadID']);
			} elseif ($pathOptions[0] == 'getPostQuote') {
				displayJSON($this->getPostQuote($_POST['postID']));
			} elseif ($pathOptions[0] == 'getPostPreview') {
				displayJSON($this->getPostPreview($_POST['postText'],$_POST['postAsId'], $_POST['postAsName']));
			} elseif ($pathOptions[0] == 'pollVote') {
				displayJSON($this->pollVote( $_POST['postId'], $_POST['vote'], $_POST['addVote'], $_POST['isMulti'], $_POST['isPublic']));
			} elseif ($pathOptions[0] == 'ffgFlip') {
				displayJSON($this->ffgFlip( $_POST['postId'], $_POST['toDark'], $_POST['totalFlips'], $_POST['tokens']));
//			} elseif ($pathOptions[0] == 'ftReindex') {
//				displayJSON($this->ftReindex( $_POST['fromId'], $_POST['toId']));
			}else {
				displayJSON(['failed' => true]);
			}
		}

		public function getSubscriptions() {
			$mysql = DB::conn('mysql');

			if (isset($_POST['userID'])) {
				$userID = (int) $_POST['userID'];
				$rForums = $mysql->query("SELECT p.forumID, p.title, p.heritage, p.parentID, p.order, IF(s.ID = p.forumID, 1, 0) isSubbed FROM forumSubs s INNER JOIN forums f ON s.ID = f.forumID INNER JOIN forums p ON f.heritage LIKE CONCAT(p.heritage, '%') WHERE p.forumID != 0 AND s.userID = {$userID} AND s.type = 'f' ORDER BY LENGTH(p.heritage), `order`");
				$forums = [];
				foreach ($rForums as $forum) {
					if (!isset($forums[$forum['forumID']])) {
						$forum['forumID'] = (int) $forum['forumID'];
						$forum['title'] = printReady($forum['title']);
						$forum['heritage'] = array_map('intval', explode('-', $forum['heritage']));
						$forum['parentID'] = (int) $forum['parentID'];
						$forum['order'] = (int) $forum['order'];
						$forum['isSubbed'] = (bool) $forum['isSubbed'];
						$forums[(int) $forum['forumID']] = $forum;
					} else {
						if ($forum['isSubbed']) {
							$forums[(int) $forum['forumID']]['isSubbed'] = true;
						}
					}
				}

				$rThreads = $mysql->query("SELECT f.forumID, f.title forumTitle, t.threadID, p.title threadTitle FROM forumSubs s INNER JOIN threads t ON s.ID = t.threadID INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts p ON t.firstPostID = p.postID WHERE s.userID = {$userID} AND s.type = 't' ORDER BY LENGTH(f.heritage), `order`");
				$threads = [];
				foreach ($rThreads as $thread) {
					if (!isset($threads[$thread['forumID']])) {
						$threads[(int) $thread['forumID']] = [
							'forumID' => (int) $thread['forumID'],
							'title' => printReady($thread['forumTitle']),
							'threads' => []
						];
					}
					$threads[(int) $thread['forumID']]['threads'][] = [
						'threadID' => (int) $thread['threadID'],
						'forumID' => (int) $thread['forumID'],
						'title' => printReady($thread['threadTitle'])
					];
				}

				displayJSON(['success' => true, 'forums' => array_values($forums), 'threads' => array_values($threads)]);
			}
		}

		public function unsubscribe() {
			$mysql = DB::conn('mysql');

			$userID = (int) $_POST['userID'];
			if ($_POST['type'] == 'f' || $_POST['type'] == 't') {
				$type = $_POST['type'];
			} else {
				displayJSON(['failed' => true, 'errors' => ['invalidType']]);
			}
			$typeID = (int) $_POST['id'];

			$mysql->query("DELETE FROM forumSubs WHERE userID = {$userID} AND type = '{$type}' AND ID = {$typeID} LIMIT 1");

			displayJSON(['success' => true]);
		}

		public function setLastPostUnread($threadID){
			global $currentUser;
			$mysql = DB::conn('mysql');
			$lastPosts=$mysql->query("SELECT postID FROM posts WHERE threadID = {$threadID} ORDER BY datePosted DESC LIMIT 2");

			if($lastPosts->rowCount()==2){
				$lastPosts->fetch(PDO::FETCH_OBJ);
				$lastPostRead=$lastPosts->fetch(PDO::FETCH_OBJ);
				$mysql->query("UPDATE forums_readData_threads SET lastRead = {$lastPostRead->postID} WHERE threadID = {$threadID} AND userID = {$currentUser->userID}");
			}
			else{
				$mysql->query("DELETE FROM forums_readData_threads WHERE threadID = {$threadID} AND userID = {$currentUser->userID}");
			}
		}

		public function getPostQuote($postID){
			global $currentUser;
			$mysql = DB::conn('mysql');

			$ret = '';

			$post = new Post($postID);
			$threadManager = new ThreadManager($post->getThreadID());

			if (!$threadManager->getThreadProperty('states[locked]')  && $threadManager->getPermissions('write')){
				$gameID = $threadManager->forumManager->forums[$threadManager->getThreadProperty('forumID')]->gameID;
				if ($gameID) {
					$checkIsGM = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID}")->fetchColumn();
					$ret = $checkIsGM ? $post->message : Post::cleanNotes($post->message);
				} else {
					$ret = Post::cleanNotes($post->message);
				}

				$ret = '[quote="' . $post->getAuthor('username') . '"]' . $ret . '[/quote]';
			}

			return $ret;
		}

		public function getPostPreview($postText, $postAsId, $postAsName){
			global $isGM, $post, $currentUser;
			$isGM=true;
			$post = new Post(0);
			$ret = null;

			$postAsId= ($postAsId=='p') ? false:$postAsId;  //coalesce 'p' to false

			if($postAsId){
				$avatar = Character::getCharacterAvatar($postAsId,true);
				$avatar = $avatar ? $avatar : User::getAvatar($currentUser->userID);
				$ret=array('post' => printReady(BBCode2Html($postText),['nl2br']), 'avatar'=>$avatar, 'name'=>$postAsName, 'npcPoster'=>false);
			} else {
				$npc = Post::extractPostingNpc($postText);

				if ($npc) {
					$ret = array('post' => printReady(BBCode2Html($postText),['nl2br']), 'avatar'=>$npc["avatar"], 'name'=>$npc["name"],'npcPoster'=>true);
				} else {
					$ret = array('post' => printReady(BBCode2Html($postText),['nl2br']),'avatar'=> User::getAvatar($currentUser->userID),'name'=>$currentUser->username,'npcPoster'=>false);
				}
			}
			return $ret;
		}

		public function pollVote($postID, $vote, $addVote, $isMulti, $isPublic) {
			global $currentUser;
			$mysql = DB::conn('mysql');
			$post = new Post($postID);
			$threadManager = new ThreadManager($post->getThreadID());

			if ($threadManager->getPermissions('write')) {
				if ($isMulti) {
					$mongo->threads->updateOne(
						['threadID' => ((int)$post->getThreadID())],
						['$pull' => [
							'votes' => [
								'postID' => (int)$postID,
								'userID' => $currentUser->userID,
								'vote' => (int)$vote
							]
						]],
						['upsert' => true]
					);
				}
				else{
					$mongo->threads->updateOne(
						['threadID' => ((int)$post->getThreadID())],
						['$pull' => [
							'votes' => [
								'postID' => (int)$postID,
								'userID' => $currentUser->userID
							]
						]],
						['upsert' => true]
					);
				}


				if($addVote || !$isMulti){
					$mongo->threads->updateOne(
						['threadID' => ((int)$post->getThreadID())],
						['$push' => [
							'votes' => [
								'postID' => (int)$postID,
								'userID' => $currentUser->userID,
								'vote' => (int)$vote,
								'username' => ($isPublic ? $currentUser->username : null)
							]
						]],
						['upsert' => true]
					);
				}

				return $post->getPollResults();
			}
			else {
				return null;
			}
		}

		public function ffgFlip($postID, $toDark, $totalFlips, $tokens){
			global $currentUser;
			$mongo = DB::conn('mongo');
			$post = new Post($postID);
			$threadManager = new ThreadManager($post->getThreadID());

			if ($threadManager->getPermissions('write')){
				$flips=$post->getFfgDestinyResults($tokens);
				if(count($flips['flips'])==$totalFlips){
					$mongo->threads->updateOne(
						['threadID' => ((int)$post->getThreadID())],
						['$push' => [
							'ffgTokens' => [
								'postID' => (int)$postID,
								'userID' => $currentUser->userID,
								'username' => $currentUser->username,
								'toDark' => (int)$toDark,
								'datetime'=>genMongoDate()
							]
						]],
						['upsert' => true]
					);
					$flips=$post->getFfgDestinyResults($tokens);
					$flips['success']=1;
				}

				return $flips;
			}
			else {
				return null;
			}
		}

		/*
		public function ftReindex($fromId, $toId){
			global $mysql;

			for($i = $fromId; $i < $toId; $i++){
				$message = $mysql->query("SELECT message FROM posts WHERE postID = {$i}")->fetchColumn();
				$message = Post::extractFullText($message);
				$updatePost = $mysql->prepare("UPDATE posts SET messageFullText = :messageFullText WHERE postID = {$i}");
				$updatePost->bindValue(':messageFullText', $message);
				$updatePost->execute();
			}

			return null;
		}
		*/
	}
?>
