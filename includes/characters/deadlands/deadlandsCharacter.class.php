<?
	class deadlandsCharacter extends Character {
		const SYSTEM = 'deadlands';

		protected $stats = [
			'cog' => ['dice' => '', 'skills' => 'Search - 1'],
			'kno' => ['dice' => '', 'skills' => "Area Knowledge: Home County - 2\nLanguage: Native Tongue - 2"],
			'mie' => ['dice' => '', 'skills' => ''],
			'sma' => ['dice' => '', 'skills' => ''],
			'spi' => ['dice' => '', 'skills' => ''],
			'def' => ['dice' => '', 'skills' => ''],
			'nim' => ['dice' => '', 'skills' => ''],
			'str' => ['dice' => '', 'skills' => ''],
			'qui' => ['dice' => '', 'skills' => ''],
			'vig' => ['dice' => '', 'skills' => '']
		);
		protected $edgesHindrances = '';
		protected $nightmare = '';
		protected $wounds = ['head' => 0, 'leftHand' => 0, 'rightHand' => 0, 'guts' => 0, 'leftLeg' => 0, 'rightLeg' => 0];
		protected $wind = 0;
		protected $weapons = '';
		protected $arcane = '';
		protected $equipment = '';

		public function setStat($stat, $sub, $value = '') {
			if ($sub != 'dice' && $sub != 'skills') {
				return false;
			}

			if (array_key_exists($stat, $this->stats)) {
				$this->stats[$stat][$sub] = sanitizeString($value);
			} else {
				return false;
			}
		}

		public function getStats($stat = null, $sub = null) {
			if ($stat == null) {
				return $this->stats;
			} elseif (array_key_exists($stat, $this->stats) && $sub == null) {
				return $this->stats[$stat];
			} elseif (array_key_exists($stat, $this->stats)) {
				if ($sub != 'dice' && $sub != 'skills') {
					return false;
				} else {
					return $this->stats[$stat][$sub];
				}
			} else {
				return false;
			}
		}

		public function setEdgesHindrances($edgesHindrances) {
			$this->edgesHindrances = sanitizeString($edgesHindrances);
		}

		public function getEdgesHindrances() {
			return $this->edgesHindrances;
		}

		public function setNightmare($nightmare) {
			$this->nightmare = sanitizeString($nightmare);
		}

		public function getNightmare() {
			return $this->nightmare;
		}

		public function setWounds($region, $value) {
			if (array_key_exists($region, $this->wounds)) {
				$this->wounds[$region] = (int) $value;
			} else {
				return false;
			}
		}

		public function getWounds($region) {
			if ($region == null) {
				return $this->wounds;
			} elseif (array_key_exists($region, $this->wounds)) {
				return $this->wounds[$region];
			} else {
				return false;
			}
		}

		public function setWind($wind) {
			$this->wind = (int) $wind;
		}

		public function getWind() {
			return $this->wind;
		}

		public function setWeapons($weapons) {
			$this->weapons = sanitizeString($weapons);
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setArcane($arcane) {
			$this->arcane = sanitizeString($arcane);
		}

		public function getArcane() {
			return $this->arcane;
		}

		public function setEquipment($equipment) {
			$this->equipment = sanitizeString($equipment);
		}

		public function getEquipment() {
			return $this->equipment;
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				foreach ($data['stats'] as $stat => $value) {
					$this->setStat($stat, 'dice', $value['numDice'].'d'.$value['typeDice']);
					$this->setStat($stat, 'skills', $value['skills']);
				}
				$this->setEdgesHindrances($data['edge_hind']);
				$this->setNightmare($data['nightmare']);
				foreach ($data['wounds'] as $region => $value) {
					$this->setWounds($region, $value);
				}
				$this->setWind($data['wind']);
				$this->setWeapons($data['weapons']);
				$this->setArcane($data['arcane']);
				$this->setEquipment($data['equipment']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
