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
				$rForums = $mysql->query(
					"WITH RECURSIVE subbed_forums (forumID, isSubbed) AS (
						SELECT
							ID,
							1 as isSubbed
						FROM
							forumSubs
						WHERE
							userID = {$userID} AND `type` = 'f'
					),
					forum_with_parents (forumID, title, parentID, `order`, depth) as (
						SELECT
							f.forumID,
							f.title,
							f.parentID,
							f.`order`,
							f.depth
						FROM
							forums f
						INNER JOIN subbed_forums s ON f.forumID = s.forumID
						UNION
						SELECT
							p.forumID,
							p.title,
							p.parentID,
							p.`order`,
							p.depth
						FROM
							forums p
						INNER JOIN forum_with_parents ON p.forumID = forum_with_parents.parentID
					)
					SELECT
						f.forumID,
						f.title,
						f.parentID,
						f.`order`,
						IF(s.forumID = f.forumID, 1, 0) isSubbed
					FROM
						forum_with_parents f
						LEFT JOIN subbed_forums s ON f.forumID = s.forumID
						WHERE f.forumID != 0
					ORDER BY depth, `order`;"
				);
				$forums = [];
				foreach ($rForums as $forum) {
					if (!isset($forums[$forum['forumID']])) {
						$forum['forumID'] = (int) $forum['forumID'];
						$forum['title'] = printReady($forum['title']);
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

				$rThreads = $mysql->query(
					"SELECT
						f.forumID, f.title forumTitle, t.threadID, p.title threadTitle
					FROM forumSubs s
					INNER JOIN threads t ON s.ID = t.threadID
					INNER JOIN forums f ON t.forumID = f.forumID
					INNER JOIN posts p ON t.firstPostID = p.postID
					WHERE
						s.userID = {$userID} AND s.`type` = 't'
					ORDER BY depth, `order`
				");
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

			$mysql->query("DELETE FROM forumSubs WHERE userID = {$userID} AND `type` = '{$type}' AND ID = {$typeID} LIMIT 1");

			displayJSON(['success' => true]);
		}

		public function setLastPostUnread($threadID) {
			global $currentUser;
			$mysql = DB::conn('mysql');
			$lastPosts = $mysql->query("SELECT p.postID, t.forumID FROM posts p INNER JOIN threads t ON p.threadID = t.threadID WHERE p.threadID = {$threadID} ORDER BY p.datePosted DESC LIMIT 2");

			if ($lastPosts->rowCount() == 2) {
				list($lastPostID, $forumID) = $lastPosts->fetch(PDO::FETCH_NUM);
				$newLastPostRead = $lastPosts->fetchColumn();
				$mysql->query("UPDATE forums_readData_threads SET lastRead = {$newLastPostRead} WHERE threadID = {$threadID} AND userID = {$currentUser->userID} LIMIT 1");
				$markedRead = $mysql->query("SELECT markedRead FROM forums_readData_forums WHERE userID = {$currentUser->userID} AND forumID = {$forumID} LIMIT 1")->fetchColumn();
				if ($markedRead > $lastPostID) {
					$mysql->query("UPDATE forums_readData_forums SET markedRead = {$lastPostID} - 1 WHERE userID = {$currentUser->userID} AND forumID = {$forumID} LIMIT 1");}
			} else {
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
					$mysql->query("DELETE FROM forums_postPollVotes WHERE postID = {$postID} AND userID = {$currentUser->userID} AND vote = {$vote} LIMIT 1");
				} else{
					$mysql->query("DELETE FROM forums_postPollVotes WHERE postID = {$postID} AND userID = {$currentUser->userID}");
				}

				if($addVote || !$isMulti){
					$mysql->query("INSERT INTO forums_postPollVotes SET postID = {$postID}, userID = {$currentUser->userID}, vote = {$vote}");
				}

				return $post->getPollResults($isPublic);
			}
			else {
				return null;
			}
		}

		public function ffgFlip(int $postID, bool $toDark, $totalFlips, $tokens){
			global $currentUser;
			$mysql = DB::conn('mysql');
			$post = new Post($postID);
			$toDark = $toDark ? 1 : 0;
			$threadManager = new ThreadManager($post->getThreadID());

			if ($threadManager->getPermissions('write')){
				$flips=$post->getFfgDestinyResults($tokens);
				if(count($flips['flips'])==$totalFlips){
					$mysql->query("INSERT INTO forums_postFFGFlips SET postID = {$postID}, userID = {$currentUser->userID}, toDark = {$toDark}");
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
