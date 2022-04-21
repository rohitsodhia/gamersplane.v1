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
			$mongo = DB::conn('mongo');

			$systems = Systems::getInstance();
			if ($system == 'custom') {
				return false;
			}
			if (!$systems->verifySystem($system)) {
				return false;
			}

			$searchName = sanitizeString($name, 'search_format');
			$ac = $mongo->charAutocomplete->findOne(
				['searchName' => $searchName],
				['projection' => ['_id' => true, 'systems' => true]]
			);
			$alreadyExists = $mongo->userAddedItems->findOne(
				['name' => $name, 'system' => $system],
				['projection' => ['_id' => true]]
			);
			if ($alreadyExists || ($ac && in_array($system, $ac['systems']))) {
				return true;
			}

			$uai = [
				'name' => $name,
				'itemID' => null,
				'action' => null,
				'system' => $system,
				'type' => $type,
				'addedBy' => [
					'userID' => (int) $currentUser->userID,
					'username' => $currentUser->username,
					'on' => genMongoDate()
				],
				'actedBy' => [
					'userID' => null,
					'username' => null,
					'on' => null
				]
			];
			if ($ac != null) {
				$uai['itemID'] = (string) $ac['_id'];
				$mongo->userAddedItems->insertOne($uai);
			} else {
				$mongo->userAddedItems->insertOne($uai);
			}

			return true;
		}

		public function library() {
			$mongo = DB::conn('mongo');

			$search = ['library.inLibrary' => true];
			if (isset($_POST['search']) && is_array($_POST['search'])) {
				$search['system'] = array('$in' => $_POST['search']);
			}
			$rCharacters = $mongo->characters->find(
				$search,
				['projection' => [
					'_id' => false,
					'characterID' => true,
					'label' => true,
					'user' => true,
					'system' => true
				]]
			);
			$systems = Systems::getInstance();
			$characters = [];
			foreach ($rCharacters as $character) {
				$character['system'] = [
					'slug' => $character['system'],
					'name' => $systems->getFullName($character['system'])
				];
				$characters[] = $character;
			}
			displayJSON(['success' => true, 'characters' => $characters]);
		}

		public function my() {
			global $loggedIn, $currentUser;
			$mongo = DB::conn('mongo');
			if (!$loggedIn) {
				displayJSON(['failed' => true, 'notLoggedIn' => true], true);
			}

			$systems = Systems::getInstance();

			$cond = [
				'user.userID' => $currentUser->userID,
				'retired' => null
			];
			if (isset($_POST['systems'])) {
				$allowedSystems = array_unique($_POST['systems']);
				if (sizeof($allowedSystems) == 1) {
					$cond['system'] = $allowedSystems[0];
				} elseif (sizeof($allowedSystems) > 1) {
					foreach ($allowedSystems as &$system) {
						$system = preg_replace('/[^\w_]/', '', $system);
					}
					$cond['system'] = ['$in' => $allowedSystems];
				}
			}
			if (isset($_POST['noGame'])) {
				$cond['game'] = null;
			}
			$rCharacters = $mongo->characters->find($cond, ['projection' => [
				'characterID' => true,
				'label' => true,
				'charType' => true,
				'system' => true,
				'game' => true,
				'library' => true
			]]);
			$characters = [];
			foreach ($rCharacters as $character) {
				$character['label'] = printReady($character['label']);
				$character['system'] = [
					'short' => printReady($character['system']),
					'name' => printReady($systems->getFullName($character['system']))
				];
				$character['gameID'] = (int) $character['game']['gameID'];
				$character['approved'] = (bool) $character['game']['approved'];
				$character['inLibrary'] = (bool) $character['library']['inLibrary'];
				unset($character['game'], $character['library']);
				$characters[] = $character;
			}
			$return = array('characters' => $characters);
			if (isset($_POST['library']) && $_POST['library']) {
				$rLibraryItems = $mongo->characterLibraryFavorites->find(['userID' => $currentUser->userID]);
				$libraryItems = [];
				foreach ($rLibraryItems as $item) {
					$libraryItems[] = $item['characterID'];
				}
				$libraryItems = $mongo->characters->find(
					['characterID' => ['$in' => $libraryItems]],
					['projection' => [
						'characterID' => true,
						'label' => true,
						'charType' => true,
						'system' => true,
						'user' => true
					]]
				);
				foreach ($libraryItems as $item) {
					$item['label'] = printReady($item['label']);
					$item['system'] = [
						'short' => printReady($item['system']),
						'name' => printReady($systems->getFullName($item['system']))
					];
					unset($item['_id']);
					$return['library'][] = $item;
				}
			}

			displayJSON($return);
		}

		public function newChar() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$errors = [];
			$system = $_POST['system'];
			$validSystem = $mongo->systems->findOne(['_id' => $system], ['projection' => ['_id' => true]]);
			if (!$validSystem) {
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
			$mongo = DB::conn('mongo');
			$characterID = intval($_POST['characterID']);
			$label = sanitizeString($_POST['label']);
			$charType = in_array($_POST['charType'], self::$charTypes) ? $_POST['charType'] : 'PC';
			if (strlen($label) == 0) {
				displayJSON(['failed' => true, 'errors' => ['noLabel']]);
			}

			$charCheck = $mongo->characters->findOne(
				[
					'user.userID' => $currentUser->userID,
					'characterID' => $characterID
				],
				['projection' => ['_id' => true]]);
			if (!$charCheck) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			} else {
				$mongo->characters->updateOne(
					['characterID' => $characterID],
					['$set' => ['label' => $label, 'charType' => $charType]]
				);
#				$hl_basicEdited = new HistoryLogger('basicEdited');
#				$hl_basicEdited->addCharacter($characterID, false)->save();
				displayJSON(['success' => true, 'basicUpdated' => true]);
			}
		}

		public function loadCharacter() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$characterID = (int) $_POST['characterID'];
			if ($characterID <= 0) {
				displayJSON([
					'failed' => true,
					'errors' => ['noCharacterID']
				]);
			}

			$systemCheck = $mongo->characters->findOne(['characterID' => $characterID], ['projection' => ['system' => true]]);
			if ($systemCheck) {
				$system = $systemCheck['system'];
				addPackage($system.'Character');
				$charClass = Systems::systemClassName($system).'Character';
				if ($character = new $charClass($characterID)) {
					$character->load();
					$charPermissions = $character->checkPermissions($currentUser->userID);
					if ($charPermissions) {
						displayJSON($character->get(isset($_POST['printReady']) && $_POST['printReady']?true:false));
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

		public function checkPermissions($characterID, $userID = null) {
			global $mongo;

			if ($userID == null) {
				$userID = $this->userID;
			} else {
				$userID = intval($userID);
			}

			$characterID = (int) $characterID;
			$charCheck = $mongo->characters->findOne(
				['characterID' => $characterID],
				['projection' => ['user' => true, 'game' => true]]
			);
			if ($charCheck['user']['userID'] == $userID) {
				return 'edit';
			} else {
				$gmCheck = $mongo->games->findOne(
					[
						'gameID' => $charCheck['game']['gameID'],
						'players' => [
							'$elemMatch' => [
								'user.userID' => $userID,
								'isGM' => true
							]
						]
					],
					['projection' => ['_id' => true]]
				);
				if ($gmCheck) {
					return 'edit';
				}
			}
			return $mongo->characters->findOne(['characterID' => $characterID, 'library.inLibrary' => true]) ? 'library' : false;
		}

		public function saveCharacter() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$characterID = (int) $_POST['characterID'];
			if ($characterID <= 0) {
				displayJSON(['failed' => true, 'errors' => ['noCharacterID']]);
			}
			$systemCheck = $mongo->characters->findOne(['characterID' => $characterID], ['projection' => ['system' => true]]);
			if (!$systemCheck) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			}
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
			$mongo = DB::conn('mongo');

			$characterID = intval($_POST['characterID']);
			$currentState = $mongo->characters->findOne(
				[
					'characterID' => $characterID,
					'user.userID' => $currentUser->userID,
					'retired' => null
				],
				['projection' => ['library' => true]]
			);
			if ($currentState) {
				$mongo->characters->updateOne(
					['_id' => $currentState['_id']],
					['$set' => ['library.inLibrary' => !$currentState['library']['inLibrary']]]
				);
#				$hl_libraryToggle = new HistoryLogger(($currentState?'removeFrom':'addTo').'Library');
#				$hl_libraryToggle->addCharacter($characterID, false)->save();

				displayJSON(['success' => true, 'state' => !$currentState['library']['inLibrary']]);
			} else
				displayJSON(['failed' => true, 'errors' => array('invalidID')]);
		}

		public function delete() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			$characterID = intval($_POST['characterID']);
			try {
				$system = $mongo->characters->findOne(
					['characterID' => $characterID],
					['projection' => ['_id' => false, 'system' => true]]
				)['system'];
			} catch (Exception $e) {
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
			$mongo = DB::conn('mongo');

			$characterID = intval($_POST['characterID']);
			$charCheck = $mongo->characters->findOne(
				[
					'characterID' => $characterID
				]
			) ? true : false;
			if ($charCheck) {
				$state = $mongo->characterLibraryFavorites->findOne(
					[
						'userID' => $currentUser->userID,
						'characterID' => $characterID
					]
				) ? 'unfavorited' : 'favorited';
				if ($state == 'unfavorited') {
					$mongo->characterLibraryFavorites->deleteOne(['userID' => $currentUser->userID, 'characterID' => $characterID]);
				} else {
					$mongo->characterLibraryFavorites->insertOne(['userID' => $currentUser->userID, 'characterID' => $characterID]);
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
			$mongo = DB::conn('mongo');

			$type = sanitizeString($_POST['type']);
			$searchName = sanitizeString($_POST['search'], 'search_format');
			$system = $_POST['system'];
			$systems = Systems::getInstance();
			$systemOnly = isset($_POST['systemOnly']) && $_POST['systemOnly']?true:false;

			if ($systems->verifySystem($system)) {
				$search = ['searchName' => new MongoDB\BSON\Regex($searchName)];
				$items = [];
				if ($systemOnly) {
					$search['systems'] = $system;
					$rCIL = $mongo->charAutocomplete->find(
						$search,
						[
							'sort' => ['searchName' => 1],
							'limit' => 5
						]
					);
					foreach ($rCIL as $item) {
						$items[] = [
							'itemID' => (string) $item['_id'],
							'name' => $item['name'],
							'systemItem' => true
						];
					}
				} else {
					$rCIL = $mongo->charAutocomplete->aggregate([
						[
							'$match' => [
								'searchName' => $search['searchName']
							]
						],
						[
							'$project' => [
								'name' => true,
								'inSystem' => [
									'$setIsSubset' => [
										[$system],
										'$systems'
									]
								]
							]
						],
						[
							'$sort' => [
								'inSystem' => -1,
								'name' => 1
							]
						],
						[
							'$limit' => 5
						]
					]);
					foreach ($rCIL['result'] as $item) {
						$items[] = [
							'itemID' => (string) $item['_id'],
							'name' => $item['name'],
							'systemItem' => $item['systemItem']?true:false
						];
					}
				}

				displayJSON(['items' => $items]);
			}
		}

		public function getUAI() {
			global $loggedIn, $currentUser;
			$mongo = DB::conn('mongo');

			if (!$loggedIn || !$currentUser->checkACP('autocomplete')) {
				displayJSON(['failed' => true, 'noPermission' => true]);
			}

			$rNewItems = $mongo->userAddedItems->find(['itemID' => null, 'action' => null]);
			$newItems = [];
			foreach ($rNewItems as $item) {
				$item['_id'] = (string) $item['_id'];
				$newItems[] = $item;
			}

			$rAddToSystem = $mongo->userAddedItems->find(
				[
					'itemID' => ['$ne' => null],
					'action' => null
				]
			);
			$addToSystem = [];
			foreach ($rAddToSystem as $item) {
				$item['_id'] = (string) $item['_id'];
				$addToSystem[] = $item;
			}

			displayJSON([
				'success' => true,
				'newItems' => $newItems,
				'addToSystem' => $addToSystem
			]);
		}

		public function processUAI() {
			global $loggedIn, $currentUser;
			$mongo = DB::conn('mongo');

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
			$mongo = DB::conn('mongo');

			if ($characterID <= 0) {
				displayJSON(['failed' => true, 'errors' => ['noCharacterID']]);
			}
			$systemCheck = $mongo->characters->findOne(['characterID' => $characterID], ['projection' => ['system' => true]]);
			if (!$systemCheck) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			}
			$system = $systemCheck['system'];

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
						displayJSON(['success' => true, 'saved' => true, 'characterID' => $characterID, 'notes'=>printReady(BBCode2Html($text),['nl2br'])]);
					} else {
						displayJSON(['success' => true, 'saved' => true, 'characterID' => $characterID]);
					}
				} else {
					displayJSON(['failed' => true, 'errors' => ['noPermission']]);
				}
			}
		}

		private function bbformUpdateVal($characterID, $fieldIdx, $fieldValue){
			$this->updateCustomSheetNotes($characterID, false, function($text) use (&$fieldIdx, &$fieldValue){
				$formField=0;
				$matches = null;
				$text=preg_replace_callback('/\[\_(([\w\_\$]*)\=)?([^\]]*)\]/', function($matches) use (&$formField, &$fieldIdx, &$fieldValue){
					if($fieldIdx==$formField++){
						return '[_'.$matches[2].'='.$fieldValue.']';
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
			$mongo = DB::conn('mongo');

			if ($characterID <= 0) {
				displayJSON(['failed' => true, 'errors' => ['noCharacterID']]);
			}
			$systemCheck = $mongo->characters->findOne(['characterID' => $characterID], ['projection' => ['system' => true]]);
			if (!$systemCheck) {
				displayJSON(['failed' => true, 'errors' => ['noCharacter']]);
			}
			$system = $systemCheck['system'];

			addPackage($system.'Character');
			$charClass = Systems::systemClassName($system).'Character';
			if ($system=='custom' && $character = new $charClass($characterID)) {
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
