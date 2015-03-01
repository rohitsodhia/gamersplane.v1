<?
	class shadowrun4Character extends Character {
		const SYSTEM = 'shadowrun4';

		protected $metatype = '';
		protected $stats = array('body' => 0, 'agility' => 0, 'reaction' => 0, 'strength' => 0, 'charisma' => 0, 'intuition' => 0, 'logic' => 0, 'willpower' => 0, 'edge_total' => 0, 'edge_current' => 0, 'essence' => 0, 'mag_res' => 0, 'initiative' => 0, 'initiative_passes' => 0, 'matrix_initiative' => 0, 'astral_initiative' => 0);
		protected $qualities = '';
		protected $damage = array('physical' => 0, 'stun' => 0);
		protected $skills = '';
		protected $spells = '';
		protected $weapons = '';
		protected $armor = '';
		protected $augments = '';
		protected $contacts = '';
		protected $items = '';

		public function setMetatype($value) {
			$this->metatype = sanitizeString($value);
		}

		public function getMetatype() {
			return $this->metatype;
		}

		public function setStat($stat, $value = 0) {
			if (array_key_exists($stat, $this->stats)) {
				$value = intval($value);
				if ($value > 0) 
					$this->stats[$stat] = $value;
			} else return false;
		}
		
		public function getStat($stat = null) {
			if ($stat == null) 
				return $this->stats;
			elseif (array_key_exists($stat, $this->stats)) 
				return $this->stats[$stat];
			else 
				return false;
		}

		public function setQualities($value) {
			$this->qualities = sanitizeString($value);
		}

		public function getQualities() {
			return $this->qualities;
		}

		public function setDamage($key, $value) {
			if (in_array($key, array_keys($this->damage))) 
				$this->damage[$key] = intval($value);
			else 
				return false;
		}

		public function getDamage($key = null) {
			if (in_array($key, array_keys($this->damage))) 
				return $this->damage[$key];
			elseif ($key == null) 
				return $this->damage;
			else 
				return false;
		}

		public function setSkills($value) {
			$this->skills = sanitizeString($value);
		}

		public function getSkills() {
			return $this->skills;
		}

		public function setSpells($value) {
			$this->spells = sanitizeString($value);
		}

		public function getSpells() {
			return $this->spells;
		}

		public function setWeapons($value) {
			$this->weapons = sanitizeString($value);
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setArmor($value) {
			$this->armor = sanitizeString($value);
		}

		public function getArmor() {
			return $this->armor;
		}

		public function setAugments($value) {
			$this->augments = sanitizeString($value);
		}

		public function getAugments() {
			return $this->augments;
		}

		public function setContacts($value) {
			$this->contacts = sanitizeString($value);
		}

		public function getContacts() {
			return $this->contacts;
		}

		public function setItems($value) {
			$this->items = sanitizeString($value);
		}

		public function getItems() {
			return $this->items;
		}

		public function save() {
			global $mysql;
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				$this->setMetatype($data['metatype']);
				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				$this->setQualities($data['qualities']);
				foreach ($data['damage'] as $type => $value) $this->setDamage($type, $value);
				$this->setSkills($data['skills']);
				$this->setSpells($data['spells']);
				$this->setWeapons($data['weapons']);
				$this->setArmor($data['armor']);
				$this->setAugments($data['augments']);
				$this->setContacts($data['contacts']);
				$this->setItems($data['items']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>