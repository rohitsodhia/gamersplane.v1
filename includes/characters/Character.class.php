<?
	abstract class Character {
		protected $bodyClasses = array();

		protected $userID ;
		protected $characterID;
		protected $label;
		public static $charTypes = array('PC', 'NPC', 'Mob');
		protected $charType = 'PC';
		protected $library = array('inLibrary' => false, 'views' => 0);
		protected $game = null;
		protected $name;
		protected $notes;

		protected $linkedTables = array();
		protected $mongoIgnore = array('save' => array('bodyClasses', 'linkedTables', 'mongoIgnore'), 'load' => array('_id', 'system'));
		
		public function __construct($characterID, $userID = null) {
			global $currentUser;

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
				if (is_array($this->$var)) $this->$var = array();
				else $this->$var = null;
			}
		}

		public function getCharacterID() {
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
			global $mysql;

			if ($userID == null) 
				$userID = $this->userID;
			else 
				$userID = intval($userID);

			$charCheck = $mysql->query("SELECT c.characterID FROM characters c LEFT JOIN players p ON c.gameID = p.gameID AND p.isGM = 1 WHERE c.characterID = {$this->characterID} AND (c.userID = $userID OR p.userID = $userID)");
			if ($charCheck->rowCount()) 
				return 'edit';

			$libraryCheck = $mysql->query("SELECT inLibrary FROM characterLibrary WHERE characterID = {$this->characterID} AND inLibrary = 1");
			if ($libraryCheck->rowCount()) 
				return 'library';
			else 
				return false;
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

		public function get() {
			$char = array();
			foreach ($this as $key => $value) 
				if (!in_array($key, array('bodyClasses', 'linkedTables', 'mongoIgnore'))) 
					$char[$key] = $value;
			return $char;
		}

		public function save() {
			global $mongo;

			$classVars = get_object_vars($this);
			foreach ($this->mongoIgnore['save'] as $key) 
				unset($classVars[$key]);
			$classVars = array_merge(array('system' => $this::SYSTEM), $classVars);
			try {
//				array_walk_recursive($classVars, function (&$value, $key) { if (is_string($value)) 
//					$value = mb_convert_encoding($value, 'UTF-8');
//				});
				$mongo->characters->update(array('characterID' => $this->characterID), array('$set' => $classVars), array('upsert' => true));
			} catch (Exception $e) { var_dump($e); }
			addCharacterHistory($this->characterID, 'charEdited');

			displayJSON(array('saved' => true, 'characterID' => $this->characterID));
		}

		public function load() {
			global $mysql, $mongo;

			$retired = $mysql->query("SELECT retired FROM characters WHERE characterID = {$this->characterID} AND retired IS NULL");
			if ($retired->rowCount()) {
				$result = $mongo->characters->findOne(array('characterID' => $this->characterID));
				foreach ($result as $key => $value) 
					if (!in_array($key, $this->mongoIgnore['load'])) 
						$this->$key = $value;
				return true;
			} else 
				return false;
		}

		public function delete() {
			global $currentUser, $mysql, $mongo;

			$mysql->query("UPDATE characters SET gameID = NULL, approved = 0, retired = NOW() WHERE characterID = {$this->characterID}");
			$mongo->characters->update(array('characterID' => $this->characterID), array('$set' => array('retired' => true)));
			
			addCharacterHistory($this->characterID, 'charDeleted', $currentUser->userID, 'NOW()');
		}
	}
?>