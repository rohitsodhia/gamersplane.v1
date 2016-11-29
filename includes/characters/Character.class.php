<?
	abstract class Character {
		protected $bodyClasses = array();

		protected $userID;
		protected $characterID;
		protected $label;
		public static $charTypes = array('PC', 'NPC', 'Mob');
		protected $charType = 'PC';
		protected $created = null;
		protected $library = array('inLibrary' => false, 'views' => 0);
		protected $game = null;
		protected $name;
		protected $notes;

		protected $linkedTables = array();
		protected $mongoIgnore = array('save' => array('bodyClasses', 'linkedTables', 'mongoIgnore'), 'load' => array('_id', 'system', 'user'));

		public function __construct($characterID = null, $userID = null) {
			global $currentUser;

			if ($characterID != null)
				$this->characterID = intval($characterID);
			if ($userID == null)
				$this->userID = $currentUser->userID;
			else
				$this->userID = $userID;

			foreach ($this->bodyClasses as $bodyClass)
				addBodyClass($bodyClass);
		}

		public function clearVar($var) {
			if (isset($this->$var)) {
				if (is_array($this->$var))
					$this->$var = array();
				else
					$this->$var = null;
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
			if (in_array($charType, self::$charTypes))
				$this->charType = $charType;
		}

		public function getCharType() {
			return $this->charType;
		}

		public function toggleLibrary() {
			$this->inLibrary = $this->inLibrary?false:true;
		}

		public function getLibraryStatus() {
			return $this->inLibrary;
		}

		public function addToGame($gameID) {
			$this->game = array('gameID' => $gameID, 'approved' => false);
		}

		public function approveToGame() {
			$this->game['approved'] = true;
		}

		public function removeFromGame() {
			$this->game = null;
		}

		public function checkPermissions($userID = null) {
			global $mysql, $mongo, $currentUser;

			if ($userID == null)
				$userID = $this->userID;
			else
				$userID = intval($userID);

			$charCheck = $mongo->characters->findOne(array('characterID' => $this->characterID), array('user' => true, 'game' => true));
			if ($charCheck['user']['userID'] == $userID)
				return 'edit';
			else {
				$gmCheck = $mongo->games->findOne(array('gameID' => $charCheck['game']['gameID'], 'players' => array('$elemMatch' => array('user.userID' => $userID, 'isGM' => true))), array('_id' => true));
				if ($gmCheck)
					return 'edit';
			}
			return $mongo->characters->findOne(array('characterID' => $this->characterID, 'library.inLibrary' => true))?'library':false;
		}

		public function showSheet() {
			require_once(FILEROOT.'/characters/'.$this::SYSTEM.'/sheet.php');
		}

		public function showEdit() {
			require_once(FILEROOT.'/characters/'.$this::SYSTEM.'/edit.php');
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
			if ($pr)
				$notes = printReady($notes);
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
			if (file_exists(FILEROOT."/characters/avatars/{$this->characterID}.jpg"))
				return "/characters/avatars/{$this->characterID}.jpg".($showTS?'?'.time():'');
			else
				return false;
		}

		protected function prElement($ele) {
			if (is_object($ele) || is_array($ele))
				foreach ($ele as $key => &$value)
					$value = $this->prElement($value);
			else
				$ele = printReady($ele);

			return $ele;
		}

		public function get($pr) {
			$char = get_object_vars($this);
			if ($pr)
				$char = $this->prElement($char);
//				if (!in_array($key, array('bodyClasses', 'linkedTables', 'mongoIgnore'))) {
			return $char;
		}

		public function createNew() {
			$this->characterID = mongo_getNextSequence('characterID');
			$this->save(true);
		}

		public function save() {
			global $mongo, $mysql;

			$classVars = get_object_vars($this);
			foreach ($this->mongoIgnore['save'] as $key)
				unset($classVars[$key]);
			$classVars = array_merge(array('system' => $this::SYSTEM), $classVars);
			if ($classVars['created'] == null)
				$classVars['created'] = new MongoDate();
			try {
//				array_walk_recursive($classVars, function (&$value, $key) { if (is_string($value))
//					$value = mb_convert_encoding($value, 'UTF-8');
//				});
				$username = $mysql->query("SELECT username FROM users WHERE userID = {$classVars['userID']}")->fetchColumn();
				$classVars['user'] = array('userID' => $classVars['userID'], 'username' => $username);
				unset($classVars['userID']);
				$mongo->characters->update(array('characterID' => $this->characterID), array('$set' => $classVars), array('upsert' => true));
				return true;
			} catch (Exception $e) { var_dump($e); }
			return false;
		}

		public function load() {
			global $mysql, $mongo;

			$character = $mongo->characters->findOne(array('characterID' => $this->characterID));
			if ($character == null)
				return false;
			if ($character['retired'] == null) {
				foreach ($character as $key => $value)
					if (!in_array($key, $this->mongoIgnore['load']))
						$this->$key = $value;
				$this->userID = $character['user']['userID'];
				return true;
			} else
				return false;
		}

		public function delete() {
			global $currentUser, $mongo;

			if ($this->label == null)
				$this->game = $mongo->characters->findOne(array('characterID' => $this->characterID), array('game'))['game'];
			if ($this->game) {
				$players = $mongo->games->findOne(array('gameID' => $this->game['gameID']), array('players' => true))['players'];
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
				$mongo->games->update(array('gameID' => $this->game['gameID']), array('$set' => array('players' => $players)));
			}
			$mongo->characters->update(array('characterID' => $this->characterID), array('$set' => array('game' => null, 'retired' => new MongoDate())));

#			$hl_charDeleted = new HistoryLogger('characterDeleted');
#			$hl_charDeleted->addCharacter($this->characterID)->save();
		}
	}
?>
