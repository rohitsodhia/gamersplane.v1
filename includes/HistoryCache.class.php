<?
	class HistoryCache {
		private static $instance;
		protected $users = array();
		protected $characters = array();
		protected $games = array();

		private function __construct() {
		}

		public static function getInstance() {
			if (empty(self::$instance)) 
				self::$instance = new HistoryCache();
			return self::$instance;
		}

		public function fetchUser($userID) {
			global $mysql;

			$userID = (int) $userID;
			if ($userID == 0) 
				return null;
			if (!isset($this->users[$userID])) {
				$username = $mysql->query("SELECT username FROM users WHERE userID = ".$userID)->fetchColumn();
				$this->users[$userID] = array(
					'userID' => $userID,
					'username' => $username
				);
			}

			return $this->users[$userID];
		}

		public function fetchCharacter($characterID, $addGame = true) {
			global $mysql, $mongo;

			$characterID = (int) $characterID;
			if (!isset($this->characters[$characterID])) {
				$charInfo = $mongo->characters->findOne(array('characterID' => $characterID), array('characterID', 'system', 'label', 'userID', 'game'));
				if ($charInfo == null) 
					return null;
				$charInfo['username'] = $this->fetchUser($charInfo['userID'])['username'];
				require_once('../includes/Systems.class.php');
				$systems = Systems::getInstance();
				$this->characters[$characterID] = array(
					'characterID' => $charInfo['characterID'],
					'label' => $charInfo['label'],
					'user' => array(
						'userID' => (int) $charInfo['userID'],
						'username' => $charInfo['username']
					),
					'system' => array(
						'short' => $charInfo['system'],
						'label' => $systems->getFullName($charInfo['system'])
					)
				);
				if ($addGame && $charInfo['game'] != null && $charInfo['game']['gameID']) 
					$this->characters[$characterID]['gameID'] = $charInfo['game']['gameID'];
			}

			return $this->characters[$characterID];
		}

		public function fetchGame($gameID) {
			global $mongo;

			$gameID = (int) $gameID;
			if ($gameID == 0) 
				return null;
			if (!isset($this->games[$gameID])) {
				require_once(FILEROOT.'/includes/Systems.class.php');
				$systems = Systems::getInstance();
				$game = $mongo->games->findOne(array('gameID' => $gameID), array('title' => true, 'system' => true, 'gm' => true, 'players' => true, 'decks' => true));
				$this->games[$gameID] = array(
					'gameID' => $gameID,
					'title' => $game['title'],
					'gm' => $game['gm']	,
					'system' => array(
						'short' => $game['system'],
						'label' => $systems->getFullName($game['system'])
					),
					'decks' => $game['decks']
				);
				$gms = array();
				foreach ($game['players'] as $player) 
					if ($player['isGM']) 
						$gms[] = $player['user']['userID'];
				$this->games[$gameID]['gms'] = $gms;
			}

			return $this->games[$gameID];
		}
	}
?>