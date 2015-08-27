<?
	class wodCharacter extends Character {
		const SYSTEM = 'wod';

		protected $attributes = array('int' => 1, 'str' => 1, 'pre' => 1, 'wit' => 1, 'dex' => 1, 'man' => 1, 'res' => 1, 'sta' => 1, 'com' => 1);
		protected $skills = array();
		protected $merits = '';
		protected $flaws = '';
		protected $traits = array('health' => 0, 'willpower' => 0, 'morality' => 0, 'size' => 0, 'speed' => 0, 'initiativeMod' => 0, 'defense' => 0, 'armor' => 0);
		protected $weapons = '';
		protected $equipment = '';

		public function setAttribute($attribute, $value = 1) {
			if (array_key_exists($attribute, $this->attributes)) {
				$value = intval($value);
				if ($value >= 1 && $value <= 5) 
					$this->attributes[$attribute] = $value;
			} else 
				return false;
		}
		
		public function getAttribute($attribute = null) {
			if ($attribute == null) 
				return $this->attributes;
			elseif (array_key_exists($attribute, $this->attributes)) 
				return $this->attributes[$attribute];
			else 
				return false;
		}

		public function setSkill($skill, $value = 0) {
			$value = (int) $value;
			if ($value >= 0 && $value <= 5) 
				$this->skills[$skill] = $value;
		}
		
		public function getSkill($skill = null) {
			if ($skill == null) 
				return $this->skills;
			elseif (array_key_exists($skill, $this->skills)) 
				return $this->skills[$skill];
			else 
				return false;
		}

		public function setMerits($value) {
			$this->merits = sanitizeString($value);
		}

		public function getMerits() {
			return $this->merits;
		}

		public function setFlaws($value) {
			$this->flaws = sanitizeString($value);
		}

		public function getFlaws() {
			return $this->flaws;
		}

		public function setTrait($trait, $value = 1) {
			if (array_key_exists($trait, $this->traits)) {
				$value = intval($value);
				if ($value >= 1 && $value <= 5) 
					$this->traits[$trait] = $value;
			} else 
				return false;
		}
		
		public function getTrait($trait = null) {
			if ($trait == null) 
				return $this->traits;
			elseif (array_key_exists($trait, $this->traits)) 
				return $this->traits[$trait];
			else 
				return false;
		}

		public function setWeapons($value) {
			$this->weapons = sanitizeString($value);
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setEquipment($value) {
			$this->equipment = sanitizeString($value);
		}

		public function getEquipment() {
			return $this->equipment;
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				foreach ($data['attributes'] as $attribute => $value) 
					$this->setAttribute($attribute, $value);
				foreach ($data['skills'] as $skill => $value) 
					$this->setSkill($skill, $value);
				$this->setMerits($data['merits']);
				$this->setFlaws($data['flaws']);
				foreach ($data['traits'] as $trait => $value) 
					$this->setTrait($trait, $value);
				$this->setWeapons($data['weapons']);
				$this->setEquipment($data['equipment']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>