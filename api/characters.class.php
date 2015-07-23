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
				return false;

			$retired = $mysql->query("SELECT retired FROM characters WHERE characterID = {$characterID} AND retired IS NULL");
			if ($retired->rowCount()) {
				$result = $mongo->characters->findOne(array('characterID' => $characterID));
				displayJSON($result);
				return true;
//				$func = $result->
//				$this->{}_load
			} else 
				return false;
		}

		public function fae_load($data) {

		}
	}
?>