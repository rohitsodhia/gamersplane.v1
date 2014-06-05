<?
	abstract class d20Character extends Character {
		protected $classes = array();
		protected $experience = 0;
		protected $stats = array('str' => 10, 'dex' => 10, 'con' => 10, 'int' => 10, 'wis' => 10, 'cha' => 10);
		protected $ac = array();
		protected $hp = array();
		protected $speed = 0;
		protected $saves = array ('fort' => array('base' => 0, 'stat' => 'con', 'magic' => 0, 'misc' => 0),
								  'ref' => array('base' => 0, 'stat' => 'dex', 'magic' => 0, 'misc' => 0),
								  'will' => array('base' => 0, 'stat' => 'wis', 'magic' => 0, 'misc' => 0));
		protected $initiative = array('stat' => 'dex', 'misc' => 0);
		protected $attackBonus = array('base' => 0, 'stat' => array('melee' => 'str', 'ranged' => 'dex'), 'misc' => array('melee' => 0, 'ranged' => 0));
		protected $skills = array();
		protected $feats = array();
		protected $items = '';

		protected $mongoIgnore = array('save' => array('mongoIgnore', 'skills', 'feats'), 'load' => array('_id', 'system'));

		public function setClass($class, $level = 1) {
			$this->classes[$class] = $level;
		}

		public function setClasses($classes) {
			if (is_array($classes)) $this->classes = $classes;
		}

		public function removeClass($class) {
			if (isset($this->classes[$class])) unset($this->classes[$class]);
		}
		
		public function getClasses($class = NULL) {
			if ($class == NULL) return $this->classes;
			elseif (in_array($class, array_keys($this->classes))) return $this->classes[$class];
			else return FALSE;
		}

		public function displayClasses() {
			array_walk($this->classes, function ($value, $key) {
				echo $key.' - '.$value.'<br>';
			});
		}

		public function setStat($stat, $value = 10) {
			if (array_key_exists($stat, $this->stats)) {
				$value = intval($value);
				if ($value > 0) $this->stats[$stat] = $value;
			} else return FALSE;
		}
		
		public function getStat($stat = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (array_key_exists($stat, $this->stats)) return $this->stats[$stat];
			else return FALSE;
		}

		public function getStatMod($stat, $showSign = TRUE) {
			if (array_key_exists($stat, $this->stats)) {
				$mod = floor(($this->stats[$stat] - 10) / 2);
				if ($showSign) $mod = showSign($mod);
				return $mod;
			} else return FALSE;
		}

		public function setAC($key, $value) {
			if (array_key_exists($key, $this->ac)) $this->ac[$key] = intval($value);
			else return FALSE;
		}

		public function getAC($key = NULL) {
			if ($key == NULL) return array_merge(array('total' => array_sum($this->ac) + 10), $this->ac);
			elseif (array_key_exists($key, $this->ac)) return $this->ac[$key];
			elseif ($key == 'total') return array_sum($this->ac) + 10;
			else return FALSE;
		}

		public function setHP($key, $value) {
			if (array_key_exists($key, $this->hp)) $this->hp[$key] = intval($value);
			else return FALSE;
		}

		public function getHP($key = NULL) {
			if (array_key_exists($key, $this->hp)) return $this->hp[$key];
			elseif ($key == NULL) return $this->hp;
			else return FALSE;
		}

		public function setSpeed($value) {
			$value = intval($value);
			if ($value >= 0) $this->speed = $value;
			else return FALSE;
		}

		public function getSpeed() {
			return $this->speed;
		}

		public function setSave($save, $key, $value) {
			if (array_key_exists($save, $this->saves) && array_key_exists($key, $this->saves[$save])) {
				if ($key == 'stat' && $value != NULL && d20Character_consts::getStatNames($value)) $this->saves[$save][$key] = $value;
				elseif ($key != 'stat') $this->saves[$save][$key] = intval($value);
				else return FALSE;
			} else return FALSE;
		}

		public function getSave($save = NULL, $key = NULL) {
			if (array_key_exists($save, $this->saves)) {
				if ($key == NULL) return $this->saves[$save];
				elseif (array_key_exists($key, $this->saves[$save])) return $this->saves[$save][$key];
				elseif ($key == 'total') {
					$total = 0;
					foreach ($this->saves[$save] as $value) if (is_numeric($value)) $total += $value;
					$total += $this->getStatMod($this->saves[$save]['stat'], FALSE);
					return $total;
				} else return FALSE;
			} elseif ($save == NULL) return $this->saves;
			else return FALSE;
		}

		public function setInitiative($key, $value) {
			if (array_key_exists($key, $this->initiative)) {
				if ($key == 'stat' && $value != NULL && d20Character_consts::getStatNames($value)) $this->initiative[$key] = $value;
				elseif ($key != 'stat') $this->initiative[$key] = intval($value);
				else return FALSE;
			} else return FALSE;
		}

		public function getInitiative($key = NULL) {
			if ($key == NULL) return $this->initiative;
			elseif (array_key_exists($key, $this->initiative)) return $this->initiative[$key];
			elseif ($key == 'total') {
				$total = 0;
				foreach ($this->initiative as $value) if (is_numeric($value)) $total += $value;
				$total += $this->getStatMod($this->initiative['stat'], FALSE);
				return $total;
			} else return FALSE;
		}

		public function setAttackBonus($key, $value, $type = NULL) {
			if (array_key_exists($key, $this->attackBonus)) {
				if (is_array($this->attackBonus[$key]) && array_key_exists($type, $this->attackBonus[$key])) {
					if ($key == 'stat' && $value != NULL && d20Character_consts::getStatNames($value)) $this->attackBonus[$key][$type] = $value;
					elseif ($key != 'stat') $this->attackBonus[$key][$type] = intval($value);
					else return FALSE;
				} elseif (!is_array($this->attackBonus[$key])) $this->attackBonus[$key] = intval($value);
				else return FALSE;
			} else return FALSE;
		}

		public function getAttackBonus($key = NULL, $type = NULL) {
			if ($key == NULL) return $this->attackBonus;
			elseif ($key == 'total' && $type != NULL) {
				$total = 0;
				foreach ($this->attackBonus as $value) {
					if (is_array($value) && is_numeric($value[$type])) $total += $value[$type];
					elseif (is_numeric($value[$type])) $total += $value;
				}
				$total += $this->getStatMod($this->attackBonus['stat'][$type], FALSE);
				return $total;
			} elseif (array_key_exists($key, $this->attackBonus)) {
				if (is_array($this->attackBonus[$key]) && array_key_exists($type, $this->attackBonus[$key])) return $this->attackBonus[$key][$type];
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
	}
?>