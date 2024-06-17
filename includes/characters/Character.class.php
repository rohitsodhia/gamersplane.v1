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
		protected $mongoIgnore = [
			'save' => ['bodyClasses', 'linkedTables', 'mongoIgnore'],
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

			list($charUserID, $gameID, $inLibrary, $isGM) = $mysql->query("SELECT characters.userID, characters.gameID, characters.inLibrary, players.isGM FROM characters LEFT JOIN players ON characters.gameID = players.gameID AND players.userID = {$userID} WHERE characters.characterID = {$this->characterID} LIMIT 1")->fetch();
			if ($charUserID == $userID || $isGM) {
				return 'edit';
			}

			return $inLibrary ? 'library' : false;
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
//				if (!in_array($key, ['bodyClasses', 'linkedTables', 'mongoIgnore'))) {
			return $char;
		}

		public function createNew() {
			$this->characterID = mongo_getNextSequence('characterID');
			$this->save(true);
		}

		public function save() {
			$mysql = DB::conn('mysql');

			$classVars = array_merge(array('system' => $this::SYSTEM), get_object_vars($this));
			try {
				// array_walk_recursive($classVars, function (&$value, $key) { if (is_string($value))
				// 	$value = mb_convert_encoding($value, 'UTF-8');
				// });

				$mongo->characters->updateOne(
					['characterID' => $this->characterID],
					['$set' => $classVars],
					['upsert' => true]
				);
				return true;
			} catch (Exception $e) { var_dump($e); }

			return false;
		}

		public function load() {
			$mysql = DB::conn('mysql');
			$mongo = DB::conn('mongo');

			$character = $mongo->characters->findOne(['characterID' => $this->characterID]);
			if ($character == null) {
				return false;
			}
			if ($character['retired'] == null) {
				foreach ($character as $key => $value) {
					if (!in_array($key, $this->mongoIgnore['load'])) {
						$this->$key = $value;
					}
				}
				$this->userID = $character['user']['userID'];

				return true;
			} else {
				return false;
			}
		}

		public function delete() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			if ($this->label == null) {
				$this->game = $mongo->characters->findOne(
					['characterID' => $this->characterID],
					['projection' => ['game' => true]]
				)['game'];
			}
			if ($this->game) {
				$players = $mongo->games->findOne(
					['gameID' => $this->game['gameID']],
					['projection' => ['players' => true]]
				)['players'];
				foreach ($players as &$player) {
					if ($player['user']['userID'] == $this->userID) {
						foreach ($player['characters'] as $key => $character) {
							if ($character['characterID'] == $this->characterID) {
								unset($player['characters'][$key]);
								break;
							}
						}
						$player['characters'] = array_values($player['characters']);
						break;
					}
				}
				$mongo->games->updateOne(
					['gameID' => $this->game['gameID']],
					['$set' => ['players' => $players]]
				);
			}
			$mongo->characters->updateOne(
				['characterID' => $this->characterID],
				['$set' => ['game' => null, 'retired' => genMongoDate()]]
			);

#			$hl_charDeleted = new HistoryLogger('characterDeleted');
#			$hl_charDeleted->addCharacter($this->characterID)->save();
		}

		public function getGameID(){
			if($this->game){
				return $this->game['gameID'];
			}

			return null;
		}
	}
?>
