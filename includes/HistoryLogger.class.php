<?php
	class HistoryLogger {
		private $history = [];
		private $error = false;

		public function __construct($action = null) {
			$this->history = [
				'action' => '',
				'for' => []
			];
			if ($action) {
				$this->history['action'] = $action;
			}
		}

		public function addAction($action) {
			$this->history['action'] = $action;

			return $this;
		}

		public function addCharacter($characterID, $addGame = true) {
			$cache = HistoryCache::getInstance();
			$this->history['character'] = $cache->fetchCharacter($characterID, $addGame);
			if ($this->history['character'] == null) {
				$this->error = true;
			}
			if ($this->error) {
				return $this;
			}
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
			if ($this->history[$label] == null) {
				$this->error = true;
			}
			if ($this->error) {
				return $this;
			}
			$this->addForUsers($userID);

			return $this;
		}

		public function addGame($gameID) {
			$cache = HistoryCache::getInstance();
			$gameInfo = $cache->fetchGame($gameID);
			if ($gameInfo == null) {
				$this->error = true;
			}
			if ($this->error) {
				return $this;
			}
			$this->addForUsers($gameInfo['gms']);
			unset($gameInfo['gms']);
			$this->history['game'] = $gameInfo;

			return $this;
		}

		public function addDeck($deckID) {
			$mysql = DB::conn('mysql');

			$cache = HistoryCache::getInstance();
			$gameInfo = $cache->fetchGame($gameID);

			$deckID = (int) $deckID;
			$deckInfo = $mysql->query("SELECT gameID, label FROM decks WHERE deckID = {$deckID}")->fetch();
			if ($deckInfo == null) {
				$this->error = true;
			}
			if ($this->error) {
				return $this;
			}
			$this->history['deck'] = [
				'deckID' => $deckID,
				'label' => $deckInfo['label']
			];
			$this->addGame($deckInfo['gameID']);

			return $this;
		}

		public function addField($field, $value) {
			$this->history[$field] = $value;
		}

		public function addForUsers($users) {
			if (!is_array($users)) {
				$users = [$users];
			}
			if (!isset($this->history['for']['users'])) {
				$this->history['for']['users'] = [];
			}

			foreach ($users as $user) {
				if (!in_array($user, $this->history['for']['users'])) {
					$this->history['for']['users'][] = (int) $user;
				}
			}

			return $this;
		}

		public function addForCharacters($characters) {
			if (!is_array($characters)) {
				$characters = [$characters];
			}
			if (!sizeof($characters)) {
				return $this;
			}
			if (!isset($this->history['for']['characters'])) {
				$this->history['for']['characters'] = [];
			}

			foreach ($characters as $character) {
				if (!in_array($character, $this->history['for']['characters'])) {
					$this->history['for']['characters'][] = (int) $character;
				}
			}

			return $this;
		}

		public function save($timestamp = null) {
			$mongo = DB::conn('mongo');

			if ($this->error) {
				return null;
			}

			$this->history['timestamp'] = genMongoDate($timestamp == null ? time() : strtotime($timestamp));
			$mongo->histories->insertOne($this->history);

			return $this->history['_id'];
		}

		public function debug($timestamp = null) {
			if ($this->error) {
				return null;
			}

			$this->history['timestamp'] = genMongoDate($timestamp == null ? time() : strtotime($timestamp));

			var_dump($this->history);
		}
	}
?>
