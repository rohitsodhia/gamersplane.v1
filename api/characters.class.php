<?
	class characters {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'library') 
				$this->library();
			elseif ($pathOptions[0] == 'my') 
				$this->my();
/*			elseif ($pathOptions[0] == 'view' && intval($_POST['pmID'])) 
				$this->displayPM($_POST['pmID']);
			elseif ($pathOptions[0] == 'send') 
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
			$characters = $mysql->prepare("SELECT characterID, label, charType, system, gameID, approved FROM characters WHERE userID = {$userID}".(isset($_POST['system'])?' AND system = :system':'').(isset($_POST['noGame'])?' AND gameID IS NULL':''));
			if (isset($_POST['system'])) 
				$characters->bindValue(':system', $_POST['system']);
			$characters->execute();
			$characters = $characters->fetchAll();
			array_walk($characters, function (&$character, $key) {
				$character['characterID'] = (int) $character['characterID'];
			});

			displayJSON(array('characters' => $characters));
		}
	}
?>