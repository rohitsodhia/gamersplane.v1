<?
	abstract class d20Character extends Character {
		protected $bodyClasses = array('d20Character');

		protected $classes = array();
		protected $experience = 0;
		protected $stats = array('str' => 10, 'dex' => 10, 'con' => 10, 'int' => 10, 'wis' => 10, 'cha' => 10);
		protected $ac = array();
		protected $hp = array();
		protected $speed = 0;
		protected $saves = array ('fort' => array('base' => 0, 'stat' => 'con', 'misc' => 0),
								  'ref' => array('base' => 0, 'stat' => 'dex', 'misc' => 0),
								  'will' => array('base' => 0, 'stat' => 'wis', 'misc' => 0));
		protected $initiative = array('stat' => 'dex', 'misc' => 0);
		protected $attackBonus = array('base' => 0, 'stat' => array('melee' => 'str', 'ranged' => 'dex'), 'misc' => array('melee' => 0, 'ranged' => 0));
		protected $skills = array();
		protected $feats = array();
		protected $items = '';

		public function __construct($characterID, $userID = NULL) {
			parent::__construct($characterID, $userID);

			$this->mongoIgnore['save'][] = 'skills';
			$this->mongoIgnore['save'][] = 'feats';
		}

		public function setClass($class, $level = 1) {
			$this->classes[$class] = $level;
		}

		public function setClasses($classes) {
			if (is_array($classes)) $this->classes = $classes;
		}

		public function removeClass($class) {
			if (isset($this->classes[$class])) unset($this->classes[$class]);
		}
		
		public function getClasses($class = null) {
			if ($class == null) return $this->classes;
			elseif (in_array($class, array_keys($this->classes))) return $this->classes[$class];
			else return false;
		}

		public function displayClasses() {
			array_walk($this->classes, function ($value, $key) {
				echo $key.' - '.$value.'<br>';
			});
		}

		public function getLevel() {
			return array_sum($this->classes);
		}

		public function setStat($stat, $value = 10) {
			if (array_key_exists($stat, $this->stats)) {
				$value = intval($value);
				if ($value > 0) $this->stats[$stat] = $value;
			} else return false;
		}
		
		public function getStat($stat = null) {
			if ($stat == null) return $this->stats;
			elseif (array_key_exists($stat, $this->stats)) return $this->stats[$stat];
			else return false;
		}

		public function getStatMod($stat, $showSign = true) {
			if (array_key_exists($stat, $this->stats)) {
				$mod = floor(($this->stats[$stat] - 10) / 2);
				if ($showSign) $mod = showSign($mod);
				return $mod;
			} else return false;
		}

		public function setAC($key, $value) {
			if (array_key_exists($key, $this->ac)) $this->ac[$key] = intval($value);
			else return false;
		}

		public function getAC($key = null) {
			if ($key == null) return array_merge(array('total' => array_sum($this->ac) + 10), $this->ac);
			elseif (array_key_exists($key, $this->ac)) return $this->ac[$key];
			elseif ($key == 'total') return array_sum($this->ac) + 10;
			else return false;
		}

		public function setHP($key, $value) {
			if (array_key_exists($key, $this->hp)) $this->hp[$key] = intval($value);
			else return false;
		}

		public function getHP($key = null) {
			if (array_key_exists($key, $this->hp)) return $this->hp[$key];
			elseif ($key == null) return $this->hp;
			else return false;
		}

		public function setSpeed($value) {
			$value = intval($value);
			if ($value >= 0) $this->speed = $value;
			else return false;
		}

		public function getSpeed() {
			return $this->speed;
		}

		public function setSave($save, $key, $value) {
			if (array_key_exists($save, $this->saves) && array_key_exists($key, $this->saves[$save])) {
				if ($key == 'stat' && $value != null && d20Character_consts::getStatNames($value)) $this->saves[$save][$key] = $value;
				elseif ($key != 'stat') $this->saves[$save][$key] = intval($value);
				else return false;
			} else return false;
		}

		public function getSave($save = null, $key = null) {
			if (array_key_exists($save, $this->saves)) {
				if ($key == null) return $this->saves[$save];
				elseif (array_key_exists($key, $this->saves[$save])) return $this->saves[$save][$key];
				elseif ($key == 'total') {
					$total = 0;
					foreach ($this->saves[$save] as $value) if (is_numeric($value)) $total += $value;
					$total += $this->getStatMod($this->saves[$save]['stat'], false);
					return $total;
				} else return false;
			} elseif ($save == null) return $this->saves;
			else return false;
		}

		public function setInitiative($key, $value) {
			if (array_key_exists($key, $this->initiative)) {
				if ($key == 'stat' && $value != null && d20Character_consts::getStatNames($value)) $this->initiative[$key] = $value;
				elseif ($key != 'stat') $this->initiative[$key] = intval($value);
				else return false;
			} else return false;
		}

		public function getInitiative($key = null) {
			if ($key == null) return $this->initiative;
			elseif (array_key_exists($key, $this->initiative)) return $this->initiative[$key];
			elseif ($key == 'total') {
				$total = 0;
				foreach ($this->initiative as $value) if (is_numeric($value)) $total += $value;
				$total += $this->getStatMod($this->initiative['stat'], false);
				return $total;
			} else return false;
		}

		public function setAttackBonus($key, $value, $type = null) {
			if (array_key_exists($key, $this->attackBonus)) {
				if (is_array($this->attackBonus[$key]) && array_key_exists($type, $this->attackBonus[$key])) {
					if ($key == 'stat' && $value != null && d20Character_consts::getStatNames($value)) $this->attackBonus[$key][$type] = $value;
					elseif ($key != 'stat') $this->attackBonus[$key][$type] = intval($value);
					else return false;
				} elseif (!is_array($this->attackBonus[$key])) $this->attackBonus[$key] = intval($value);
				else return false;
			} else return false;
		}

		public function getAttackBonus($key = null, $type = null) {
			if ($key == null) return $this->attackBonus;
			elseif ($key == 'total' && $type != null) {
				$total = 0;
				foreach ($this->attackBonus as $value) {
					if (is_array($value) && is_numeric($value[$type])) $total += $value[$type];
					elseif (is_numeric($value[$type])) $total += $value;
				}
				$total += $this->getStatMod($this->attackBonus['stat'][$type], false);
				return $total;
			} elseif (array_key_exists($key, $this->attackBonus)) {
				if (is_array($this->attackBonus[$key]) && array_key_exists($type, $this->attackBonus[$key])) return $this->attackBonus[$key][$type];
				elseif (!is_array($this->attackBonus[$key])) return $this->attackBonus[$key];
				else return false;
			} else return false;
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
	}
?>