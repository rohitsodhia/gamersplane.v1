<?
	class afmbeCharacter extends Character {
		const SYSTEM = 'afmbe';

		protected $stats = array('str' => '', 'dex' => '', 'con' => '', 'int' => '', 'per' => '', 'wil' => '', 'lp' => '', 'end' => '', 'spd' => '', 'ess' => '');
		protected $qualities = '';
		protected $drawbacks = '';
		protected $skills = '';
		protected $powers = '';
		protected $weapons = '';
		protected $posessions = '';

		public function setStat($stat, $value = '') {
			if (in_array($stat, array_keys($this->stats))) {
				$value = intval($value);
				if ($value > 0) $this->stats[$stat] = $value;
			} else return FALSE;
		}
		
		public function getStat($stat = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (in_array($stat, array_keys($this->stats))) return $this->stats[$stat];
			else return FALSE;
		}

		public function setQualities($qualities) {
			$this->qualities = $qualities;
		}

		public function getQualities() {
			return $this->qualities;
		}

		public function setDrawbacks($drawbacks) {
			$this->drawbacks = $drawbacks;
		}

		public function getDrawbacks() {
			return $this->drawbacks;
		}

		public function setSkills($skills) {
			$this->skills = $skills;
		}

		public function getSkills() {
			return $this->skills;
		}

		public function setPowers($powers) {
			$this->powers = $powers;
		}

		public function getPowers() {
			return $this->powers;
		}

		public function setWeapons($weapons) {
			$this->weapons = $weapons;
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setPosessions($posessions) {
			$this->posessions = $posessions;
		}

		public function getPosessions() {
			return $this->posessions;
		}

		public function save() {
			$data = $_POST;

			$this->setName($data['name']);
			foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
			$this->setQualities($data['qualities']);
			$this->setDrawbacks($data['drawbacks']);
			$this->setSkills($data['skills']);
			$this->setPowers($data['powers']);
			$this->setWeapons($data['weapons']);
			$this->setPosessions($data['posessions']);
			$this->setNotes($data['notes']);

			parent::save();
		}
	}
?>