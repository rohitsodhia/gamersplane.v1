<?
	class HistoryLogger {
		private $history = array();

		public function __construct($action = null) {
			$this->history = array(
				'action' => '',
				'for' => array()
			);
			if ($action) 
				$this->history['action'] = $action;
		}

		public function addAction($action) {
			$this->history['action'] = $action;

			return $this;
		}

		public function addCharacter($characterID, $addGame = true) {
			$cache = HistoryCache::getInstance();
			$this->history['character'] = $cache->fetchCharacter($characterID, $addGame);
			if ($addGame && isset($this->history['character']['gameID'])) {
				$this->addGame($this->history['character']['gameID']);
				unset($this->history['character']['gameID']);
			}
			$this->addForUsers($this->history['character']['user']['userID']);
			$this->addForCharacters($characterID);

			return $this;
		}

		public function addUser($userID, $label = 'user') {
			$cache = HistoryCache::getInstance();
			$this->history[$label] = $cache->fetchUser($userID);
			$this->addForUsers($userID);

			return $this;
		}

		public function addGame($gameID) {
			$cache = HistoryCache::getInstance();
			$gameInfo = $cache->fetchGame($gameID);
			$this->addForUsers($gameInfo['gms']);
			unset($gameInfo['gms']);
			$this->history['game'] = $gameInfo;

			return $this;
		}

		public function addDeck($deckID) {
			global $mysql;

			$deckID = (int) $deckID;
			$deckInfo = $mysql->query("SELECT gameID, label FROM decks WHERE deckID = {$deckID}")->fetch();
			$this->history['deck'] = array(
				'deckID' => $deckID,
				'label' => $deckInfo['label']
			);
			$this->addGame($deckInfo['gameID']);

			return $this;
		}

		public function addField($field, $value) {
			$this->history[$field] = $value;
		}

		public function addForUsers($users) {
			if (!is_array($users)) 
				$users = array($users);
			if (!isset($this->history['for']['users'])) 
				$this->history['for']['users'] = array();

			foreach ($users as $user) 
				if (!in_array($user, $this->history['for']['users'])) 
					$this->history['for']['users'][] = (int) $user;

			return $this;
		}

		public function addForCharacters($characters) {
			if (!is_array($characters)) 
				$characters = array($characters);
			if (!sizeof($characters)) 
				return $this;
			if (!isset($this->history['for']['characters'])) 
				$this->history['for']['characters'] = array();

			foreach ($characters as $character) 
				if (!in_array($character, $this->history['for']['characters'])) 
					$this->history['for']['characters'][] = (int) $character;

			return $this;
		}

		public function save($timestamp = null) {
			global $mongo;

			$this->history['timestamp'] = new MongoDate($timestamp == null?time():strtotime($timestamp));

			$mongo->histories->insert($this->history);

			return $this->history['_id'];
		}

		public function debug($timestamp = null) {
			$this->history['timestamp'] = new MongoDate($timestamp == null?time():strtotime($timestamp));

			var_dump($this->history);
		}
	}
?>