<?
	class d20CthulhuCharacter extends d20character {
		const SYSTEM = 'd20cthulhu';

		protected $professions = array();
		protected $ac = array('armor' => 0, 'dex' => 0, 'misc' => 0);
		protected $damage = array('total' => 0, 'current' => 0, 'subdual' => 0);
		protected $speed = 0;
		protected $sanity = array('max' => 0, 'current' => 0);
		protected $initiative = array('misc' => 0);
		protected $attackBonus = array('base' => 0, 'misc' => array('melee' => 0, 'ranged' => 0));
		protected $weapons = array();
		protected $feats = array();
		protected $spells = array();

		
		
		public function setProfession($profession, $level = 1) {
			$this->professions[$profession] = $level;
		}

		public function removeProfession($profession) {
			if (isset($this->professions[$profession])) unset($this->professions[$profession]);
		}
		
		public function getProfession($profession = NULL) {
			if ($profession == NULL) return $this->professions;
			elseif (in_array($profession, array_keys($this->professions))) return $this->professions[$profession];
			else return FALSE;
		}

		public function displayProfession($profession = NULL) {
			if ($profession == NULL) {
				return $this->professions;
			} elseif (in_array($profession, array_keys($this->professions))) return $this->professions[$profession];
			else return FALSE;
		}

		public function setSanity($key, $value) {
			if (in_array($key, array_keys($this->sanity))) $this->sanity[$key] = intval($value);
			else return FALSE;
		}

		public function getSanity($key = NULL) {
			if (in_array($key, array_keys($this->sanity))) return $this->sanity[$key];
			elseif ($key == NULL) return $this->sanity;
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
			if (in_array($key, array_keys($this->attackBonus))) {
				if (is_array($this->attackBonus[$key]) && in_array($type, array_keys($this->attackBonus[$key]))) return $this->attackBonus[$key][$type];
				elseif (!is_array($this->attackBonus[$key])) return $this->attackBonus[$key];
				else return FALSE;
			} else return FALSE;
		}
	}
?>