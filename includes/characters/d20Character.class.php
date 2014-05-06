<?
	abstract class d20Character {
		protected $userID ;
		protected $characterID;
		protected $name;
		protected $experience = 0;
		protected $stats = array('str' => 10, 'dex' => 10, 'con' => 10, 'int' => 10, 'wis' => 10, 'cha' => 10);
		protected $ac = array();
		protected $hp = array();
		protected $speed = 0;
		protected $saves = array ('fort' => array('base' => 0, 'magic' => 0, 'misc' => 0),
								  'ref' => array('base' => 0, 'magic' => 0, 'misc' => 0),
								  'will' => array('base' => 0, 'magic' => 0, 'misc' => 0));
		protected $initiative = array();
		protected $attackBonus = array();
		protected $skills = array();
		protected $feats = array();
		protected $items = '';
		protected $notes = '';

		protected $mongoIgnore = array('save' => array('mongoIgnore', 'skills', 'feats'), 'load' => array('_id', 'system'));
		
		public function __construct($characterID, $userID = NULL) {
			require_once(FILEROOT.'/includes/characters/d20Character_consts.class.php');

			$this->characterID = $characterID;
			if ($userID == NULL) $this->userID = intval($_SESSION['userID']);
			else $this->userID = $userID;
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
		
		public function setStat($stat, $value = 10) {
			if (in_array($stat, array_keys($this->stats))) {
				$value = intval($value);
				if ($value > 0) $this->$stat = $value;
			} else return FALSE;
		}
		
		public function getStat($stat = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (in_array($stat, array_keys($this->stats))) return $this->stats[$stat];
			else return FALSE;
		}

		public function getStatMod($stat) {
			if (in_array($stat, array_keys($this->stats))) return showSign(floor(($this->stats[$stat] - 10) / 2));
			else return FALSE;
		}

		public function setAC($key, $value) {
			if (in_array($key, array_keys($this->ac))) $this->ac[$key] = intval($value);
			else return FALSE;
		}

		public function getAC($key = NULL) {
			if ($key == NULL) return array_merge(array('total' => array_sum($this->ac) + 10), $this->ac);
			elseif (in_array($key, array_keys($this->ac))) return $this->ac[$key];
			elseif ($key == 'total') return array_sum($this->ac) + 10;
			else return FALSE;
		}

		public function setHP($key, $value) {
			if (in_array($key, array_keys($this->hp))) $this->hp[$key] = intval($value);
			else return FALSE;
		}

		public function getHP($key = NULL) {
			if (in_array($key, array_keys($this->hp))) return $this->hp[$key];
			elseif ($key == NULL) return $this->hp;
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
				elseif ($key == 'total') return array_sum($this->saves[$save]);
				else return FALSE;
			} elseif ($save == NULL) return $this->saves;
			else return FALSE;
		}

		public function setInitiative($key, $value) {
			if (in_array($key, array_keys($this->initiative))) $this->initiative[$key] = intval($value);
			else return FALSE;
		}

		public function getInitiative($key = NULL) {
			if ($key == NULL) return array_merge(array('total' => array_sum($this->initiative) + $this->getStatMod('dex')), $this->initiative);
			elseif (in_array($key, array_keys($this->initiative))) return $this->initiative[$key];
			if ($key == 'total') return array_sum($this->initiative) + $this->getStatMod('dex');
			else return FALSE;
		}

		public function setAttackBonus($key, $value, $type = NULL) {
			if (in_array($key, array_keys($this->attackBonus))) {
				if (is_array($this->attackBonus[$key]) && in_array($type, array_keys($this->attackBonus[$key]))) $this->attackBonus[$key][$type] = intval($value);
				elseif (!is_array($this->attackBonus[$key])) $this->attackBonus[$key] = $value;
				else return FALSE;
			} else return FALSE;
		}

		public function getAttackBonus($key = NULL, $type = NULL) {
			if ($key == NULL) return $this->attackBonus;
			elseif ($key == 'total' && $type != NULL) {
				$total = 0;
				foreach ($this->attackBonus as $value) {
					if (is_array($value)) $total += $value[$type];
					else $total += $value;
				}
			} elseif (in_array($key, array_keys($this->attackBonus))) {
				if (is_array($this->attackBonus[$key]) && in_array($type, array_keys($this->attackBonus[$key]))) return $this->attackBonus[$key][$type];
				elseif (!is_array($this->attackBonus[$key])) return $this->attackBonus[$key];
				else return FALSE;
			} else return FALSE;
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

			$classVars = get_object_vars($this);
			foreach ($this->mongoIgnore['save'] as $key) unset($classVars[$key]);
			$classVars = array_merge(array('system' => $this::SYSTEM), $classVars);
			$mongo->characters->update(array('characterID' => $this->characterID), $classVars, array('upsert' => TRUE));
		}

		public function load() {
			global $mongo;

			$result = $mongo->characters->findOne(array('characterID' => $this->characterID));
			foreach ($result as $key => $value) {
				if (!in_array($key, $this->mongoIgnore['load'])) $this->$key = $value;
			}
		}
	}
?>