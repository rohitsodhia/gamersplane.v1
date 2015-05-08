<?
	class primevalCharacter extends Character {
		const SYSTEM = 'primeval';

		protected $attributes = array(
			'awareness' => array('starting' => 0, 'current' => 0),
			'coordination' => array('starting' => 0, 'current' => 0),
			'ingenuity' => array('starting' => 0, 'current' => 0),
			'presence' => array('starting' => 0, 'current' => 0),
			'resolve' => array('starting' => 0, 'current' => 0),
			'strength' => array('starting' => 0, 'current' => 0)
		);
		protected $storyPoints = 0;
		protected $skills = array();
		protected $traits = array();
		protected $equipment = '';

		public function setAttributes($attribute, $key, $value = '') {
			if (array_key_exists($attribute,$this->attributes) && in_array($key, array('starting', 'current'))) {
				$value = intval($value);
				if ($value >= 0) 
					$this->attributes[$attribute] = $value;
			} else 
				return false;
		}
		
		public function getAttributes($attribute = null, $key = null) {
			if ($attribute == null) 
				return $this->attribute;
			elseif (array_key_exists($attribute, $this->attributes)) {
				if (in_array($key, array('starting', 'current'))) 
					return $this->attributes[$attribute][$key];
				elseif ($key == null) 
					return $this->attributes[$attribute];
				else 
					return false;
			}
			else 
				return false;
		}

		public function setStoryPoints($storyPoints) {
			$this->storyPoints = intval($storyPoints);
		}

		public function getStoryPoints() {
			return $this->storyPoints;
		}

		public function setEquipment($equipment) {
			$this->equipment = sanitizeString($equipment);
		}

		public function getEquipment() {
			return $this->equipment;
		}

		public function save() {
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				$this->setQualities($data['qualities']);
				$this->setDrawbacks($data['drawbacks']);
				$this->setSkills($data['skills']);
				$this->setPowers($data['powers']);
				$this->setWeapons($data['weapons']);
				$this->setPosessions($data['posessions']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>