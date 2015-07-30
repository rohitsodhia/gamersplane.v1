<?
	class characters {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'library') 
				$this->library();
			elseif ($pathOptions[0] == 'my') 
				$this->my();
			elseif ($pathOptions[0] == 'load' && intval($_POST['characterID'])) 
				$this->loadCharacter($_POST['characterID']);
			elseif ($pathOptions[0] == 'save' && intval($_POST['characterID'])) 
				$this->saveCharacter($_POST['characterID']);
/*			elseif ($pathOptions[0] == 'send') 
				$this->sendPM();
			elseif ($pathOptions[0] == 'delete' && intval($_POST['pmID'])) 
				$this->deletePM($_POST['pmID']);*/
			else 
				displayJSON(array('failed' => true));
		}

		public function my() {
			global $loggedIn, $currentUser, $mysql;
			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'notLoggedIn' => true), true);

			$userID = $currentUser->userID;
			$characters = $mysql->prepare("SELECT characterID, label, charType, system, gameID, approved FROM characters WHERE retired IS NULL AND userID = {$userID}".(isset($_POST['system'])?' AND system = :system':'').(isset($_POST['noGame'])?' AND gameID IS NULL':''));
			if (isset($_POST['system'])) 
				$characters->bindValue(':system', $_POST['system']);
			$characters->execute();
			$characters = $characters->fetchAll();
			array_walk($characters, function (&$character, $key) {
				$character['characterID'] = (int) $character['characterID'];
			});

			displayJSON(array('characters' => $characters));
		}

		public function loadCharacter($characterID) {
			global $mysql, $mongo;

			$characterID = (int) $characterID;
			if ($characterID <= 0) 
				displayJSON(array('failed' => true, 'errors' => array('noCharacterID')));

			$retired = $mysql->query("SELECT retired FROM characters WHERE characterID = {$characterID} AND retired IS NULL");
			if ($retired->rowCount()) {
				$result = $mongo->characters->findOne(array('characterID' => $characterID));
				displayJSON($result);
				return true;
			} else 
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
			$charClass = $systems->systemClassName($system).'Character';
			if ($character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions == 'edit') {
					$character->save();
				} else 
					displayJSON(array('failed' => true, 'errors' => array('noPermission')));
			}
		}
	}
?>