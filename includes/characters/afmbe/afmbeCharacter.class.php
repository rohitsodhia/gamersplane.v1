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
			} else return false;
		}
		
		public function getStat($stat = null) {
			if ($stat == null) return $this->stats;
			elseif (in_array($stat, array_keys($this->stats))) return $this->stats[$stat];
			else return false;
		}

		public function setQualities($qualities) {
			$this->qualities = sanitizeString($qualities);
		}

		public function getQualities() {
			return $this->qualities;
		}

		public function setDrawbacks($drawbacks) {
			$this->drawbacks = sanitizeString($drawbacks);
		}

		public function getDrawbacks() {
			return $this->drawbacks;
		}

		public function setSkills($skills) {
			$this->skills = sanitizeString($skills);
		}

		public function getSkills() {
			return $this->skills;
		}

		public function setPowers($powers) {
			$this->powers = sanitizeString($powers);
		}

		public function getPowers() {
			return $this->powers;
		}

		public function setWeapons($weapons) {
			$this->weapons = sanitizeString($weapons);
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setPosessions($posessions) {
			$this->posessions = sanitizeString($posessions);
		}

		public function getPosessions() {
			return $this->posessions;
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