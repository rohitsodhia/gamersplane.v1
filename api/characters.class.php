<?
	class characters {
		public static $charTypes = array('PC', 'NPC', 'Mob');

		public function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'library') 
				$this->library();
			elseif ($pathOptions[0] == 'my') 
				$this->my();
			elseif ($pathOptions[0] == 'new') 
				$this->newChar();
			elseif ($pathOptions[0] == 'saveBasic') 
				$this->saveBasic();
			elseif ($pathOptions[0] == 'load' && intval($_POST['characterID'])) 
				$this->loadCharacter($_POST['characterID']);
			elseif ($pathOptions[0] == 'save' && intval($_POST['characterID'])) 
				$this->saveCharacter($_POST['characterID']);
			elseif ($pathOptions[0] == 'toggleLibrary') 
				$this->toggleLibrary();
			elseif ($pathOptions[0] == 'delete') 
				$this->delete();
			elseif ($pathOptions[0] == 'toggleFavorite') 
				$this->toggleFavorite();
			elseif ($pathOptions[0] == 'cilSearch' && $loggedIn) 
				$this->cilSearch(); 
			elseif ($pathOptions[0] == 'getUAI') 
				$this->getUAI(); 
			elseif ($pathOptions[0] == 'processUAI') 
				$this->processUAI(); 
			else 
				displayJSON(array('failed' => true));
		}

		public static function newItemized($type, $name, $system) {
			global $currentUser, $mysql;

			require_once(FILEROOT.'/includes/Systems.class.php');
			$systems = Systems::getInstance();
			if ($system == 'custom') 
				return false;
			if ($systems->verifySystem($system)) 
				return false;

			$itemCheck = $mysql->prepare("SELECT itemID FROM charAutocomplete WHERE type = :type AND LOWER(searchName) = :searchName");
			$itemCheck->bindValue(':type', $type);
			$itemCheck->bindValue(':searchName', sanitizeString($name, 'search_format'));
			$itemCheck->execute();
			if ($itemCheck->rowCount()) {
				$itemID = $itemCheck->fetchColumn();
				$inSystem = $mysql->query("SELECT system FROM system_charAutocomplete_map WHERE system = '{$system}' AND itemID = {$itemID}");
				if ($inSystem->rowCount() == 0) {
					try {
						$addItem = $mysql->prepare("INSERT INTO userAddedItems (itemType, itemID, addedBy, addedOn, system) VALUES (:itemType, :itemID, {$currentUser->userID}, NOW(), '{$system}')");
						$addItem->bindValue(':itemType', $type);
						$addItem->bindValue(':itemID', $itemID);
						$addItem->execute();
					} catch (Exception $e) {}
				}
			} else {
				try {
					$addItem = $mysql->prepare("INSERT INTO userAddedItems (itemType, name, addedBy, addedOn, system) VALUES (:itemType, :name, {$currentUser->userID}, NOW(), '{$system}')");
					$addItem->bindValue(':itemType', $type);
					$addItem->bindValue(':name', sanitizeString($name, 'rem_dup_spaces'));
					$addItem->execute();
				} catch (Exception $e) {}
			}

			return true;
		}

		public function library() {
			global $mongo;

			$search = array('library.inLibrary' => true);
			if (isset($_POST['search']) && is_array($_POST['search'])) 
				$search['system'] = array('$in' => $_POST['search']);
			$rCharacters = $mongo->characters->find($search, array('_id' => false, 'characterID' => true, 'label' => true, 'user' => true, 'system' => true));
			require_once('../includes/Systems.class.php');
			$systems = Systems::getInstance();
			$characters = array();
			foreach ($rCharacters as $character) {
				$character['system'] = array('slug' => $character['system'], 'name' => $systems->getFullName($character['system']));
				$characters[] = $character;
			}
			displayJSON(array('success' => true, 'characters' => $characters));
		}

		public function my() {
			global $loggedIn, $currentUser, $mysql, $mongo;
			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'notLoggedIn' => true), true);

			require_once('../includes/Systems.class.php');
			$systems = Systems::getInstance();

			$userID = $currentUser->userID;
			$characters = $mysql->prepare("SELECT c.characterID, c.label, c.charType, c.system, c.gameID, c.approved FROM characters c WHERE c.retired IS NULL AND c.userID = {$userID}".(isset($_POST['system'])?' AND c.system = :system':'').(isset($_POST['noGame'])?' AND c.gameID IS NULL':''));
			if (isset($_POST['system'])) 
				$characters->bindValue(':system', $_POST['system']);
			$characters->execute();
			$characters = $characters->fetchAll();
			foreach ($characters as &$character) {
				$character['characterID'] = (int) $character['characterID'];
				$character['label'] = printReady($character['label']);
				$character['system'] = array('short' => printReady($character['system']), 'name' => printReady($systems->getFullName($character['system'])));
				$character['gameID'] = (int) $character['gameID'];
				$character['approved'] = (bool) $character['approved'];
				$inLibrary = $mongo->characters->findOne(array('characterID' => $character['characterID']), array('library' => true));
				$character['inLibrary'] = (bool) $inLibrary['library']['inLibrary'];
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
			global $currentUser, $mysql, $mongo;

			require_once('../includes/Systems.class.php');
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
				$addCharacter = $mysql->prepare('INSERT INTO characters (userID, label, charType, system) VALUES (:userID, :label, :charType, :system)');
				$addCharacter->bindValue(':userID', $currentUser->userID);
				$addCharacter->bindValue(':label', $label);
				$addCharacter->bindValue(':charType', $charType);
				$addCharacter->bindValue(':system', $system);
				$addCharacter->execute();
				$characterID = $mysql->lastInsertId();

				require_once(FILEROOT."/includes/packages/{$system}Character.package.php");
				$charClass = Systems::systemClassName($system).'Character';
				$newChar = new $charClass($characterID);
				$newChar->setLabel($label);
				$newChar->setCharType($charType);
				$newChar->save(true);
#				$hl_charCreated = new HistoryLogger('characterCreated');
#				$hl_charCreated->addCharacter($characterID, false)->save();

				displayJSON(array('success' => true, 'system' => $system, 'characterID' => $characterID));
			}
		}

		public function saveBasic() {
			global $currentUser, $mysql, $mongo;
			$characterID = intval($_POST['characterID']);
			$label = sanitizeString($_POST['label']);
			$charType = in_array($_POST['charType'], self::$charTypes)?$_POST['charType']:'PC';
			if (strlen($label) == 0) 
				displayJSON(array('failed' => true, 'errors' => array('noLabel')));

			$labelCheck = $mysql->query("SELECT label, charType FROM characters WHERE retired IS NULL AND userID = {$currentUser->userID} AND characterID = {$characterID}");
			if ($labelCheck->rowCount() == 0) 
				displayJSON(array('failed' => true, 'errors' => array('noCharacter')));
			else {
				$updateLabel = $mysql->prepare("UPDATE characters SET label = :label, charType = :charType WHERE characterID = $characterID");
				$updateLabel->bindValue(':label', $label);
				$updateLabel->bindValue(':charType', $charType);
				$updateLabel->execute();
				$mongo->characters->update(array('characterID' => $characterID), array('$set' => array('label' => $label, 'charType' => $charType)));
#				$hl_basicEdited = new HistoryLogger('basicEdited');
#				$hl_basicEdited->addCharacter($characterID, false)->save();
				displayJSON(array('success' => true, 'basicUpdated' => true));
			}
		}

		public function loadCharacter($characterID) {
			global $mysql, $mongo, $currentUser;

			$characterID = (int) $characterID;
			if ($characterID <= 0) 
				displayJSON(array('failed' => true, 'errors' => array('noCharacterID')));

			$charCheck = $mysql->query("SELECT system FROM characters WHERE characterID = {$characterID} AND retired IS NULL");
			if ($charCheck->rowCount()) {
				$system = $charCheck->fetchColumn();
				require_once(FILEROOT.'/includes/Systems.class.php');
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
			global $mysql;

			if ($userID == null) 
				$userID = $this->userID;
			else 
				$userID = intval($userID);

			$charCheck = $mysql->query("SELECT c.characterID FROM characters c LEFT JOIN players p ON c.gameID = p.gameID AND p.isGM = 1 WHERE c.characterID = {$characterID} AND (c.userID = {$userID} OR p.userID = {$userID})");
			if ($charCheck->rowCount()) 
				return 'edit';
			else 
				return $mongo->characters->findOne(array('characterID' => $this->characterID, 'library.inLibrary' => true))?'library':false;
		}

		public function saveCharacter($characterID) {
			global $mysql, $mongo, $currentUser;

			$characterID = (int) $characterID;
			if ($characterID <= 0) 
				displayJSON(array('failed' => true, 'errors' => array('noCharacterID')));
			$system = $mysql->query("SELECT system FROM characters WHERE characterID = {$characterID}");
			if ($system->rowCount() == 0) 
				displayJSON(array('failed' => true, 'errors' => array('noCharacter')));
			$system = $system->fetchColumn();

			require_once(FILEROOT.'/includes/Systems.class.php');
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
			global $currentUser, $mysql, $mongo;

			$characterID = intval($_POST['characterID']);
			$charAllowed = $mysql->query("SELECT userID FROM characters WHERE retired IS NULL AND characterID = {$characterID} AND userID = {$currentUser->userID}");
			if ($charAllowed->rowCount()) {
				$currentState = $mongo->characters->findOne(array('characterID' => $characterID), array('library' => true));
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
			require_once('../includes/Systems.class.php');
			$charClass = Systems::systemClassName($system).'Character';
			if ($character = new $charClass($characterID)) {
				$character->delete();
#				$hl_charDeleted = new HistoryLogger('characterDeleted');
#				$hl_charDeleted->addCharacter($characterID, false)->save();

				displayJSON(array('success' => true, 'charDeleted' => true));
			}
		}

		public function toggleFavorite() {
			global $currentUser, $mysql, $mongo;

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

		public function cilSearch() {
			global $mysql;

			$type = sanitizeString($_POST['type']);
			$search = sanitizeString($_POST['search'], 'search_format');
			$characterID = intval($_POST['characterID']);
			$system = $_POST['system'];
			require_once('../includes/Systems.class.php');
			$systems = Systems::getInstance();
			$systemOnly = isset($_POST['systemOnly']) && $_POST['systemOnly']?true:false;
			
			if ($systems->verifySystem($system)) {
				$rItems = $mysql->prepare("SELECT sacm.itemID, il.name, sacm.itemID IS NOT NULL systemItem FROM charAutocomplete il LEFT JOIN system_charAutocomplete_map sacm ON sacm.system = '{$system}' AND sacm.itemID = il.itemID WHERE il.type = ?".($systemOnly?" AND sacm.system = '{$system}'":'')." AND il.name LIKE ? ORDER BY systemItem DESC, il.name LIMIT 5");
				$rItems->execute(array($type, "%{$search}%"));
				$items = array();
				foreach ($rItems as $item) 
					$items[] = array(
						'itemID' => (int) $item['itemID'],
						'name' => $item['name'],
						'systemItem' => $item['systemItem']?true:false
					);
				displayJSON(array('items' => $items));
			}
		}

		public function getUAI() {
			global $loggedIn, $currentUser, $mysql, $mongo;

			if (!$loggedIn || !$currentUser->checkACP('autocomplete')) 
				displayJSON(array('failed' => true, 'noPermission' => true));

			$rNewItems = $mongo->userAddedItems->find(array('itemID' => null, 'action' => null));
			$newItems = array();
			foreach ($rNewItems as $item) 
				$newItems[] = $item;

			$rAddToSystem = $mongo->userAddedItems->find(array('itemID' => array('$ne' => null), 'action' => null));
			$addToSystem = array();
			foreach ($rAddToSystem as $item) 
				$addToSystem[] = $item;

			displayJSON(array('success' => true, 'newItems' => $newItems, 'addToSystem' => $addToSystem));
		}

		public function processUAI() {
			global $loggedIn, $currentUser, $mysql;

			if (!$loggedIn || !$currentUser->checkACP('autocomplete')) 
				displayJSON(array('failed' => true, 'noPermission' => true));

			$updateName = $mysql->prepare('UPDATE userAddedItems SET name = :name WHERE uItemID = '.intval($_POST['uItemID']));
			$updateName->bindParam(':name', sanitizeString($_POST['name']));
			$updateName->execute();
			$newItemInfo = $mysql->query('SELECT * FROM userAddedItems WHERE uItemID = '.intval($_POST['uItemID']));
			$newItemInfo = $newItemInfo->fetch();

			if ($_POST['action'] == 'add') {
				try {
					$addNewItem = $mysql->prepare("INSERT INTO charAutocomplete SET type = '{$newItemInfo['itemType']}', name = :name, searchName = :searchName, userDefined = {$newItemInfo['user']->userID}");
					$addNewItem->bindParam(':name', $_POST['name']);
					$addNewItem->bindParam(':searchName', sanitizeString($_POST['name'], 'search_format'));
					$addNewItem->execute();
					$itemID = $mysql->lastInsertId();
					$action = 'approved';
				} catch (Exception $e) {
					$findItem = $mysql->prepare("SELECT itemID FROM charAutocomplete WHERE searchName = :searchName");
					$findItem->bindParam(':searchName', sanitizeString($_POST['name'], 'search_format'));
					$findItem->execute();
					$itemID = $findItem->fetchColumn();
					$action = 'duplicate';
				}
				$addSystemRequest = $mysql->query("INSERT INTO userAddedItems SET itemType = '{$newItemInfo['itemType']}', itemID = {$itemID}, addedBy = {$newItemInfo['addedBy']}, addedOn = '{$newItemInfo['addedOn']}', system = '{$newItemInfo['system']}'");
				$mysql->query("UPDATE userAddedItems SET itemID = {$itemID}, system = NULL, action = '{$action}', actedBy = {$currentUser->userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
			} elseif ($_POST['action'] == 'reject') 
				$mysql->query("UPDATE userAddedItems SET action = 'rejected', actedBy = {$currentUser->userID}, actedOn = NOW() WHERE uItemID = ".intval($_POST['uItemID']));
		}
	}
?>