<?
	class n_7thsea_2eCharacter extends Character {
		const SYSTEM = '7thsea_2e';

		protected $concept = '';
		protected $nation = '';
		protected $religion = '';
		protected $reputations = '';
		protected $wealth = '';
		protected $arcana = '';
		protected $traits = array('brawn' => 1, 'finesse' => 1, 'resolve' => 1, 'wits' => 1, 'panache' => 1);
		protected $skills = array(
			'aim' => 0,
			'athletics' => 0,
			'brawl' => 0,
			'convince' => 0,
			'empathy' => 0,
			'hide' => 0,
			'intimidate' => 0,
			'notice' => 0,
			'perform' => 0,
			'ride' => 0,
			'sailing' => 0,
			'scholarship' => 0,
			'tempt' => 0,
			'theft' => 0,
			'warfare' => 0,
			'weaponry' => 0
		);
		protected $deathSpiral = 0;
		protected $backgrounds = '';
		protected $advantages = '';
		protected $stories = array('name' => '', 'goal' => '', 'rewards' => '', 'steps' => array());

		public function setConcept($concept) {
			$this->concept = sanitizeString($concept);
		}

		public function getConcept() {
			return $this->concept;
		}

		public function setNation($nation) {
			$this->nation = sanitizeString($nation);
		}

		public function getNation() {
			return $this->nation;
		}

		public function setReligion($religion) {
			$this->religion = sanitizeString($religion);
		}

		public function getReligion() {
			return $this->religion;
		}

		public function setReputations($reputations) {
			$this->reputations = sanitizeString($reputations);
		}

		public function getReputations() {
			return $this->reputations;
		}

		public function setWealth($wealth) {
			$this->wealth = sanitizeString($wealth);
		}

		public function getWealth() {
			return $this->wealth;
		}

		public function setArcana($arcana) {
			$this->arcana = sanitizeString($arcana);
		}

		public function getArcana() {
			return $this->arcana;
		}

		public function setTrait($trait, $value = null) {
			if (array_key_exists($trait, $this->traits))
				$this->traits[$trait] = intval($value) >= 1 && intval($value) <= 5?intval($value):1;
			else
				return false;
		}

		public function getTraits($trait = null) {
			if ($trait == null)
				return $this->traits;
			elseif (array_key_exists($trait, $this->traits))
				return $this->traits[$trait];
			else
				return false;
		}

		public function setSkill($skill, $value = null) {
			if (array_key_exists($skill, $this->skills))
				$this->skills[$skill] = intval($value) >= 0 && intval($value) <= 5?intval($value):0;
			else
				return false;
		}

		public function getSkills($skill = null) {
			if ($skill == null)
				return $this->skills;
			elseif (array_key_exists($skill, $this->skills))
				return $this->skills[$skill];
			else
				return false;
		}

		public function setDeathSpiral($deathSpiral) {
			$this->deathSpiral = (int) $deathSpiral;
		}

		public function getDeathSpiral() {
			return $this->deathSpiral;
		}

		public function setBackgrounds($backgrounds) {
			$this->backgrounds = sanitizeString($backgrounds);
		}

		public function getBackgrounds() {
			return $this->backgrounds;
		}

		public function setAdvantages($advantages) {
			$this->advantages = sanitizeString($advantages);
		}

		public function getAdvantages() {
			return $this->advantages;
		}

		public function setStory($part, $value = null) {
			if (array_key_exists($part, $this->stories) && $part != 'steps') {
				$this->stories[$part] = sanitizeString(value);
			} else
				return false;
		}

		public function addStoryStep($step = null) {
			$this->stories['steps'][] = sanitizeString($step);
		}

		public function getStories($part = null) {
			if ($part == null) {
				return $this->stories;
			} elseif (array_key_exists($part, $this->stories)) {
				return $this->stories[$part];
			} else {
				return false;
			}
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				$this->setConcept($data['concept']);
				$this->setNation($data['nation']);
				$this->setReligion($data['religion']);
				$this->setReputations($data['reputations']);
				$this->setWealth($data['wealth']);
				$this->setArcana($data['arcana']);
				foreach ($data['traits'] as $trait => $value) {
					$this->setTrait($trait, $value);
				}
				foreach ($data['skills'] as $skill => $value) {
					$this->setSkill($skill, $value);
				}
				$this->setDeathSpiral($data['deathSpiral']);
				$this->setBackgrounds($data['backgrounds']);
				$this->setAdvantages($data['advantages']);
				$this->setStory('goal', $data['stories']['goal']);
				$this->setStory('name', $data['stories']['name']);
				$this->setStory('rewards', $data['stories']['rewards']);
				$this->stories['steps'] = [];
				foreach ($data['stories']['steps'] as $step) {
					$this->addStoryStep($step);
				}
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
