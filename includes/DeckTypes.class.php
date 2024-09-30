<?
	class DeckTypes {
		private static $instance;
		protected $deckTypes = array();

		private function __construct() {
			$file = file_get_contents(FILEROOT.'/data/deckTypes.json');
			$deckTypes = json_decode($file, true);
			foreach ($deckTypes as $deck)
				$this->deckTypes[$deck['id']] = $deck;
		}

		public static function getInstance() {
			if (empty(self::$instance))
				self::$instance = new DeckTypes();
			return self::$instance;
		}

		public function getAll() {
			return $this->deckTypes;
		}

		public function getDeck($type) {
			return isset($this->deckTypes[$type])?$this->deckTypes[$type]:false;
		}
	}
?>
