<?
	class deadlandsCharacter extends Character {
		const SYSTEM = 'deadlands';

		protected $stats = array(
			'cog' => array('dice' => '', 'skills' => 'Search - 1'), 
			'kno' => array('dice' => '', 'skills' => "Area Knowledge: Home County - 2\nLanguage: Native Tongue - 2"), 
			'mie' => array('dice' => '', 'skills' => ''), 
			'sma' => array('dice' => '', 'skills' => ''), 
			'spi' => array('dice' => '', 'skills' => ''), 
			'def' => array('dice' => '', 'skills' => ''), 
			'nim' => array('dice' => '', 'skills' => ''), 
			'str' => array('dice' => '', 'skills' => ''), 
			'qui' => array('dice' => '', 'skills' => ''), 
			'vig' => array('dice' => '', 'skills' => '')
		);
		protected $edgesHindrances = '';
		protected $nightmare = '';
		protected $wounds = array('head' => 0, 'leftHand' => 0, 'rightHand' => 0, 'guts' => 0, 'leftLeg' => 0, 'rightLeg' => 0);
		protected $wind = 0;
		protected $weapons = '';
		protected $arcane = '';
		protected $equipment = '';

		public function setStat($stat, $sub, $value = '') {
			if ($sub != 'dice' && $sub != 'skills') return FALSE;

			if (array_key_exists($stat, $this->stats)) $this->stats[$stat][$sub] = $value;
			else return FALSE;
		}
		
		public function getStats($stat = NULL, $sub = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (array_key_exists($stat, $this->stats) && $sub == NULL) return $this->stats[$stat];
			elseif (array_key_exists($stat, $this->stats)) {
				if ($sub != 'dice' && $sub != 'skills') return FALSE;
				else return $this->stats[$stat][$sub];
			} else return FALSE;
		}

		public function setEdgesHindrances($edgesHindrances) {
			$this->edgesHindrances = $edgesHindrances;
		}

		public function getEdgesHindrances() {
			return $this->edgesHindrances;
		}

		public function setNightmare($nightmare) {
			$this->nightmare = $nightmare;
		}

		public function getNightmare() {
			return $this->nightmare;
		}

		public function setWounds($region, $value) {
			if (array_key_exists($region, $this->wounds)) $this->wounds[$region] = $value;
			else return FALSE;
		}

		public function getWounds($region) {
			if ($region == NULL) return $this->wounds;
			elseif (array_key_exists($region, $this->wounds)) return $this->wounds[$region];
			else return FALSE;
		}

		public function setWind($wind) {
			$this->wind = intval($wind);
		}

		public function getWind() {
			return $this->wind;
		}

		public function setWeapons($weapons) {
			$this->weapons = $weapons;
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setArcane($arcane) {
			$this->arcane = $arcane;
		}

		public function getArcane() {
			return $this->arcane;
		}

		public function setEquipment($equipment) {
			$this->equipment = $equipment;
		}

		public function getEquipment() {
			return $this->equipment;
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
				$this->setNightmare($data['nightmare']);
				foreach ($data['wounds'] as $region => $value) $this->setWounds($region, $value);
				$this->setWind($data['wind']);
				$this->setWeapons($data['weapons']);
				$this->setArcane($data['arcane']);
				$this->setEquipment($data['equipment']);
			}

			parent::save();
		}
	}
?>