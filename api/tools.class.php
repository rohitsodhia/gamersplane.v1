<?
	class tools {
		function __construct() {
			global $loggedIn, $pathOptions;

			if ($pathOptions[0] == 'get') 
				$this->get();
			elseif ($pathOptions[0] == 'getDeckTypes') 
				$this->getDeckTypes();
			else 
				displayJSON(array('failed' => true));
		}

		public function getDeckTypes() {
			require_once('../includes/DeckTypes.class.php');
			$deckTypes = DeckTypes::getInstance()->getAll();
			displayJSON(array('success' => true, 'types' => $deckTypes));
		}
	}
?>