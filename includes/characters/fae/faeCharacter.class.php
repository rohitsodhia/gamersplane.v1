<?
	class faeCharacter extends Character {
		const SYSTEM = 'fae';

		protected $fatePoints = ['current' => 3, 'refresh' => 0];
		protected $highConcept = '';
		protected $trouble = '';
		protected $aspects = [];
		protected $approaches = [];
		protected $stunts = [];
		protected $stress = 0;
		protected $consequences = [];

		public function __construct($characterID = null, $userID = null) {
			foreach (fae_consts::getApproaches() as $approach) {
				$this->approaches[$approach] = 0;
			}
			for ($count = 2; $count <= 6; $count += 2) {
				$this->consequences[$count] = '';
			}
			parent::__construct($characterID, $userID);
		}

		public function setFatePoints($key, $value) {
			if (array_key_exists($key, $this->fatePoints)) {
				$this->fatePoints[$key] = intval($value);
			} else {
				return false;
			}
		}

		public function setHighConcept($highConcept) {
			$this->highConcept = sanitizeString($highConcept);
		}

		public function setTrouble($trouble) {
			$this->trouble = sanitizeString($trouble);
		}

		public function addAspect($aspect) {
			if (strlen($aspect->name)) {
				$this->aspects[] = sanitizeString($aspect->name);
			}
		}

		public function setApproaches($key, $value) {
			if (array_key_exists($key, $this->approaches)) {
				$this->approaches[$key] = intval($value);
			} else {
				return false;
			}
		}

		public function addStunt($stunt) {
			if (strlen($stunt->name)) {
				$this->stunts[] = sanitizeString($stunt->name);
			}
		}

		public function setStress($value) {
			if ($value >= 0 && $value <= 3) {
				$this->stress = (int) $value;
			} else {
				$this->stress = 0;
			}
		}

		public function setConsequences($level, $consequences) {
			$level = (int) $level;
			if (in_array($level, [2, 4, 6])) {
				$this->consequences[$level] = sanitizeString($consequences);
			}
		}

		public function save($bypass = false) {
			if (isset($_POST['character'])) {
				$data = $_POST['character'];
			} else {
				$data = $_POST;
			}

			if (!isset($data->create) && !$bypass) {
				$this->setName($data->name);
				$this->setFatePoints('current', $data->fatePoints->current);
				$this->setFatePoints('refresh', $data->fatePoints->refresh);

				$this->setHighConcept($data->highConcept);
				$this->setTrouble($data->trouble);
				$this->clearVar('aspects');
				if ($data->aspects && sizeof($data->aspects)) {
					foreach ($data->aspects as $aspect) {
						$this->addAspect($aspect);
					}
				}

				foreach (fae_consts::getApproaches() as $approach) {
					$this->setApproaches($approach, isset($data->approaches->$approach) && (int) $data->approaches->$approach >= 0 ? (int) $data->approaches->$approach : 0);
				}

				$this->clearVar('stunts');
				if ($data->stunts && sizeof($data->stunts)) {
					foreach ($data->stunts as $stuntInfo) {
						$this->addStunt($stuntInfo);
					}
				}

				$this->setStress((int) $data->stress >= 0 && (int) $data->stress <= 3 ? (int) $data->stress : 0);

				$this->clearVar('consequences');
				foreach ($data->consequences as $level => $consequence) {
					$this->setConsequences($level, $consequence);
				}

				$this->setNotes($data->notes);
			}

			// var_dump($this); exit;

			parent::save();
		}
	}
?>
