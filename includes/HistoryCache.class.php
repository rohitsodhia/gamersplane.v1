<?php
	class HistoryCache {
		private static $instance;
		protected $users = [];
		protected $characters = [];
		protected $games = [];

		private function __construct() {
		}

		public static function getInstance() {
			if (empty(self::$instance)) {
				self::$instance = new HistoryCache();
			}
			return self::$instance;
		}

		public function fetchUser($userID) {
			$mysql = DB::conn('mysql');

			$userID = (int) $userID;
			if ($userID == 0) {
				return null;
			}
			if (!isset($this->users[$userID])) {
				$username = $mysql->query("SELECT username FROM users WHERE userID = " . $userID)->fetchColumn();
				$this->users[$userID] = [
					'userID' => $userID,
					'username' => $username
				];
			}

			return $this->users[$userID];
		}

		public function fetchCharacter($characterID, $addGame = true) {
			$mysql = DB::conn('mysql');
			$mongo = DB::conn('mongo');

			$characterID = (int) $characterID;
			if (!isset($this->characters[$characterID])) {
				$charInfo = $mongo->characters->findOne(
					['characterID' => $characterID],
					['projection' => ['characterID' => true, 'system' => true, 'label' => true, 'userID' => true, 'game' => true]]
				);
				if ($charInfo == null) {
					return null;
				}
				$charInfo['username'] = $this->fetchUser($charInfo['userID'])['username'];
				require_once('../includes/Systems.class.php');
				$systems = Systems::getInstance();
				$this->characters[$characterID] = [
					'characterID' => $charInfo['characterID'],
					'label' => $charInfo['label'],
					'user' => [
						'userID' => (int) $charInfo['userID'],
						'username' => $charInfo['username']
					],
					'system' => [
						'short' => $charInfo['system'],
						'label' => $systems->getFullName($charInfo['system'])
					]
				];
				if ($addGame && $charInfo['game'] != null && $charInfo['game']['gameID']) {
					$this->characters[$characterID]['gameID'] = $charInfo['game']['gameID'];
				}
			}

			return $this->characters[$characterID];
		}

		public function fetchGame($gameID) {
			$mongo = DB::conn('mongo');

			$gameID = (int) $gameID;
			if ($gameID == 0) {
				return null;
			}
			if (!isset($this->games[$gameID])) {
				require_once(FILEROOT.'/includes/Systems.class.php');
				$systems = Systems::getInstance();
				$game = $mongo->games->findOne(
					['gameID' => $gameID],
					['projection' => ['title' => true, 'system' => true, 'gm' => true, 'players' => true, 'decks' => true]]
				);
				$this->games[$gameID] = [
					'gameID' => $gameID,
					'title' => $game['title'],
					'gm' => $game['gm']	,
					'system' => [
						'short' => $game['system'],
						'label' => $systems->getFullName($game['system'])
					],
					'decks' => $game['decks']
				];
				$gms = [];
				foreach ($game['players'] as $player) {
					if ($player['isGM']) {
						$gms[] = $player['user']['userID'];
					}
				}
				$this->games[$gameID]['gms'] = $gms;
			}

			return $this->games[$gameID];
		}
	}
?>
