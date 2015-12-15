<?
	class forums {
		function __construct() {
			global $pathOptions;

			addPackage('forum');

			if ($pathOptions[0] == 'acp') {
				require(APIROOT.'/forumACP.class.php');
				$subcontroller = new forumACP();
			} elseif ($pathOptions[0] == 'save') 
				$this->saveLink();
			elseif ($pathOptions[0] == 'deleteImage') 
				$this->deleteImage();
			elseif ($pathOptions[0] == 'deleteLink') 
				$this->deleteLink();
			elseif ($pathOptions[0] == 'getSubscriptions') 
				$this->getSubscriptions();
			elseif ($pathOptions[0] == 'unsubscribe') 
				$this->unsubscribe();
			else 
				displayJSON(array('failed' => true));
		}

		public function getLinks() {
			global $mongo, $currentUser;

			$search = array();
			if (isset($_POST['level']) && in_array($_POST['level'], array('Link', 'Affiliate', 'Partner'))) 
				$search['level'] = $_POST['level'];
			if (isset($_POST['networks'])) 
				$search['networks'] = $_POST['networks'];

			$page = isset($_POST['page']) && intval($_POST['page'])?intval($_POST['page']):1;
			$numLinks = $mongo->links->find($search, array('_id' => 1))->count();
			if (isset($_POST['page'])) 
				$linksResults = $mongo->links->find($search)->sort(array('title' => 1))->skip(PAGINATE_PER_PAGE * ($page - 1))->limit(PAGINATE_PER_PAGE);
			else 
				$linksResults = $mongo->links->find($search)->sort(array('title' => 1));
			$links = array();
			foreach ($linksResults as $link) {
				$link['_id'] = $link['_id']->{'$id'};
				$links[] = $link;
			}
			displayJSON(array('type' => $type, 'links' => $links, 'totalCount' => $numLinks));
		}

		private function uploadLogo($_id, $logoFile) {
			if ($logoFile['error'] == 0 && $logoFile['size'] > 15 && $logoFile['size'] < 2097152) {
				$logoExt = trim(end(explode('.', strtolower($logoFile['name']))));
				if ($logoExt == 'jpeg') 
					$logoExt = 'jpg';
				if (in_array($logoExt, array('jpg', 'gif', 'png'))) {
					$maxWidth = 300;
					$maxHeight = 300;
					
					list($imgWidth, $imgHeight, $imgType) = getimagesize($logoFile['tmp_name']);
					if ($imgWidth >= $maxWidth || $imgHeight >= $maxHeight) {
						if (image_type_to_mime_type($imgType) == 'image/jpeg' || image_type_to_mime_type($imgType) == 'image/pjpeg') 
							$tempImg = imagecreatefromjpeg($logoFile['tmp_name']);
						elseif (image_type_to_mime_type($imgType) == 'image/gif') 
							$tempImg = imagecreatefromgif($logoFile['tmp_name']);
						elseif (image_type_to_mime_type($imgType) == 'image/png') 
							$tempImg = imagecreatefrompng($logoFile['tmp_name']);
						
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
						foreach (glob(FILEROOT."/images/links/{$_id}.*") as $oldFile) 
							unlink($oldFile);
						if ($logoExt == 'jpg') 
							imagejpeg($tempColor, $destination, 100);
						elseif ($logoExt == 'gif') 
							imagegif($tempColor, $destination);
						elseif ($logoExt == 'png') 
							imagepng($tempColor, $destination, 0);
						imagedestroy($tempImg);
						imagedestroy($tempColor);

						return $logoExt;
					}
				} elseif ($logoExt == 'svg') {
					foreach (glob(FILEROOT."/images/links/{$_id}.*") as $oldFile) 
						unlink($oldFile);
					move_uploaded_file($logoFile['tmp_name'], FILEROOT."/images/links/{$_id}.svg");

					return 'svg';
				}
			}
			return null;
		}

		public function saveLink() {
			global $loggedIn, $mongo;
			if (!$loggedIn) exit;

			$data = array();
			if (isset($_POST['_id'])) 
				$data['_id'] = new MongoId($_POST['_id']);
			else
				$data['_id'] = new MongoId();
			$data['title'] = $_POST['title'];
			$data['sortName'] = strtolower($data['title']);
			$data['url'] = $_POST['url'];
			if (!strlen($data['title']) || !strlen($data['url'])) 
				displayJSON(array('failed' => 'incomplete'));
			$data['level'] = $_POST['level'];
			if (!in_array($data['level'], array_keys($this->levels))) 
				$data['level'] = 'Link';
			if (isset($_FILES['file'])) {
				$ext = $this->uploadLogo($data['_id'], $_FILES['file']);
				if ($ext) 
					$data['image'] = $ext;
			}
			$data['networks'] = array();
			$_POST['networks'] = json_decode(html_entity_decode($_POST['networks']));
			foreach ($_POST['networks'] as $key => $value) 
				if ($value) 
					$data['networks'][] = $key;
			$data['categories'] = array();
			$_POST['categories'] = json_decode(html_entity_decode($_POST['categories']));
			foreach ($_POST['categories'] as $key => $value) 
				if ($value) 
					$data['categories'][] = $key;

			if (!isset($_POST['_id'])) {
				$data['random'] = $mongo->execute('Math.random()');
				$data['random'] = $data['random']['retval'];

				$mongo->links->insert($data);
			} else {
				$mongoID = $data['_id'];
				unset($data['_id']);
				$mongo->links->update(array('_id' => new MongoId($mongoID)), array('$set' => $data));
			}

			displayJSON(array('success' => 1, 'image' => $data['image']));
		}

		public function deleteImage() {
			global $loggedIn, $mongo;
			if (!$loggedIn) exit;

			foreach (glob(FILEROOT."/images/links/{$_POST['_id']}.*") as $oldFile) 
				unlink($oldFile);
			$mongo->links->update(array('_id' => new MongoId($_POST['_id'])), array('$unset' => array('image' => '')));
		}

		public function deleteLink() {
			global $loggedIn, $mongo;
			if (!$loggedIn) exit;

			foreach (glob(FILEROOT."/images/links/{$_POST['_id']}.*") as $file) 
				unlink($file);
			$mongo->links->remove(array('_id' => new MongoId($_POST['_id'])));
		}

		public function getSubscriptions() {
			global $mysql;

			if (isset($_POST['userID'])) {
				$userID = (int) $_POST['userID'];
				$rForums = $mysql->query("SELECT p.forumID, p.title, p.heritage, p.parentID, p.order, IF(s.ID = p.forumID, 1, 0) isSubbed FROM forumSubs s INNER JOIN forums f ON s.ID = f.forumID INNER JOIN forums p ON f.heritage LIKE CONCAT(p.heritage, '%') WHERE p.forumID != 0 AND s.userID = {$userID} AND s.type = 'f' ORDER BY LENGTH(p.heritage), `order`");
				$forums = array();
				foreach ($rForums as $forum) {
					if (!isset($forums[$forum['forumID']])) {
						$forum['forumID'] = (int) $forum['forumID'];
						$forum['title'] = printReady($forum['title']);
						$forum['heritage'] = array_map('intval', explode('-', $forum['heritage']));
						$forum['parentID'] = (int) $forum['parentID'];
						$forum['order'] = (int) $forum['order'];
						$forum['isSubbed'] = (bool) $forum['isSubbed'];
						$forums[(int) $forum['forumID']] = $forum;
					} else 
						if ($forum['isSubbed']) 
							$forums[(int) $forum['forumID']]['isSubbed'] = true;
				}

				$rThreads = $mysql->query("SELECT f.forumID, f.title forumTitle, t.threadID, p.title threadTitle FROM forumSubs s INNER JOIN threads t ON s.ID = t.threadID INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts p ON t.firstPostID = p.postID WHERE s.userID = {$userID} AND s.type = 't' ORDER BY LENGTH(f.heritage), `order`");
				$threads = array();
				foreach ($rThreads as $thread) {
					if (!isset($threads[$thread['forumID']])) 
						$threads[(int) $thread['forumID']] = array(
							'forumID' => (int) $thread['forumID'],
							'title' => printReady($thread['forumTitle']),
							'threads' => array()
						);
					$threads[(int) $thread['forumID']]['threads'][] = array(
						'threadID' => (int) $thread['threadID'],
						'forumID' => (int) $thread['forumID'],
						'title' => printReady($thread['threadTitle'])
					);
				}

				displayJSON(array('success' => true, 'forums' => array_values($forums), 'threads' => array_values($threads)));
			}
		}

		public function unsubscribe() {
			global $mysql;

			$userID = (int) $_POST['userID'];
			if ($_POST['type'] == 'f' || $_POST['type'] == 't') 
				$type = $_POST['type'];
			else 
				displayJSON(array('failed' => true, 'errors' => array('invalidType')));
			$typeID = (int) $_POST['id'];

			$mysql->query("DELETE FROM forumSubs WHERE userID = {$userID} AND type = '{$type}' AND ID = {$typeID} LIMIT 1");

			displayJSON(array('success' => true));
		}
	}
?>