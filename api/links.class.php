<?php
	class links {
		public $levels = ['Link', 'Affiliate', 'Partner'];

		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'get') {
				$this->get();
			} elseif ($pathOptions[0] == 'save') {
				$this->saveLink();
			} elseif ($pathOptions[0] == 'deleteImage'){
				$this->deleteImage();
			} elseif ($pathOptions[0] == 'deleteLink') {
				$this->deleteLink();
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function get() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$search = [];
			if (isset($_POST['level'])) {
				if (!is_array($_POST['level'])) {
					$_POST['level'] = [$_POST['level']];
				}
				foreach ($_POST['level'] as $level) {
					if (in_array($level, self::$levels)) {
						$search['level'][] = $level;
					}
				}
				if (isset($search['level'])) {
					$search['level'] = ['$in' => $search['level']];
				}
			}
			if (isset($_POST['networks'])) {
				$search['networks'] = $_POST['networks'];
			}
			if (isset($_POST['or'])) {
				$or = [];
				foreach ($search as $key => $value) {
					$or[] = [$key => $value];
				}
				$search = ['$or' => $or];
			}

			$page = isset($_POST['page']) && intval($_POST['page']) ? intval($_POST['page']) : 1;
			$numLinks = count($mongo->links->find($search, ['projection' => ['_id' => 1]]));
			if (isset($_POST['page'])) {
				$linksResults = $mongo->links->find(
					$search,
					[
						'sort' => ['title' => 1],
						'skip' => PAGINATE_PER_PAGE * ($page - 1,
						'limit' => PAGINATE_PER_PAGE
					]
				);
			} else {
				$linksResults = $mongo->links->find($search, ['sort' => ['title' => 1]]);
			}
			$links = [];
			foreach ($linksResults as $rawLink) {
				$link['_id'] = $rawLink['_id']->{'$id'};
				$link['title'] = $rawLink['title'];
				$link['url'] = $rawLink['url'];
				$link['level'] = $rawLink['level'];
				$link['networks'] = is_array($rawLink['networks']) ? $rawLink['networks'] : [];
				$link['categories'] = is_array($rawLink['categories']) ? $rawLink['categories'] : [];
				$link['image'] = $rawLink['image'];
				$links[] = $link;
			}
			displayJSON(['links' => $links, 'totalCount' => $numLinks]);
		}

		private function uploadLogo($_id, $logoFile) {
			if ($logoFile['error'] == 0 && $logoFile['size'] > 15 && $logoFile['size'] < 2097152) {
				$logoExt = trim(end(explode('.', strtolower($logoFile['name']))));
				if ($logoExt == 'jpeg') {
					$logoExt = 'jpg';
				}
				if (in_array($logoExt, array('jpg', 'gif', 'png'))) {
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

						$destination = FILEROOT . '/images/links/' . $_id . '.' . $logoExt;
						foreach (glob(FILEROOT . '/images/links/' . $_id . '.*') as $oldFile) {
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
					foreach (glob(FILEROOT . '/images/links/' . $_id . '.*') as $oldFile) {
						unlink($oldFile);
					}
					move_uploaded_file($logoFile['tmp_name'], FILEROOT . "/images/links/{$_id}.svg");

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
			$errors = [];
			if (isset($_POST['_id'])) {
				$data['_id'] = genMongoId($_POST['_id']);
			} else {
				$data['_id'] = genMongoId();
			}
			$data['title'] = $_POST['title'];
			$data['sortName'] = strtolower($data['title']);
			$data['url'] = $_POST['url'];
			if (!strlen($data['title']) || !strlen($data['url'])) {
				displayJSON(['failed' => 'incomplete']);
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
			$data['networks'] = sizeof($_POST['networks']) ? $_POST['networks'] : [];
			$data['categories'] = sizeof($_POST['categories']) ? $_POST['categories'] : [];

			if (!isset($_POST['_id'])) {
				$data['random'] = randomFloat();

				$mongo->links->insertOne($data);
			} else {
				$mongoID = $data['_id'];
				unset($data['_id']);
				$mongo->links->updateOne(['_id' => genMongoId($mongoID)], ['$set' => $data]);
			}

			displayJSON(['success' => true, 'image' => $data['image']]);
		}

		public function deleteImage() {
			global $loggedIn;
			$mongo = DB::conn('mongo');
			if (!$loggedIn) {
				exit;
			}

			foreach (glob(FILEROOT . "/images/links/{$_POST['_id']}.*") as $oldFile) {
				unlink($oldFile);
			}
			$mongo->links->updateOne(['_id' => genMongoId($_POST['_id'])], ['$unset' => ['image' => '']]);
		}

		public function deleteLink() {
			global $loggedIn;
			$mongo = DB::conn('mongo');
			if (!$loggedIn) {
				exit;
			}

			foreach (glob(FILEROOT . "/images/links/{$_POST['_id']}.*") as $file) {
				unlink($file);
			}
			$mongo->links->remove(['_id' => genMongoId($_POST['_id'])]);
		}
	}
?>
