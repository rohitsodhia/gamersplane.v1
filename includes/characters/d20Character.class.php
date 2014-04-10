<?
	abstract class d20Character {
		protected $userID ;
		protected $characterID;
		protected $name;
		protected $stats = array('str' => 10, 'dex' => 10, 'con' => 10, 'int' => 10, 'wis' => 10, 'cha' => 10);
		protected $ac = array();
		protected $damage = array();
		protected $speed = 0;
		protected $saves = array ('fort' => array('base' => 0, 'magic' => 0, 'misc' => 0),
								  'ref' => array('base' => 0, 'magic' => 0, 'misc' => 0),
								  'will' => array('base' => 0, 'magic' => 0, 'misc' => 0));
		protected $initiative = array();
		protected $attackBonus = array();
		protected $skills = array();
		protected $items = '';
		protected $experience = 0;
		
		public function __construct($characterID, $userID = NULL) {
			require_once(FILEROOT.'/includes/characters/d20Character_consts.class.php');

			$this->characterID = $characterID;
			if ($userID == NULL) $this->userID = intval($_SESSION['userID']);
			else $this->userID = $userID;

			return $this->checkPermissions();
		}

		public function checkPermissions($userID = NULL) {
			global $mysql;

			if ($userID == NULL) $userID = $this->userID;
			else $userID = intval($userID);

			$charCheck = $mysql->query("SELECT c.characterID FROM characters c LEFT JOIN players p ON c.gameID = p.gameID AND p.isGM = 1 WHERE c.characterID = {$this->characterID} AND (c.userID = $userID OR p.userID = $userID)");
			if ($charCheck->rowCount()) return 'edit';

			$libraryCheck = $mysql->query("SELECT inLibrary FROM characterLibrary WHERE characterID = {$this->characterID} AND inLibrary = 1");
			if ($libraryCheck->rowCount()) {
				$charCheck = $mysql->query("SELECT c.characterID FROM characters c INNER JOIN players p ON c.gameID = p.gameID AND p.isGM = 0 WHERE c.characterID = $characterID AND c.userID != $userID AND p.userID = $userID");
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
			
			return TRUE;
		}

		public function getName() {
			return $this->name;
		}
		
		public function displayName() {
			return $this->name;
		}
		
		public function setStat($stat, $value = 10) {
			if (in_array($stat, array_keys($this->stats))) {
				$value = intval($value);
				if ($value > 0) $this->$stat = $value;
			} else return FALSE;
		}
		
		public function getStat($stat = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (in_array($stat, array_keys($this->stats))) return $this->$stat;
			else return FALSE;
		}

		public function getStatMod($stat) {
			if (in_array($stat, array_keys($this->stats))) return floor($this->$stat / 2);
			else return FALSE;
		}

		public function setAC($key, $value) {
			if (in_array($key, array_keys($this->ac))) $this->ac[$key] = intval($value);
			else return FALSE;
		}

		public function getAC($key = NULL) {
			if ($key == NULL) return array_merge(array('total' => array_sum($this->ac)), $this->ac);
			elseif (in_array($key, array_keys($this->ac))) return $this->ac[$key];
			else return FALSE;
		}

		public function setDamage($key, $value) {
			if (in_array($key, array_keys($this->damage))) $this->damage[$key] = intval($value);
			else return FALSE;
		}

		public function getDamage($key = NULL) {
			if (in_array($key, array_keys($this->damage))) return $this->damage[$key];
			elseif ($key == NULL) return $this->damage;
			else return FALSE;
		}

		public function setSpeed($value) {
			$value = intval($value);
			if ($value > 0) $this->speed = $value;
			else return FALSE;
		}

		public function getSpeed() {
			return $this->speed;
		}

		public function setSave($save, $key, $value) {
			if (in_array($save, array_keys($this->saves))) {
				if (in_array($key, array_keys($this->saves[$save]))) $this->saves[$save][$key] = intval($value);
				else return FALSE;
			} else return FALSE;
		}

		public function getSave($save = NULL, $key = NULL) {
			if (in_array($save, array_keys($this->saves))) {
				if ($key == NULL) return $this->saves[$save];
				elseif (in_array($key, array_keys($this->saves[$save]))) return $this->saves[$save][$key];
				else return FALSE;
			} elseif ($save == NULL) return $this->saves;
			else return FALSE;
		}

		public function setInitiative($key, $value) {
			if (in_array($key, array_keys($this->ac))) $this->ac[$key] = intval($value);
			else return FALSE;
		}

		public function getInitiative($key = NULL) {
			if ($key == NULL) return array_merge(array('total' => array_sum($this->initiative)), $this->initiative);
			elseif (in_array($key, array_keys($this->initiative))) return $this->initiative[$key];
			else return FALSE;
		}

		public function setItems($value) {
			$this->items = $value;
		}

		public function getItems() {
			return $this->items;
		}

		public function setExperience($value) {
			$this->experience = $value;
		}

		public function getExperience() {
			return $this->experience;
		}

		public function save() {
			global $mongo;

			$classVars = array_merge(array('system' => $this::SYSTEM), get_object_vars($this));
			$mongo->characters->update(array('characterID' => $this->characterID), $classVars, array('upsert' => TRUE));
		}

		public function load() {
			global $mongo;

			$result = $mongo->characters->find(array('characterID' => $this->characterID))->getNext();
			foreach ($result as $key => $value) {
				if (!in_array($key, array('_id', 'system'))) $this->$key = $value;
			}
		}
	}
?>