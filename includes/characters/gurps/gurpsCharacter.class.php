<?
	class gurpsCharacter extends Character {
		const SYSTEM = 'gurps';

		protected $stats = array('st' => 0, 'dx' => 0, 'iq' => 0, 'ht' => 0, 'hp' => 0, 'will' => 0, 'per' => 0, 'fp' => 0);
		protected $damage = array('thrown' => 0, 'swing' => 0);
		protected $speed = array('speed' => 0, 'move' => 0);
		protected $languages = '';
		protected $advantages = '';
		protected $disadvantages = '';
		protected $skills = '';
		protected $items = '';

		public function setStat($stat, $value) {
			if (array_key_exists($stat, $this->stats)) {
				$value = intval($value);
				if ($value > 0) 
					$this->stats[$stat] = $value;
			} else 
				return false;
		}
		
		public function getStat($stat = null) {
			if ($stat == null) 
				return $this->stats;
			elseif (array_key_exists($stat, $this->stats)) 
				return $this->stats[$stat];
			else 
				return false;
		}

		public function setDamage($type, $value) {
			if (array_key_exists($type, $this->damage)) {
				$value = intval($value);
				if ($value > 0) 
					$this->damage[$type] = $value;
			} else 
				return false;
		}
		
		public function getDamage($type = null) {
			if ($type == null) 
				return $this->damage;
			elseif (array_key_exists($type, $this->damage)) 
				return $this->damage[$type];
			else 
				return false;
		}

		public function setSpeed($type, $value) {
			if (array_key_exists($type, $this->speed)) {
				$value = floatval($value);
				if ($value > 0) 
					$this->speed[$type] = $value;
			} else 
				return false;
		}
		
		public function getSpeed($type = null) {
			if ($type == null) 
				return $this->speed;
			elseif (array_key_exists($type, $this->speed)) 
				return $this->speed[$type];
			else 
				return false;
		}

		public function setLanguages($languages) {
			$this->languages = sanitizeString($languages);
		}

		public function getLanguages() {
			return $this->languages;
		}

		public function setAdvantages($advantages) {
			$this->advantages = sanitizeString($advantages);
		}

		public function getAdvantages() {
			return $this->advantages;
		}

		public function setDisadvantages($disadvantages) {
			$this->disadvantages = sanitizeString($disadvantages);
		}

		public function getDisadvantages() {
			return $this->disadvantages;
		}

		public function setSkills($skills) {
			$this->skills = sanitizeString($skills);
		}

		public function getSkills() {
			return $this->skills;
		}

		public function setItems($items) {
			$this->items = sanitizeString($items);
		}

		public function getItems() {
			return $this->items;
		}

		public function save() {
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);

				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				foreach ($data['damage'] as $type => $value) $this->setDamage($type, $value);
				foreach ($data['speed'] as $type => $value) $this->setSpeed($type, $value);

				$this->languages = $data['languages'];
				$this->advantages = $data['advantages'];
				$this->disadvantages = $data['disadvantages'];
				$this->skills = $data['skills'];
				$this->items = $data['items'];
				$this->notes = $data['notes'];
			}

			parent::save();
		}
	}
?>