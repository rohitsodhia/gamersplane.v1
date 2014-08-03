<?
	class savageworldsCharacter extends Character {
		const SYSTEM = 'savageworlds';

		protected $stats = array('agi' => null, 'sma' => null, 'spi' => null, 'str' => null, 'vig' => null
		);
		protected $derivedStats = array('pace' => 0, 'parry' => 0, 'charisma' => 0, 'toughness' => 0);
		protected $edgesHindrances = '';
		protected $wounds = 0;
		protected $fatigue = 0;
		protected $injuries = '';
		protected $weapons = '';
		protected $equipment = '';
		protected $advances = '';

		protected $linkedTables = array('skills');

		public function setStat($stat, $value = null) {
			if (array_key_exists($stat, $this->stats)) $this->stats[$stat] = intval($value);
			else return FALSE;
		}
		
		public function getStats($stat = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (array_key_exists($stat, $this->stats)) return $this->stats;
			else return FALSE;
		}

		public function setEdgesHindrances($edgesHindrances) {
			$this->edgesHindrances = $edgesHindrances;
		}

		public function getEdgesHindrances() {
			return $this->edgesHindrances;
		}

		public function setWounds($value) {
			$this->wounds = intval($wounds);
		}

		public function getWounds($region) {
			return $this->wounds;
		}

		public function setFatigue($fatigue) {
			$this->fatigue = intval($fatigue);
		}

		public function getFatigue() {
			return $this->fatigue;
		}

		public function setInjuries($injuries) {
			$this->injuries = $injuries;
		}

		public function getInjuries() {
			return $this->injuries;
		}

		public function setWeapons($weapons) {
			$this->weapons = $weapons;
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setEquipment($equipment) {
			$this->equipment = $equipment;
		}

		public function getEquipment() {
			return $this->equipment;
		}

		public function setAdvances($advances) {
			$this->advances = $advances;
		}

		public function getAdvances() {
			return $this->advances;
		}

		public function save() {
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				foreach ($data['stats'] as $stat => $value) {
					$this->setStat($stat, 'dice', $value['numDice'].'d'.$value['typeDice']);
					$this->setStat($stat, 'skills', $value['skills']);
				}
				$this->setEdgesHindrances($data['edgesHindrances']);
				$this->setWounds($data['wounds']);
				$this->setFatigue($data['fatigue']);
				$this->setInjuries($data['injuries']);
				$this->setWeapons($data['weapons']);
				$this->setEquipment($data['equipment']);
				$this->setAdvances($data['advances']);
			}

			parent::save();
		}
	}
?>