<?php
	require_once(FILEROOT . '/javascript/markItUp/markitup.bbcode-parser.php');

	abstract class Character {
		protected $bodyClasses = [];

		protected $userID;
		protected $characterID;
		protected $label;
		public static $charTypes = ['PC', 'NPC', 'Mob'];
		protected $charType = 'PC';
		protected $created = null;
		protected $library = ['inLibrary' => false, 'views' => 0];
		protected $game = null;
		protected $name;
		protected $notes;

		protected $linkedTables = [];
		protected $dbIgnore = [
			'save' => ['approved', 'bodyClasses', 'charType', 'characterID', 'created', 'dbIgnore', 'game', 'gameID', 'inLibrary', 'label', 'library', 'libraryViews', 'linkedTables', 'name', 'retired', 'userID'],
			'load' => ['_id', 'system', 'user']
		];

		public function __construct($characterID = null, $userID = null) {
			global $currentUser;

			if ($characterID != null) {
				$this->characterID = intval($characterID);
			}
			if ($userID == null) {
				$this->userID = $currentUser->userID;
			} else {
				$this->userID = $userID;
			}

			foreach ($this->bodyClasses as $bodyClass) {
				addBodyClass($bodyClass);
			}
		}

		public function clearVar($var) {
			if (isset($this->$var)) {
				if (is_array($this->$var)) {
					$this->$var = [];
				} else {
					$this->$var = null;
				}
			}
		}

		public static function getBookData() {
			return null;
		}

		public function getID() {
			return $this->characterID;
		}

		public function setLabel($label) {
			$this->label = sanitizeString($label);
		}

		public function getLabel() {
			return $this->label;
		}

		public function setCharType($charType) {
			if (in_array($charType, self::$charTypes)) {
				$this->charType = $charType;
			}
		}

		public function getCharType() {
			return $this->charType;
		}

		public function toggleLibrary() {
			$this->inLibrary = $this->inLibrary ? false : true;
		}

		public function getLibraryStatus() {
			return $this->inLibrary;
		}

		public function addToGame($gameID) {
			$this->game = ['gameID' => $gameID, 'approved' => false];
		}

		public function approveToGame() {
			$this->game['approved'] = true;
		}

		public function removeFromGame() {
			$this->game = null;
		}

		public function checkPermissions($userID = null) {
			global $currentUser;
			$mysql = DB::conn('mysql');

			if ($userID == null) {
				$userID = $this->userID;
			} else {
				$userID = intval($userID);
			}

			$charInfo = $mysql->query("SELECT characters.userID, characters.inLibrary, players.isGM FROM characters LEFT JOIN players ON characters.gameID = players.gameID AND players.userID = {$userID} WHERE characters.characterID = {$this->characterID} LIMIT 1")->fetch();
			if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
				return 'edit';
			}

			return $charInfo['inLibrary'] ? 'library' : false;
		}

		public function showSheet() {
			require_once(FILEROOT . '/characters/' . $this::SYSTEM . '/sheet.php');
		}

		public function showEdit() {
			require_once(FILEROOT . '/characters/' . $this::SYSTEM . '/edit.php');
		}

		public function setName($name) {
			$this->name = sanitizeString($name);
		}

		public function getName() {
			return $this->name;
		}

		public function setNotes($notes) {
			$this->notes = sanitizeString($notes);
		}

		public function getNotes($pr = false) {
			$notes = $this->notes;
			if ($pr) {
				$notes = printReady($notes);
			}
			return $notes;
		}

		public function getForumTop($postAuthor, $isGM) {
			global $currentUser;

			if ($this->checkPermissions($currentUser->userID) == 'edit') {
?>
					<p class="charName"><a href="/characters/<?=$this::SYSTEM?>/<?=$this->characterID?>/"><?=$this->name?></a></p>
<?			} else { ?>
					<p class="charName"><?=$this->name?></p>
<?			} ?>
			<p class="posterName"><a href="/user/<?=$postAuthor->userID?>/" class="username"><?=$postAuthor->username?></a><?=$isGM?' <img src="/images/gm_icon.png">':''?><?=User::inactive($postAuthor->lastActivity)?></p>
<?
		}

		public function getAvatar($showTS = true) {
			return Character::getCharacterAvatar($this->characterID,$showTS);
		}

		public static function getCharacterAvatar($characterID,$showTS = true) {
			if (file_exists(FILEROOT . "/characters/avatars/{$characterID}.jpg")) {
				return "/characters/avatars/{$characterID}.jpg" . ($showTS ? '?' . time() : '');
			} else {
				return false;
			}
		}

		protected function prElement($ele) {
			if (is_object($ele) || is_array($ele)) {
				foreach ($ele as $key => &$value) {
					$value = $this->prElement($value);
				}
			} else {
				$ele = printReady($ele);
			}

			return $ele;
		}

		public function get($pr = false, $bb = false) {
			$char = get_object_vars($this);
			if ($pr) {
				$char = $this->prElement($char);
				$char['notes'] = BBCode2Html($char['notes']);
			}
			return $char;
		}

		public function save() {
			$mysql = DB::conn('mysql');

			$classVars = get_object_vars($this);
			$setValues = [
				'userID' => $classVars['userID'],
				'label' => $classVars['label'],
				'name' => $classVars['name'],
				'charType' => $classVars['charType'],
				'system' => $this::SYSTEM,
				'inLibrary' => $classVars['library']['inLibrary'] ? 1 : 0,
				'libraryViews' => $classVars['library']['views']
			];
			foreach (array_keys($setValues) as $key) {
				unset($classVars[$key]);
			}
			foreach ($this->dbIgnore['save'] as $key) {
				unset($classVars[$key]);
			}
			$setValues['data'] = json_encode($classVars);
			try {
				$preparedValues = array_map(function ($value) {
					return "`{$value}` = :{$value}";
				}, array_keys($setValues));
				if ($this->characterID) {
					$saveCharacter = $mysql->prepare("UPDATE characters SET " . implode(', ', $preparedValues) . " WHERE characterID = {$this->characterID} LIMIT 1");
				} else {
					$saveCharacter = $mysql->prepare("INSERT INTO characters SET " . implode(', ', $preparedValues));
				}
				$saveCharacter->execute($setValues);
				if (!$this->characterId) {
					$this->characterID = $mysql->lastInsertID();
				}
				return true;
			} catch (Exception $e) { var_dump($e); }

			return false;
		}

		public function load() {
			$mysql = DB::conn('mysql');

			$getCharacter = $mysql->query("SELECT * FROM characters WHERE characterID = {$this->characterID} LIMIT 1");
			if (!$getCharacter->rowCount()) {
				return false;
			}
			$character = $getCharacter->fetch();
			if ($character['retired'] == null) {
				$data = json_decode($character['data'], true);
				unset($character['data']);
				foreach (array_merge($character, $data) as $key => $value) {
					if (!in_array($key, $this->dbIgnore['load'])) {
						$this->$key = $value;
					}
				}

				return true;
			} else {
				return false;
			}
		}

		public function delete() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$mysql->query("UPDATE characters SET gameID = NULL, retired = NOW(), inLibrary = 0 WHERE characterID = {$this->characterID} LIMIT 1");
			$mysql->query("DELETE FROM characterLibrary_favorites WHERE characterID = {$this->characterID}");
		}

		public function getGameID(){
			return (int) $this->gameID ?? null;
		}
	}
?>
