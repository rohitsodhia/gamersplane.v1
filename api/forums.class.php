<?php
	class forums {
		function __construct() {
			global $pathOptions;

			addPackage('forum');

			if ($pathOptions[0] == 'acp') {
				require(APIROOT.'/forumACP.class.php');
				$subcontroller = new forumACP();
			} elseif ($pathOptions[0] == 'save') {
				$this->saveLink();
			} elseif ($pathOptions[0] == 'deleteImage') {
				$this->deleteImage();
			} elseif ($pathOptions[0] == 'deleteLink') {
				$this->deleteLink();
			} elseif ($pathOptions[0] == 'getSubscriptions') {
				$this->getSubscriptions();
			} elseif ($pathOptions[0] == 'unsubscribe') {
				$this->unsubscribe();
			} elseif ($pathOptions[0] == 'setLastPostUnread') {
				$this->setLastPostUnread($_POST['threadID']);
			} elseif ($pathOptions[0] == 'getPostQuote') {
				displayJSON($this->getPostQuote($_POST['postID']));
			} elseif ($pathOptions[0] == 'getPostPreview') {
				displayJSON($this->getPostPreview($_POST['postText']));
			} elseif ($pathOptions[0] == 'pollVote') {
				displayJSON($this->pollVote( $_POST['postId'], $_POST['vote'], $_POST['addVote'], $_POST['isMulti']));
			} elseif ($pathOptions[0] == 'ftReindex') {
				displayJSON($this->ftReindex( $_POST['fromId'], $_POST['toId']));
			}else {
				displayJSON(['failed' => true]);
			}
		}

		public function getLinks() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$search = [];
			if (isset($_POST['level']) && in_array($_POST['level'], ['Link', 'Affiliate', 'Partner'])) {
				$search['level'] = $_POST['level'];
			}
			if (isset($_POST['networks'])) {
				$search['networks'] = $_POST['networks'];
			}

			$page = isset($_POST['page']) && intval($_POST['page']) ? intval($_POST['page']) : 1;
			$numLinks = $mongo->links->count($search);
			if (isset($_POST['page'])) {
				$linksResults = $mongo->links->find(
					$search,
					[
						'sort' => ['title' => 1],
						'skip' => PAGINATE_PER_PAGE * ($page - 1),
						'limit' => PAGINATE_PER_PAGE
					]
				);
			} else {
				$linksResults = $mongo->links->find(
					$search,
					['sort' => ['title' => 1]]
				);
			}
			$links = [];
			foreach ($linksResults as $link) {
				$link['_id'] = (string) $link['_id'];
				$links[] = $link;
			}
			displayJSON([
				'type' => $type,
				'links' => $links,
				'totalCount' => $numLinks
			]);
		}

		private function uploadLogo($_id, $logoFile) {
			if ($logoFile['error'] == 0 && $logoFile['size'] > 15 && $logoFile['size'] < 2097152) {
				$logoExt = trim(end(explode('.', strtolower($logoFile['name']))));
				if ($logoExt == 'jpeg') {
					$logoExt = 'jpg';
				}
				if (in_array($logoExt, ['jpg', 'gif', 'png'])) {
					$maxWidth = 300;
					$maxHeight = 300;

					list($imgWidth, $imgHeight, $imgType) = getimagesize($logoFile['tmp_name']);
					if ($imgWidth >= $maxWidth || $imgHeight >= $maxHeight) {
						if (image_type_to_mime_type($imgType) == 'image/jpeg' || image_type_to_mime_type($imgType) == 'image/pjpeg') {
							$tempImg = imagecreatefromjpeg($logoFile['tmp_name']);
						} elseif (image_type_to_mime_type($imgType) == 'image/gif') {
							$tempImg = imagecreatefromgif($logoFile['tmp_name']);
						} elseif (image_type_to_mime_type($imgType) == 'image/png') {
							$tempImg = imagecreatefrompng($logoFile['tmp_name']);
						}

						$xRatio = $maxWidth / $imgWidth;
						$yRatio = $maxHeight / $imgHeight;

						if ($imgWidth <= $maxWidth && $imgHeight <= $maxHeight) {
							$finalWidth = $imgWidth;
							$finalHeight = $imgHeight;
						} elseif (($xRatio * $imgHeight) < $maxHeight) {
							$finalWidth = $maxWidth;
							$finalHeight = ceil($xRatio * $imgHeight);
						} else {
							$finalWidth = ceil($yRatio * $imgWidth);
							$finalHeight = $maxHeight;
						}

						$tempColor = imagecreatetruecolor($finalWidth, $finalHeight);
						imagealphablending($tempColor, false);
						imagesavealpha($tempColor,true);
						imagecopyresampled($tempColor, $tempImg, 0, 0, 0, 0, $finalWidth, $finalHeight, $imgWidth, $imgHeight);

						$destination = FILEROOT."/images/links/{$_id}.{$logoExt}";
						foreach (glob(FILEROOT."/images/links/{$_id}.*") as $oldFile) {
							unlink($oldFile);
						}
						if ($logoExt == 'jpg') {
							imagejpeg($tempColor, $destination, 100);
						} elseif ($logoExt == 'gif') {
							imagegif($tempColor, $destination);
						} elseif ($logoExt == 'png') {
							imagepng($tempColor, $destination, 0);
						}
						imagedestroy($tempImg);
						imagedestroy($tempColor);

						return $logoExt;
					}
				} elseif ($logoExt == 'svg') {
					foreach (glob(FILEROOT."/images/links/{$_id}.*") as $oldFile) {
						unlink($oldFile);
					}
					move_uploaded_file($logoFile['tmp_name'], FILEROOT."/images/links/{$_id}.svg");

					return 'svg';
				}
			}

			return null;
		}

		public function saveLink() {
			global $loggedIn;
			$mongo = DB::conn('mongo');

			if (!$loggedIn) {
				exit;
			}

			$data = [];
			$data['_id'] = genMongoId($_POST['_id'] ?? null);
			$data['title'] = $_POST['title'];
			$data['sortName'] = strtolower($data['title']);
			$data['url'] = $_POST['url'];
			if (!strlen($data['title']) || !strlen($data['url'])) {
				displayJSON(array('failed' => 'incomplete'));
			}
			$data['level'] = $_POST['level'];
			if (!in_array($data['level'], array_keys($this->levels))) {
				$data['level'] = 'Link';
			}
			if (isset($_FILES['file'])) {
				$ext = $this->uploadLogo($data['_id'], $_FILES['file']);
				if ($ext) {
					$data['image'] = $ext;
				}
			}
			$data['networks'] = [];
			$_POST['networks'] = json_decode(html_entity_decode($_POST['networks']));
			foreach ($_POST['networks'] as $key => $value) {
				if ($value) {
					$data['networks'][] = $key;
				}
			}
			$data['categories'] = [];
			$_POST['categories'] = json_decode(html_entity_decode($_POST['categories']));
			foreach ($_POST['categories'] as $key => $value) {
				if ($value) {
					$data['categories'][] = $key;
				}
			}

			if (!isset($_POST['_id'])) {
				$data['random'] = randomFloat();

				$mongo->links->insertOne($data);
			} else {
				$mongoID = $data['_id'];
				unset($data['_id']);
				$mongo->links->updateOne(['_id' => genMongoId($mongoID)], ['$set' => $data]);
			}

			displayJSON(['success' => 1, 'image' => $data['image']]);
		}

		public function deleteImage() {
			global $currentUser;
			$mongo = DB::conn('mongo');
			if (!$loggedIn) {
				exit;
			}

			foreach (glob(FILEROOT."/images/links/{$_POST['_id']}.*") as $oldFile) {
				unlink($oldFile);
			}
			$mongo->links->updateOne(['_id' => genMongoId($_POST['_id'])], ['$unset' => ['image' => '']]);
		}

		public function deleteLink() {
			global $currentUser;
			$mongo = DB::conn('mongo');
			if (!$loggedIn) {
				exit;
			}

			foreach (glob(FILEROOT."/images/links/{$_POST['_id']}.*") as $file) {
				unlink($file);
			}
			$mongo->links->deleteOne(['_id' => genMongoId($_POST['_id'])]);
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

			global $currentUser,$mongo;

			$ret = '';

			$post = new Post($postID);
			$threadManager = new ThreadManager($post->getThreadID());

			if (!$threadManager->getThreadProperty('states[locked]')  && $threadManager->getPermissions('write')){
				$gameID = $threadManager->forumManager->forums[$threadManager->getThreadProperty('forumID')]->gameID;
				if ($gameID) {
					$game = $mongo->games->findOne(
						[
							'gameID' => (int) $gameID,
							'players' => ['$elemMatch' => [
								'user.userID' => $currentUser->userID,
								'isGM' => true
							]]
						],
						['projection' => ['players.$' => true]]
					);
					$isGM = $game['players'][0]['isGM'];
					if (!$isGM) {
						$ret = Post::cleanNotes($post->message);
					}
					else{
						$ret = $post->message;
					}
				}
				else{
					$ret = Post::cleanNotes($post->message);
				}

				$ret='[quote="'.$post->getAuthor('username').'"]'.$ret.'[/quote]';

			}

			return $ret;
		}

		public function getPostPreview($postText){
			global $isGM,$post;
			$isGM=true;
			$post = new Post(0);
			return printReady(BBCode2Html($postText));
		}

		public function pollVote($postID, $vote, $addVote, $isMulti){
			global $currentUser;
			$mongo = DB::conn('mongo');
			$post = new Post($postID);
			$threadManager = new ThreadManager($post->getThreadID());

			if ($threadManager->getPermissions('write')){

				if($isMulti){
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
	}
?>
