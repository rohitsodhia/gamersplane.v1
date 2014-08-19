<?
	abstract class Character {
		protected $bodyClasses = array();

		protected $userID ;
		protected $characterID;
		protected $label;
		protected $type = 'PC';
		protected $inLibrary = FALSE;
		protected $game = NULL;
		protected $name;
		protected $notes;

		protected $linkedTables = array();
		protected $mongoIgnore = array('save' => array('bodyClasses', 'linkedTables', 'mongoIgnore'), 'load' => array('_id', 'system'));
		
		public function __construct($characterID, $userID = NULL) {
			$this->characterID = intval($characterID);
			if ($userID == NULL) $this->userID = intval($_SESSION['userID']);
			else $this->userID = $userID;

			foreach ($this->bodyClasses as $bodyClass) addBodyClass($bodyClass);
		}

		public function clearVar($var) {
			if (isset($this->$var)) {
				if (is_array($this->$var)) $this->$var = array();
				else $this->$var = NULL;
			}
		}

		public function setLabel($label) {
			$this->label = $label;
		}

		public function getLabel() {
			return $this->label;
		}

		public function setType($type) {
			global $charTypes;
			if (in_array($type, $charTypes)) $this->type = $type;
		}

		public function getType() {
			return $this->type;
		}

		public function toggleLibrary() {
			$this->inLibrary = $this->inLibrary?FALSE:TRUE;
		}

		public function getLibraryStatus() {
			return $this->inLibrary;
		}

		public function addToGame($gameID) {
			$this->game = array('gameID' => $gameID, 'approved' => FALSE);
		}

		public function approveToGame() {
			$this->game['approved'] = TRUE;
		}

		public function removeFromGame() {
			$this->game = NULL;
		}

		public function checkPermissions($userID = NULL) {
			global $mysql;

			if ($userID == NULL) $userID = $this->userID;
			else $userID = intval($userID);

			$charCheck = $mysql->query("SELECT c.characterID FROM characters c LEFT JOIN players p ON c.gameID = p.gameID AND p.isGM = 1 WHERE c.characterID = {$this->characterID} AND (c.userID = $userID OR p.userID = $userID)");
			if ($charCheck->rowCount()) return 'edit';

			$libraryCheck = $mysql->query("SELECT inLibrary FROM characterLibrary WHERE characterID = {$this->characterID} AND inLibrary = 1");
			if ($libraryCheck->rowCount()) {
				$charCheck = $mysql->query("SELECT c.characterID FROM characters c INNER JOIN players p ON c.gameID = p.gameID AND p.isGM = 0 WHERE c.characterID = {$this->characterID} AND c.userID != $userID AND p.userID = $userID");
				if ($charCheck->rowCount()) return FALSE;
				else return 'library';
			} else return FALSE;
		}
		
		public function showSheet() {
			require_once(FILEROOT.'/characters/'.$this::SYSTEM.'/sheet.php');
		}
		
		public function showEdit() {
			require_once(FILEROOT.'/characters/'.$this::SYSTEM.'/edit.php');
		}
		
		public function setName($name) {
			$this->name = $name;
		}

		public function getName() {
			return $this->name;
		}

		public function setNotes($notes) {
			$this->notes = $notes;
		}

		public function getNotes() {
			return $this->notes;
		}

		public function save() {
			global $mongo;

			$classVars = get_object_vars($this);
			foreach ($this->mongoIgnore['save'] as $key) unset($classVars[$key]);
			$classVars = array_merge(array('system' => $this::SYSTEM), $classVars);
			$mongo->characters->update(array('characterID' => $this->characterID), array('$set' => $classVars), array('upsert' => TRUE));
			addCharacterHistory($this->characterID, 'charEdited');
		}

		public function load() {
			global $mongo;

			$result = $mongo->characters->findOne(array('characterID' => $this->characterID));
			foreach ($result as $key => $value) {
				if (!in_array($key, $this->mongoIgnore['load'])) $this->$key = $value;
			}
		}

		public function delete() {
			global $mysql, $mongo;

			$userID = intval($_SESSION['userID']);

			$mysql->query('DELETE FROM characters WHERE characterID = '.$this->characterID);
			foreach ($this->linkedTables as $table) $mysql->query('DELETE FROM '.$this::SYSTEM.'_'.$table.' WHERE characterID = '.$this->characterID);
			$mongo->characters->remove(array('characterID' => $this->characterID));
			
			addCharacterHistory($this->characterID, 'charDeleted', $userID, 'NOW()');
		}
	}
?>