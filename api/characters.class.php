<?php
	require_once(FILEROOT.'/includes/Systems.class.php');

	class characters {
		public static $charTypes = array('PC', 'NPC', 'Mob');

		public function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'library') {
				$this->library();
			} elseif ($pathOptions[0] == 'my') {
				$this->my();
			} elseif ($pathOptions[0] == 'new') {
				$this->newChar();
			} elseif ($pathOptions[0] == 'saveBasic') {
				$this->saveBasic();
			} elseif ($pathOptions[0] == 'load') {
				$this->loadCharacter();
			} elseif ($pathOptions[0] == 'save') {
				$this->saveCharacter();
			} elseif ($pathOptions[0] == 'toggleLibrary') {
				$this->toggleLibrary();
			} elseif ($pathOptions[0] == 'delete') {
				$this->delete();
			} elseif ($pathOptions[0] == 'toggleFavorite') {
				$this->toggleFavorite();
			} elseif ($pathOptions[0] == 'getBookData') {
				$this->getBookData();
			} elseif ($pathOptions[0] == 'cilSearch' && $loggedIn) {
				$this->cilSearch();
			} elseif ($pathOptions[0] == 'getUAI') {
				$this->getUAI();
			} elseif ($pathOptions[0] == 'processUAI') {
				$this->processUAI();
			} elseif ($pathOptions[0] == 'bbformUpdateVal') {
				$this->bbformUpdateVal((int)$_POST['charID'], (int)$_POST['fieldIdx'], $_POST['fieldValue']);
			} elseif ($pathOptions[0] == 'bbformUpdateBlock') {
				$this->bbformUpdateBlock((int)$_POST['charID'], (int)$_POST['blockIdx'], $_POST['fieldValue']);
			} elseif ($pathOptions[0] == 'bbformUpdateAbilities') {
				$this->bbformUpdateAbilities((int)$_POST['charID'], (int)$_POST['blockIdx'], $_POST['fieldValue']);
			} elseif ($pathOptions[0] == 'getBbcodeSection') {
				$this->getBbcodeSection((int)$_POST['charID'], (int)$_POST['requestIdx'], $_POST['tagSelector']);
			}  elseif ($pathOptions[0] == 'createFromSnippet') {
				$this->createFromSnippet((int)$_POST['postID'],(int)$_POST['snippetIdx'], $_POST['name']);
			} elseif ($pathOptions[0] == 'getCharacterSnippet') {
				$this->getCharacterSnippet((int)$_POST['postID'],(int)$_POST['snippetIdx']);
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public static function newItemized($type, $name, $system) {
			global $currentUser, $systems;
			$mysql = DB::conn('mysql');

			$systems = Systems::getInstance();
			if ($system == 'custom') {
				return false;
			}
			if (!$systems->verifySystem($system)) {
				return false;
			}

			$searchName = sanitizeString($name, 'search_format');
			$searchAutocomplete = $mysql->prepare("SELECT itemID FROM charAutocomplete WHERE searchName = :searchName LIMIT 1");
			$searchAutocomplete->execute(['searchName' => $searchName]);
			if (!$searchAutocomplete->rowCount()) {
				$addUAI = $mysql->prepare("INSERT INTO charAutocomplete SET `type` = :type, `name` = :name, searchName = :searchName, userDefined = :userDefined, approved = 0")
				$addUAI->execute([
					`type` = $type,
					`name` = $name,
					searchName = $searchName,
					userDefined = $currentUser->userID
				]);
				$itemID = $mysql->lastInsertId();
			} else {
				$itemID = $searchAutocomplete->fetchColumn();
			}

			try {
				$mysql->query("INSERT INTO charAutocomplete_systems SET itemID = {$itemID}, system = {$system}");
			} catch (Exception $e) {}

			return true;
		}

		public function library() {
			$mysql = DB::conn('mysql');

			$searchSystems = [];
			$systems = Systems::getInstance();
			if (isset($_POST['search']) && is_array($_POST['search'])) {
				foreach($_POST['search'] as $searchSystem) {
					if ($systems->verifySystem($searchSystem)) {
						$searchSystems[] = $searchSystem;
					}
				}
				$searchSystems = ' AND system IN (' . implode(', ', $searchSystems) . ')';
			}
			$getCharacters = $mysql->query("SELECT characters.characterID, characters.label, characters.userID, user.username, characters.system FROM characters INNER JOIN users ON characters.userID = users.userID WHERE inLibrary = TRUE" . $searchSystems);
			$characters = [];
			foreach ($getCharacters->fetchAll() as $character) {
				$character['system'] = [
					'slug' => $character['system'],
					'name' => $systems->getFullName($character['system'])
				];
				$character['user'] = [
					'userID' => $character['userID'],
					'username' => $character['username']
				];
				$unset($character['userID'], $character['username']);
				$characters[] = $character;
			}
			displayJSON(['success' => true, 'characters' => $characters]);
		}

		public function my() {
			global $loggedIn, $currentUser;
			$mysql = DB::conn('mysql');
			if (!$loggedIn) {
				displayJSON(['failed' => true, 'notLoggedIn' => true], true);
			}

			$systems = Systems::getInstance();

			$conds = [
				"user.userID = {$currentUser->userID}",
				"retired IS NULL"
			];
			if (isset($_POST['systems'])) {
				$allowedSystems = array_unique($_POST['systems']);
				if (sizeof($allowedSystems) == 1 && $systems->verifySystem($allowedSystems[0])) {
					$conds = "system = {$allowedSystems[0]}";
				} elseif (sizeof($allowedSystems) > 1) {
					$validSystems = [];
					foreach ($allowedSystems as $system) {
						if ($systems->verifySystem($system)) {
							$validSystems[] = $system;
						}
					}
					$conds[] = 'system IN (' . implode(', ', $validSystems) . ')';
				}
			}
			if (isset($_POST['noGame'])) {
				$conds[] = "gameID IS NULL";
			}
			$getCharacters = $mysql->query("SELECT characterID, label, charType, system, gameID, approved, inLibrary WHERE " . implode(' AND ', $conds));
			$characters = [];
			foreach ($rCharacters as $character) {
				$character['label'] = printReady($character['label']);
				$character['system'] = [
					'short' => printReady($character['system']),
					'name' => printReady($systems->getFullName($character['system']))
				];
				// $character['gameID'] = (int) $character['game']['gameID'];
				// $character['approved'] = (bool) $character['game']['approved'];
				// $character['inLibrary'] , characters.= (bool) $character['library']['inLibrary'];
				$characters[] = $character;
			}
			$return = array('characters' => $characters);
			if (isset($_POST['library']) && $_POST['library']) {
				$getUserLibraryChars = $mysql->query("SELECT characters.characterID, characters.label, characters.charType, characters.system, user.userID, user.username FROM characterLibrary_favorites favorites INNER JOIN characters ON favorites.characterID = characters.characterID INNER JOIN users ON characters.userID = users.userID WHERE favorites.userID = {$currentUser->userID}");
				$libraryItems = [];
				foreach ($getUserLibraryChars->fetchAll() as $character) {
					$character['label'] = printReady($character['label']);
					$character['system'] = [
						'short' => printReady($character['system']),
						'name' => printReady($systems->getFullName($character['system']))
					];
					$return['library'][] = $character;
				}
			}

			displayJSON($return);
		}

		public function newChar() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$errors = [];
			$system = $_POST['system'];
			$validSystem = $mysql->prepare("SELECT id FROM systems WHERE ID = ?");
			$validSystem->execute($system);
			if (!$validSystem->rowCount()) {
				$errors[] = 'invalidSystem';
			}
			$label = sanitizeString($_POST['label']);
			if (strcmp(filterString($label), $label) != 0 || $label == '') {
				$errors[] = 'invalidLabel';
			}
			$charType = $_POST['charType'];
			if (!in_array($charType, self::$charTypes)) {
				$charType = 'PC';
			}

			if (sizeof($errors)) {
				displayJSON(['failed' => true, 'errors' => $errors]);
			} else {
				require_once(FILEROOT."/includes/packages/{$system}Character.package.php");
				$charClass = Systems::systemClassName($system).'Character';
				$newChar = new $charClass();
				$newChar->setLabel($label);
				$newChar->setCharType($charType);
				$newChar->createNew();
#				$hl_charCreated = new HistoryLogger('characterCreated');
#				$hl_charCreated->addCharacter($characterID, false)->save();

				displayJSON([
					'success' => true,
					'system' => $system,
					'characterID' => $newChar->getID()
				]);
			}
		}

		public function saveBasic() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$characterID = intval($_POST['characterID']);
			$label = sanitizeString($_POST['label']);
			$charType = in_array($_POST['charType'], self::$charTypes) ? $_POST['charType'] : 'PC';
			if (strlen($label) == 0) {
				displayJSON(['failed' => true, 'errors' => ['noLabel']]);
			}

			$updateCharacter = $mysql->prepare("UPDATE characters SET label = :label, charType = :charType WHERE userID = {$currentUser->userID} AND characterID = {$characterID}");
			$updateCharacter->execute(['label' => $label, 'charType' => $charType]);
			if (!$updateCharacter->rowCount()) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			} else {
#				$hl_basicEdited = new HistoryLogger('basicEdited');
#				$hl_basicEdited->addCharacter($characterID, false)->save();
				displayJSON(['success' => true, 'basicUpdated' => true]);
			}
		}

		public function loadCharacter() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$characterID = (int) $_POST['characterID'];
			if ($characterID <= 0) {
				displayJSON([
					'failed' => true,
					'errors' => ['noCharacterID']
				]);
			}

			$systemCheck = $mysql->query("SELECT system FROM characters WHERE {$characterID}");
			if ($systemCheck->rowCount()) {
				$systemCheck = $systemCheck->fetch();
				$system = $systemCheck['system'];
				addPackage($system.'Character');
				$charClass = Systems::systemClassName($system).'Character';
				if ($character = new $charClass($characterID)) {
					$character->load();
					$charPermissions = $character->checkPermissions($currentUser->userID);
					if ($charPermissions) {
						displayJSON($character->get(isset($_POST['printReady']) && $_POST['printReady'] ? true : false));
					} else {
						displayJSON([
							'failed' => true,
							'errors' => ['noPermission']
						]);
					}
				}
			}
			displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
		}

		public function saveCharacter() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$characterID = (int) $_POST['characterID'];
			if ($characterID <= 0) {
				displayJSON(['failed' => true, 'errors' => ['noCharacterID']]);
			}
			$systemCheck = $mysql->query("SELECT system FROM characters WHERE {$characterID}");
			if (!$systemCheck->rowCount()) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			}
			$systemCheck = $systemCheck->fetch();
			$system = $systemCheck['system'];

			$systems = Systems::getInstance();
			addPackage($system.'Character');
			$charClass = Systems::systemClassName($system).'Character';
			if ($character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions == 'edit') {
					$character->save();
#					$hl_charEdited = new HistoryLogger('characterEdited');
#					$hl_charEdited->addCharacter($characterID)->addUser($currentUser->userID)->save();

					displayJSON(['success' => true, 'saved' => true, 'characterID' => $characterID]);
				} else {
					displayJSON(['failed' => true, 'errors' => ['noPermission']]);
				}
			}
		}

		public function toggleLibrary() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$characterID = intval($_POST['characterID']);
			$libraryCheck = $mysql->query("SELECT inLibrary FROM characters WHERE characterID = {$characterID} AND userID = {$currentUser->userID} AND retired IS NULL");
			if ($libraryCheck->rowCount()) {
				$mysql->query("UPDATE characters SET inLibrary = !inLibrary");
#				$hl_libraryToggle = new HistoryLogger(($currentState?'removeFrom':'addTo').'Library');
#				$hl_libraryToggle->addCharacter($characterID, false)->save();

				displayJSON(['success' => true, 'state' => !$libraryCheck->fetchColumn()]);
			} else {
				displayJSON(['failed' => true, 'errors' => ['invalidID']]);
			}
		}

		public function delete() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$characterID = intval($_POST['characterID']);
			$system = $mysql->query("SELECT system FROM characters WHERE characterID = {$characterID}")->fetchColumn();
			if (!$system) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			}
			require_once(FILEROOT."/includes/packages/".$system."Character.package.php");
			$charClass = Systems::systemClassName($system).'Character';
			if ($character = new $charClass($characterID)) {
				$character->delete();
#				$hl_charDeleted = new HistoryLogger('characterDeleted');
#				$hl_charDeleted->addCharacter($characterID, false)->save();

				displayJSON(['success' => true, 'charDeleted' => true]);
			}
		}

		public function toggleFavorite() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$characterID = intval($_POST['characterID']);
			$charCheck = $mysql->("SELECT characterID FROM characters WHERE characterID = {$characterID}");
			if ($charCheck->rowCount()) {
				try {
					$mysql->query("INSERT INTO characterLibrary_favorites SET userID = {$currentUser->userID}, characterID = {$characterID}");
					$state = "favorited";
				} catch (Exception $e) {
					if (str_contains($e->getMessage(), 'Integrity constraint violation: 1062')) {
						$mysql->query("DELETE FROM characterLibrary_favorites WHERE userID = {$currentUser->userID} AND characterID = {$characterID}")
						$state = "unfavorited";
					}
				}
#				$hl_charFavorited = new HistoryLogger($state == 'favorited'?'characterFavorited':'characterUnfavorited');
#				$hl_charFavorited->addCharacter($characterID, false)->addUser($currentUser->userID)->save();
				displayJSON(['success' => true, 'state' => $state]);
			} else {
				displayJSON(['failed' => true, 'errors' => ['noChar']]);
			}
		}

		public function getBookData() {
			$system = $_POST['system'];
			$systems = Systems::getInstance();
			if ($systems->verifySystem($system)) {
				addPackage($system.'Character');
				$charClass = Systems::systemClassName($system).'Character';
				return $charClass::getBookData();
			} else {
				return null;
			}
		}

		public function cilSearch() {
			$mysql = DB::conn('mysql');

			$type = sanitizeString($_POST['type']);
			$formattedSearch = sanitizeString($_POST['search'], 'search_format');
			$system = $_POST['system'];
			$systems = Systems::getInstance();
			$systemOnly = isset($_POST['systemOnly']) && $_POST['systemOnly'] ? true : false;

			if (!$system || $systems->verifySystem($system)) {
				$getCIL = $mysql->query("
					SELECT charAutocomplete.itemID, charAutocomplete.name, IF(systems.system, TRUE, FALSE) systemItem
					FROM charAutocomplete " . ($systemOnly ? "INNER" : "LEFT") . " JOIN systems ON charAutocomplete.itemID = systems.itemID
					WHERE charAutocomplete.approved = 1 AND charAutocomplete.searchName LIKE '%{$formattedSearch}%'" . ($systemOnly ? " AND systems.system = {$system}" : "") .
					" ORDER BY systemItem DESC LIMIT 5");
				$items = [];
				foreach ($getCIL->fetchAll() as $item) {
					$item['systemItem'] = (bool) $item['systemItem'] ? true : false;
					$items[] = $item;
				}

				displayJSON(['items' => $items]);
			}
		}

		public function getUAI() {
			global $loggedIn, $currentUser;
			$mysql = DB::conn('mysql');

			if (!$loggedIn || !$currentUser->checkACP('autocomplete')) {
				displayJSON(['failed' => true, 'noPermission' => true]);
			}

			$getNewUAI = $mysql->query("SELECT ac.itemID, ac.name, GROUP_CONCAT(systems.system ORDER BY systems.system SEPARATOR ';') systems FROM charAutocomplete ac LEFT JOIN charAutocomplete_systems systems ON ac.itemID = systems.itemID WHERE action IS NULL GROUP BY ac.itemID")
			$newItems = [];
			foreach ( as $item) {
				$item['systems'] = explode(';', $item['systems']);
				$newItems[] = $item;
			}

			// $rAddToSystem = $mongo->userAddedItems->find(
			// 	[
			// 		'itemID' => ['$ne' => null],
			// 		'action' => null
			// 	]
			// );
			// $addToSystem = [];
			// foreach ($rAddToSystem as $item) {
			// 	$item['_id'] = (string) $item['_id'];
			// 	$addToSystem[] = $item;
			// }

			displayJSON([
				'success' => true,
				'newItems' => $newItems,
				'addToSystem' => []
			]);
		}

		public function processUAI() {
			global $loggedIn, $currentUser;
			$mysql = DB::conn('mysql');

			if (!$loggedIn || !$currentUser->checkACP('autocomplete')) {
				displayJSON(['failed' => true, 'noPermission' => true]);
			}

			$uai = $mongo->userAddedItems->findOne(
				['_id' => genMongoId($_POST['item'])]
			);
			$uai['name'] = $_POST['item']->name;
			$uai['actedBy'] = array(
				'userID' => (int) $currentUser->userID,
				'username' => $currentUser->username,
				'on' => genMongoDate()
			);
			$action = $_POST['action'];
			$ac = null;
			$newItem = false;
			if ($uai['itemID'] == null) {
				$ac = $this->searchAutocomplete($uai['name'], $action == 'add' ? $uai['type'] : null);
				$uai['itemID'] = (string) $ac['_id'];
				$newItem = true;
			} else {
				$ac = $mongo->charAutocomplete->findOne(['_id' => $uai['itemID']]);
			}
			if ($_POST['action'] == 'reject') {
				$uai = ['$set' => [
					'name' => $uai['name'],
					'action' => 'rejected',
					'actedBy' => $uai['actedBy']
				]];
				$mongo->userAddedItems->updateOne(
					['_id' => genMongoId($_POST['item']->_id)],
					$uai
				);
			} elseif ($_POST['action'] == 'add') {
				$uai['action'] = 'accepted';
				if ($ac) {
					$mongo->charAutocomplete->updateOne(
						['_id' => $ac['_id']],
						['$push' => ['systems' => $uai['system']]]
					);
				}
				$_id = $uai['_id'];
				unset($uai['_id']);
				$mongo->userAddedItems->updateOne(['_id' => $_id], ['$set' => $uai]);
			}

			displayJSON(['success' => true]);
		}

		private function searchAutocomplete($name, $type = null) {
			$mongo = DB::conn('mongo');

			$searchName = sanitizeString($name, 'search_format');
			$ac = $mongo->charAutocomplete->findOne(['searchName' => $searchName]);
			if ($ac == null && $type != null) {
				$ac = [
					'name' => $name,
					'searchName' => $searchName,
					'type' => $type,
					'userDefined' => true,
					'systems' => []
				];
				$mongo->charAutocomplete->insertOne($ac);
			}

			return $ac;
		}

		private function updateCustomSheetNotes($characterID, $returnNotes, callable $updateFn){
			global $currentUser;
			$mysql = DB::conn('mysql');

			if ($characterID <= 0) {
				displayJSON(['failed' => true, 'errors' => ['noCharacterID']]);
			}
			$system = $mysql->query("SELECT system FROM characters WHERE characterID = {$characterID}")->fetchColumn();
			if (!$system) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			}

			addPackage($system.'Character');
			$charClass = Systems::systemClassName($system).'Character';
			if ($system=='custom' && $character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions == 'edit') {
					$text = $character->getNotes();
					$text = $updateFn($text);
					$character->setNotes($text);
					$character->saveCharacter();

					if($returnNotes){
						displayJSON(['success' => true, 'saved' => true, 'characterID' => $characterID, 'notes' => printReady(BBCode2Html($text),['nl2br'])]);
					} else {
						displayJSON(['success' => true, 'saved' => true, 'characterID' => $characterID]);
					}
				} else {
					displayJSON(['failed' => true, 'errors' => ['noPermission']]);
				}
			}
		}

		private function bbformUpdateVal($characterID, $fieldIdx, $fieldValue){
			$this->updateCustomSheetNotes($characterID, false, function($text) use (&$fieldIdx, &$fieldValue) {
				$formField = 0;
				$matches = null;
				$text = preg_replace_callback('/\[\_(([\w\_\$]*)\=)?([^\]]*)\]/', function($matches) use (&$formField, &$fieldIdx, &$fieldValue) {
					if ($fieldIdx == $formField++){
						return '[_' . $matches[2] . '=' . $fieldValue . ']';
					} else {
						return $matches[0];
					}
				}, $text);
				return $text;
			});
		}

		private function bbformUpdateBlock($characterID, $blockIdx, $fieldValue){
			$this->updateCustomSheetNotes($characterID, true, function($text) use (&$blockIdx, &$fieldValue){
				$formField=0;
				$matches = null;
				$text=preg_replace_callback("/\[#=\"?(.*?)\"?\](.*?)\[\/#\]/ms", function($matches) use (&$formField, &$blockIdx, &$fieldValue){
					if($blockIdx==$formField++){
						return '[#='.$matches[1].']'.$fieldValue.'[/#]';
					} else {
						return $matches[0];
					}
				}, $text);
				return $text;
			});
		}

		private function bbformUpdateAbilities($characterID, $blockIdx, $fieldValue){
			$this->updateCustomSheetNotes($characterID, true, function($text) use (&$blockIdx, &$fieldValue){
				$formField=0;
				$matches = null;
				$text=preg_replace_callback("/\[abilities=\"?(.*?)\"?\](.*?)\[\/abilities\]/ms", function($matches) use (&$formField, &$blockIdx, &$fieldValue){
					if($blockIdx==$formField++){
						return '[abilities='.$matches[1].']'.$fieldValue.'[/abilities]';
					} else {
						return $matches[0];
					}
				}, $text);
				return $text;
			});
		}

		private function getBbcodeSection($characterID, $requestIdx, $selector){
			global $currentUser;
			$mysql = DB::conn('mysql');

			if ($characterID <= 0) {
				displayJSON(['failed' => true, 'errors' => ['noCharacterID']]);
			}
			$system = $mysql->query("SELECT system FROM characters WHERE characterID = {$characterID}")->fetchColumn();
			if (!$system) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			}

			addPackage($system.'Character');
			$charClass = Systems::systemClassName($system).'Character';
			if ($system == 'custom' && $character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions == 'edit') {
					$text = $character->getNotes();

					$ret='';
					$formField=0;
					if($selector=='abilities') {
						preg_replace_callback("/\[abilities=\"?(.*?)\"?\](.*?)\[\/abilities\]/ms", function($matches) use (&$formField, &$requestIdx, &$ret){
							if($requestIdx==$formField++){
								$ret=$matches[2];
							}
						}, $text);
					} else if($selector=='block'){
						preg_replace_callback("/[\r\n]*\[#=\"?(.*?)\"?\](.*?)\[\/#\][\r\n]*/ms", function($matches) use (&$formField, &$requestIdx, &$ret){
							if($requestIdx==$formField++){
								$ret=$matches[2];
							}
						}, $text);
					}

					displayJSON(['success' => true, 'characterID' => $characterID, 'section'=>$ret]);
				} else {
					displayJSON(['failed' => true, 'errors' => ['noPermission']]);
				}
			}
		}

		private function processCharacterSheetSnippet($postID, $snippetIdx, $regex, callable $processFn){
			addPackage('forum');
			$post = new Post($postID);
			$threadManager = new ThreadManager($post->getThreadID());

			if ($threadManager->getPermissions('read')){
				$charSheetBBCode = null;
				$snippetLoop = 0;
				preg_replace_callback($regex, function($matches) use (&$snippetLoop, &$snippetIdx, &$charSheetBBCode){
					if($snippetIdx==$snippetLoop++){
						$charSheetBBCode=$matches[2];
					}
				}, $post->getMessage());

				if($charSheetBBCode){
					$processFn($charSheetBBCode);
				}else {
					displayJSON(['failed' => true, 'errors' => ['noSnippet']]);
				}

			} else {
				displayJSON(['failed' => true, 'errors' => ['noPermission']]);
			}
		}

		private function createFromSnippet($postID, $snippetIdx, $name){
			$this->processCharacterSheetSnippet($postID, $snippetIdx,"/\[charsheet=\"?(.*?)\"?\](.*?)\[\/charsheet\]/ms" ,function($bbCode) use (&$postID, &$snippetIdx, &$name){
				$name = sanitizeString($name);
				if (strcmp(filterString($name), $name) != 0 || $name == '') {
					$name='PC';
				}

				addPackage('customCharacter');
				$charClass = 'customCharacter';
				$newChar = new $charClass();
				$newChar->setLabel($name);
				$newChar->setCharType('PC');
				$newChar->setName($name);
				$newChar->setNotes($bbCode);
				$newChar->createNew();

				displayJSON([
					'success' => true,
					'characterID' => $newChar->getID()
				]);
			});
		}

		private function getCharacterSnippet($postID, $snippetIdx){
			$this->processCharacterSheetSnippet($postID, $snippetIdx,"/\[snippet=\"?(.*?)\"?\](.*?)\[\/snippet\]/ms" ,function($bbCode) use (&$postID, &$snippetIdx){
				displayJSON([
					'success' => true,
					'bbcode' => $bbCode,
					'postID' => $postID,
					'snippetIdx' => $snippetIdx
				]);
			});
		}
	}
?>
