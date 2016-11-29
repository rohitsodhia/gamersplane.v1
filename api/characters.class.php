<?
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
			} else {
				displayJSON(array('failed' => true));
			}
		}

		public static function newItemized($type, $name, $system) {
			global $currentUser, $systems, $mongo;

			$systems = Systems::getInstance();
			if ($system == 'custom') {
				return false;
			}
			if (!$systems->verifySystem($system)) {
				return false;
			}

			$searchName = sanitizeString($name, 'search_format');
			$ac = $mongo->charAutocomplete->findOne(array('searchName' => $searchName), array('_id' => true, 'systems' => true));
			$alreadyExists = $mongo->userAddedItems->findOne(array('name' => $name, 'system' => $system), array('_id' => true));
			if ($alreadyExists || ($ac && in_array($system, $ac['systems']))) {
				return true;
			}

			$uai = array(
				'name' => $name,
				'itemID' => null,
				'action' => null,
				'system' => $system,
				'type' => $type,
				'addedBy' => array(
					'userID' => (int) $currentUser->userID,
					'username' => $currentUser->username,
					'on' => new MongoDate()
				),
				'actedBy' => array(
					'userID' => null,
					'username' => null,
					'on' => null
				)
			);
			if ($ac != null) {
				$uai['itemID'] = $ac['_id']->{'$id'};
				$mongo->userAddedItems->insert($uai);
			} else {
				$mongo->userAddedItems->insert($uai);
			}

			return true;
		}

		public function library() {
			global $mongo;

			$search = array('library.inLibrary' => true);
			if (isset($_POST['search']) && is_array($_POST['search']))
				$search['system'] = array('$in' => $_POST['search']);
			$rCharacters = $mongo->characters->find($search, array('_id' => false, 'characterID' => true, 'label' => true, 'user' => true, 'system' => true));
			$systems = Systems::getInstance();
			$characters = array();
			foreach ($rCharacters as $character) {
				$character['system'] = array('slug' => $character['system'], 'name' => $systems->getFullName($character['system']));
				$characters[] = $character;
			}
			displayJSON(array('success' => true, 'characters' => $characters));
		}

		public function my() {
			global $loggedIn, $currentUser, $mongo;
			if (!$loggedIn)
				displayJSON(array('failed' => true, 'notLoggedIn' => true), true);

			$systems = Systems::getInstance();

			$cond = array(
				'user.userID' => $currentUser->userID,
				'retired' => null
			);
			if (isset($_POST['systems'])) {
				$allowedSystems = array_unique($_POST['systems']);
				if (sizeof($allowedSystems) == 1)
					$cond['system'] = $allowedSystems[0];
				elseif (sizeof($allowedSystems) > 1) {
					foreach ($allowedSystems as &$system)
						$system = preg_replace('/[^\w_]/', '', $system);
					$cond['system'] = array('$in' => $allowedSystems);
				}
			}
			if (isset($_POST['noGame']))
				$cond['game'] = null;
			$rCharacters = $mongo->characters->find($cond, array(
				'characterID' => true,
				'label' => true,
				'charType' => true,
				'system' => true,
				'game' => true,
				'library' => true
			));
			$characters = array();
			foreach ($rCharacters as $character) {
				$character['label'] = printReady($character['label']);
				$character['system'] = array('short' => printReady($character['system']), 'name' => printReady($systems->getFullName($character['system'])));
				$character['gameID'] = (int) $character['game']['gameID'];
				$character['approved'] = (bool) $character['game']['approved'];
				$character['inLibrary'] = (bool) $character['library']['inLibrary'];
				unset($character['game'], $character['library']);
				$characters[] = $character;
			}
			$return = array('characters' => $characters);
			if (isset($_POST['library']) && $_POST['library']) {
				$rLibraryItems = $mongo->characterLibraryFavorites->find(array('userID' => $currentUser->userID));
				$libraryItems = array();
				foreach ($rLibraryItems as $item)
					$libraryItems[] = $item['characterID'];
				$libraryItems = $mongo->characters->find(array('characterID' => array('$in' => $libraryItems)), array('characterID' => true, 'label' => true, 'charType' => true, 'system' => true, 'user' => true));
				foreach ($libraryItems as $item) {
					$item['label'] = printReady($item['label']);
					$item['system'] = array('short' => printReady($item['system']), 'name' => printReady($systems->getFullName($item['system'])));
					unset($item['_id']);
					$return['library'][] = $item;
				}
			}

			displayJSON($return);
		}

		public function newChar() {
			global $currentUser, $mongo;

			$errors = array();
			$system = $_POST['system'];
			$validSystem = $mongo->systems->findOne(array('_id' => $system), array('_id' => true));
			if (!$validSystem)
				$errors[] = 'invalidSystem';
			$label = sanitizeString($_POST['label']);
			if (strcmp(filterString($label), $label) != 0 || $label == '')
				$errors[] = 'invalidLabel';
			$charType = $_POST['charType'];
			if (!in_array($charType, array('PC', 'NPC', 'Mob')))
				$charType = 'PC';

			if (sizeof($errors))
				displayJSON(array('failed' => true, 'errors' => $errors));
			else {
				require_once(FILEROOT."/includes/packages/{$system}Character.package.php");
				$charClass = Systems::systemClassName($system).'Character';
				$newChar = new $charClass();
				$newChar->setLabel($label);
				$newChar->setCharType($charType);
				$newChar->createNew();
#				$hl_charCreated = new HistoryLogger('characterCreated');
#				$hl_charCreated->addCharacter($characterID, false)->save();

				displayJSON(array('success' => true, 'system' => $system, 'characterID' => $newChar->getID()));
			}
		}

		public function saveBasic() {
			global $currentUser, $mongo;
			$characterID = intval($_POST['characterID']);
			$label = sanitizeString($_POST['label']);
			$charType = in_array($_POST['charType'], self::$charTypes)?$_POST['charType']:'PC';
			if (strlen($label) == 0)
				displayJSON(array('failed' => true, 'errors' => array('noLabel')));

			$charCheck = $mongo->characters->findOne(array('user.userID' => $currentUser->userID, 'characterID' => $characterID), array('_id' => true));
			if (!$charCheck)
				displayJSON(array('failed' => true, 'errors' => array('noCharacter')));
			else {
				$mongo->characters->update(array('characterID' => $characterID), array('$set' => array('label' => $label, 'charType' => $charType)));
#				$hl_basicEdited = new HistoryLogger('basicEdited');
#				$hl_basicEdited->addCharacter($characterID, false)->save();
				displayJSON(array('success' => true, 'basicUpdated' => true));
			}
		}

		public function loadCharacter() {
			global $mongo, $currentUser;

			$characterID = (int) $_POST['characterID'];
			if ($characterID <= 0)
				displayJSON(array('failed' => true, 'errors' => array('noCharacterID')));

			$systemCheck = $mongo->characters->findOne(array('characterID' => $characterID), array('system' => true));
			if ($systemCheck) {
				$system = $systemCheck['system'];
				$systems = Systems::getInstance();
				addPackage($system.'Character');
				$charClass = Systems::systemClassName($system).'Character';
				if ($character = new $charClass($characterID)) {
					$character->load();
					$charPermissions = $character->checkPermissions($currentUser->userID);
					if ($charPermissions)
						displayJSON($character->get(isset($_POST['printReady']) && $_POST['printReady']?true:false));
					else
						displayJSON(array('failed' => true, 'errors' => array('noPermission')));
				}
			}
			displayJSON(array('failed' => true, 'errors' => array('noCharacter')));
		}

		public function checkPermissions($characterID, $userID = null) {
			global $mongo;

			if ($userID == null)
				$userID = $this->userID;
			else
				$userID = intval($userID);

			$characterID = (int) $characterID;
			$charCheck = $mongo->characters->findOne(array('characterID' => $characterID), array('user' => true, 'game' => true));
			if ($charCheck['user']['userID'] == $userID)
				return 'edit';
			else {
				$gmCheck = $mongo->games->findOne(array('gameID' => $charCheck['game']['gameID'], 'players' => array('$elemMatch' => array('user.userID' => $userID, 'isGM' => true))), array('_id' => true));
				if ($gmCheck)
					return 'edit';
			}
			return $mongo->characters->findOne(array('characterID' => $characterID, 'library.inLibrary' => true))?'library':false;
		}

		public function saveCharacter() {
			global $mongo, $currentUser;

			$characterID = (int) $_POST['characterID'];
			if ($characterID <= 0)
				displayJSON(array('failed' => true, 'errors' => array('noCharacterID')));
			$systemCheck = $mongo->characters->findOne(array('characterID' => $characterID), array('system' => true));
			if (!$systemCheck)
				displayJSON(array('failed' => true, 'errors' => array('noCharacter')));
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

					displayJSON(array('success' => true, 'saved' => true, 'characterID' => $characterID));
				} else
					displayJSON(array('failed' => true, 'errors' => array('noPermission')));
			}
		}

		public function toggleLibrary() {
			global $currentUser, $mongo;

			$characterID = intval($_POST['characterID']);
			$currentState = $mongo->characters->findOne(array('characterID' => $characterID, 'user.userID' => $currentUser->userID, 'retired' => null), array('library' => true));
			if ($currentState) {
				$mongo->characters->update(array('_id' => $currentState['_id']), array('$set' => array('library.inLibrary' => !$currentState['library']['inLibrary'])));
#				$hl_libraryToggle = new HistoryLogger(($currentState?'removeFrom':'addTo').'Library');
#				$hl_libraryToggle->addCharacter($characterID, false)->save();

				displayJSON(array('success' => true, 'state' => !$currentState['library']['inLibrary']));
			} else
				displayJSON(array('failed' => true, 'errors' => array('invalidID')));
		}

		public function delete() {
			global $currentUser, $mongo;

			$characterID = intval($_POST['characterID']);
			try {
				$system = $mongo->characters->findOne(array('characterID' => $characterID), array('_id' => false, 'system' => true))['system'];
			} catch (Exception $e) {
				displayJSON(array('failed' => true, 'errors' => array('noCharacter')));
			}
			require_once(FILEROOT."/includes/packages/".$system."Character.package.php");
			$charClass = Systems::systemClassName($system).'Character';
			if ($character = new $charClass($characterID)) {
				$character->delete();
#				$hl_charDeleted = new HistoryLogger('characterDeleted');
#				$hl_charDeleted->addCharacter($characterID, false)->save();

				displayJSON(array('success' => true, 'charDeleted' => true));
			}
		}

		public function toggleFavorite() {
			global $currentUser, $mongo;

			$characterID = intval($_POST['characterID']);
			$charCheck = $mongo->characters->findOne(array('characterID' => $characterID, 'library.inLibrary' => true))?true:false;
			if ($charCheck) {
				$state = $mongo->characterLibraryFavorites->findOne(array('userID' => $currentUser->userID, 'characterID' => $characterID))?'unfavorited':'favorited';
				if ($state == 'unfavorited')
					$mongo->characterLibraryFavorites->remove(array('userID' => $currentUser->userID, 'characterID' => $characterID));
				else
					$mongo->characterLibraryFavorites->insert(array('userID' => $currentUser->userID, 'characterID' => $characterID));
#				$hl_charFavorited = new HistoryLogger($state == 'favorited'?'characterFavorited':'characterUnfavorited');
#				$hl_charFavorited->addCharacter($characterID, false)->addUser($currentUser->userID)->save();
				displayJSON(array('success' => true, 'state' => $state));
			} else
				displayJSON(array('failed' => true, 'errors' => array('noChar')));
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
			global $mongo;

			$type = sanitizeString($_POST['type']);
			$searchName = sanitizeString($_POST['search'], 'search_format');
			$system = $_POST['system'];
			$systems = Systems::getInstance();
			$systemOnly = isset($_POST['systemOnly']) && $_POST['systemOnly']?true:false;

			if ($systems->verifySystem($system)) {
				$search = array('searchName' => new MongoRegex("/{$searchName}/"));
				$items = array();
				if ($systemOnly) {
					$search['systems'] = $system;
					$rCIL = $mongo->charAutocomplete->find($search)->sort(array('searchName' => 1))->limit(5);
					foreach ($rCIL as $item)
						$items[] = array(
							'itemID' => $item['_id']->{$id},
							'name' => $item['name'],
							'systemItem' => true
						);
				} else {
					$rCIL = $mongo->charAutocomplete->aggregate(array(
						array(
							'$match' => array(
								'searchName' => $search['searchName']
							)
						),
						array(
							'$project' => array(
								'name' => true,
								'inSystem' => array(
									'$setIsSubset' => array(
										array($system),
										'$systems'
									)
								)
							)
						),
						array(
							'$sort' => array(
								'inSystem' => -1,
								'name' => 1
							)
						),
						array(
							'$limit' => 5
						)
					));
					foreach ($rCIL['result'] as $item)
						$items[] = array(
							'itemID' => $item['_id']->{$id},
							'name' => $item['name'],
							'systemItem' => $item['systemItem']?true:false
						);
				}

				displayJSON(array('items' => $items));
			}
		}

		public function getUAI() {
			global $loggedIn, $currentUser, $mongo;

			if (!$loggedIn || !$currentUser->checkACP('autocomplete'))
				displayJSON(array('failed' => true, 'noPermission' => true));

			$rNewItems = $mongo->userAddedItems->find(array('itemID' => null, 'action' => null));
			$newItems = array();
			foreach ($rNewItems as $item) {
				$item['_id'] = $item['_id']->{'$id'};
				$newItems[] = $item;
			}

			$rAddToSystem = $mongo->userAddedItems->find(array('itemID' => array('$ne' => null), 'action' => null));
			$addToSystem = array();
			foreach ($rAddToSystem as $item) {
				$item['_id'] = $item['_id']->{'$id'};
				$addToSystem[] = $item;
			}

			displayJSON(array('success' => true, 'newItems' => $newItems, 'addToSystem' => $addToSystem));
		}

		public function processUAI() {
			global $loggedIn, $currentUser, $mongo;

			if (!$loggedIn || !$currentUser->checkACP('autocomplete'))
				displayJSON(array('failed' => true, 'noPermission' => true));

			$uai = $mongo->userAddedItems->findOne(array('_id' => new MongoId($_POST['item']->_id)));
			$uai['name'] = $_POST['item']->name;
			$uai['actedBy'] = array(
				'userID' => (int) $currentUser->userID,
				'username' => $currentUser->username,
				'on' => new MongoDate()
			);
			$action = $_POST['action'];
			$ac = null;
			$newItem = false;
			if ($uai['itemID'] == null) {
				$ac = $this->searchAutocomplete($uai['name'], $action == 'add'?$uai['type']:null);
				$uai['itemID'] = $ac['_id']->{'$id'};
				$newItem = true;
			} else
				$ac = $mongo->charAutocomplete->findOne(array('_id' => $uai['itemID']));
			if ($_POST['action'] == 'reject') {
				$uai = array(
					'$set' =>
						array(
							'name' => $uai['name'],
							'action' => 'rejected',
							'actedBy' => $uai['actedBy']
						)
				);
				$mongo->userAddedItems->update(array('_id' => new MongoId($_POST['item']->_id)), $uai);
			} elseif ($_POST['action'] == 'add') {
				$uai['action'] = 'accepted';
				if ($ac)
					$mongo->charAutocomplete->update(array('_id' => $ac['_id']), array('$push' => array('systems' => $uai['system'])));
				$_id = $uai['_id'];
				unset($uai['_id']);
				$mongo->userAddedItems->update(array('_id' => $_id), array('$set' => $uai));
			}

			displayJSON(array('success' => true));
		}

		private function searchAutocomplete($name, $type = null) {
			global $mongo;

			$searchName = sanitizeString($name, 'search_format');
			$ac = $mongo->charAutocomplete->findOne(array('searchName' => $searchName));
			if ($ac == null && $type != null) {
				$ac = array(
					'name' => $name,
					'searchName' => $searchName,
					'type' => $type,
					'userDefined' => true,
					'systems' => array()
				);
				$mongo->charAutocomplete->insert($ac);
			}

			return $ac;
		}
	}
?>
