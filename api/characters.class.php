<?
	class characters {
		public static $charTypes = array('PC', 'NPC', 'Mob');

		function __construct() {
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
			else 
				displayJSON(array('failed' => true));
		}

		public function my() {
			global $loggedIn, $currentUser, $mysql;
			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'notLoggedIn' => true), true);

			require_once('../includes/Systems.class.php');
			$systems = Systems::getInstance();

			$userID = $currentUser->userID;
			$characters = $mysql->prepare("SELECT c.characterID, c.label, c.charType, c.system, c.gameID, c.approved, IF(l.characterID IS NOT NULL AND l.inLibrary = 1, 1, 0) inLibrary FROM characters c LEFT JOIN characterLibrary l ON c.characterID = l.characterID WHERE c.retired IS NULL AND c.userID = {$userID}".(isset($_POST['system'])?' AND c.system = :system':'').(isset($_POST['noGame'])?' AND c.gameID IS NULL':''));
			if (isset($_POST['system'])) 
				$characters->bindValue(':system', $_POST['system']);
			$characters->execute();
			$characters = $characters->fetchAll();
			foreach ($characters as &$character) {
				$character['characterID'] = (int) $character['characterID'];
				$character['system'] = array('short' => $character['system'], 'name' => $systems->getFullName($character['system']));
				$character['gameID'] = (int) $character['gameID'];
				$character['approved'] = (bool) $character['approved'];
				$character['inLibrary'] = (bool) $character['inLibrary'];
			};
			$return = array('characters' => $characters);
			if (isset($_POST['library']) && $_POST['library']) {
				$libraryItems = $mysql->query("SELECT c.characterID, c.label, c.charType, c.system, u.username, u.userID FROM characterLibrary_favorites f INNER JOIN characters c ON f.characterID = c.characterID INNER JOIN users u ON c.userID = u.userID WHERE c.retired IS NULL AND f.userID = {$currentUser->userID} ORDER BY c.charType, c.label")->fetchAll();
				foreach ($libraryItems as &$item) {
					$item['characterID'] = (int) $item['characterID'];
					$item['system'] = array('short' => $item['system'], 'name' => $systems->getFullName($item['system']));
					$item['user'] = array('userID' => (int) $item['userID'], 'username' => $item['username']);
					unset($item['userID'], $item['username']);
				}
				$return['library'] = $libraryItems;
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
				addCharacterHistory($characterID, 'charCreated', $currentUser->userID, 'NOW()', $system);

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
				addCharacterHistory($characterID, 'basicEdited', $currentUser->userID);
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
						displayJSON($character->get());
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

			$libraryCheck = $mysql->query("SELECT inLibrary FROM characterLibrary WHERE characterID = {$this->characterID} AND inLibrary = 1");
			if ($libraryCheck->rowCount()) 
				return 'library';
			else 
				return false;
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
				} else 
					displayJSON(array('failed' => true, 'errors' => array('noPermission')));
			}
		}

		public function toggleLibrary() {
			global $currentUser, $mysql, $mongo;

			$characterID = intval($_POST['characterID']);
			$charAllowed = $mysql->query("SELECT userID FROM characters WHERE retired IS NULL AND characterID = {$characterID} AND userID = {$currentUser->userID}");
			if ($charAllowed->rowCount()) {
				$currentState = $mysql->query("SELECT inLibrary FROM characterLibrary WHERE characterID = {$characterID}");
				if ($currentState->rowCount()) 
					$currentState = (bool) $currentState->fetchColumn();
				else 
					$currentState = false;
				addCharacterHistory($characterID, ($currentState?'removeFrom':'addTo').'Library');

				$mysql->query("INSERT INTO characterLibrary SET characterID = {$characterID} ON DUPLICATE KEY UPDATE inLibrary = ".($currentState?0:1));
				$mongo->characters->update(array('characterID' => $characterID), array('$set' => array('inLibrary' => !$currentState)));

				displayJSON(array('success' => true, 'state' => !$currentState));
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

				displayJSON(array('success' => true, 'charDeleted' => true));
			}
		}

		public function toggleFavorite() {
			global $currentUser, $mysql, $mongo;

			$characterID = intval($_POST['characterID']);
			$charCheck = $mysql->query("SELECT inLibrary FROM characterLibrary WHERE characterID = {$characterID} AND inLibrary = 1");
			if ($charCheck->rowCount()) {
				$unfavorited = $mysql->query("DELETE FROM characterLibrary_favorites WHERE userID = {$currentUser->userID} AND characterID = $characterID");
				$state = $unfavorited->rowCount()?'unfavorited':'favorited';
				if ($state == 'favorited') 
					$mysql->query("INSERT INTO characterLibrary_favorites SET userID = {$currentUser->userID}, characterID = {$characterID}");
				addCharacterHistory($characterID, ($state == 'favorited'?'charFavorited':'charUnfavorited'));
				displayJSON(array('success' => true, 'state' => $state));
			} else 
				displayJSON(array('failed' => true, 'errors' => array('noChar')));
		}
	}
?>