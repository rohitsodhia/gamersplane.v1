<?
	class faeCharacter extends Character {
		const SYSTEM = 'fae';

		protected $fatePoints = array('current' => 3, 'refresh' => 0);
		protected $highConcept = '';
		protected $trouble = '';
		protected $aspects = array();
		protected $approaches = array();
		protected $stunts = array();
		protected $stress = 0;
		protected $consequences = array();

		public function __construct($characterID, $userID = null) {
			foreach (fae_consts::getApproaches() as $approach) 
				$this->approaches[$approach] = 0;
			for ($count = 2; $count <= 6; $count += 2) 
				$this->consequences[$count] = '';
			parent::__construct($characterID, $userID);
		}

		public function setFatePoints($key, $value) {
			if (array_key_exists($key, $this->fatePoints)) 
				$this->fatePoints[$key] = intval($value);
			else 
				return false;
		}

		public function getFatePoints($key = null) {
			if ($key == null) 
				return $this->fatePoints;
			elseif (array_key_exists($key, $this->fatePoints)) 
				return $this->fatePoints[$key];
			else 
				return false;
		}

		public function setHighConcept($highConcept) {
			$this->highConcept = sanitizeString($highConcept);
		}

		public function getHighConcept() {
			return $this->highConcept;
		}

		public function setTrouble($trouble) {
			$this->trouble = sanitizeString($trouble);
		}

		public function getTrouble() {
			return $this->trouble;
		}

		public function addAspect($aspect) {
			if (strlen($aspect->name)) 
				$this->aspects[] = sanitizeString($aspect->name);
		}

		public function setApproaches($key, $value) {
			if (array_key_exists($key, $this->approaches)) 
				$this->approaches[$key] = intval($value);
			else 
				return false;
		}

		public function getApproaches($key = null) {
			if ($key == null) 
				return $this->approaches;
			elseif (array_key_exists($key, $this->approaches)) 
				return $this->approaches[$key];
			else 
				return false;
		}

		public function addStunt($stunt) {
			if (strlen($stunt->name)) {
				$this->stunts[] = sanitizeString($stunt->name);
			}
		}

		public function setStress($value) {
			if ($value >= 0 && $value <= 3) 
				$this->stress = (int) $value;
			else 
				$this->stress = 0;
		}

		public function getStress() {
			return (int) $this->stress;
		}

		public function setConsequences($level, $consequences) {
			$level = (int) $level;
			if (in_array($level, array(2, 4, 6))) 
				$this->consequences[$level] = sanitizeString($consequences);
		}

		public function getConsequences($level = null) {
			if (in_array($level, array(2, 4, 6))) 
				return $this->consequences[$level];
			else
				return $this->consequences;
		}

		public function save($bypass = false) {
			global $mysql;
			if (isset($_POST['character'])) 
				$data = $_POST['character'];
			else 
				$data = $_POST;
			$system = $this::SYSTEM;

			if (!isset($data->create) && !$bypass) {
				$this->setName($data->name);
				$this->setFatePoints('current', $data->fatePoints->current);
				$this->setFatePoints('refresh', $data->fatePoints->refresh);

				$this->setHighConcept($data->highConcept);
				$this->setTrouble($data->trouble);
				$this->clearVar('aspects');
				if (sizeof($data->aspects)) 
					foreach ($data->aspects as $aspect) 
						$this->addAspect($aspect);

				foreach (fae_consts::getApproaches() as $approach) 
					$this->setApproaches($approach, isset($data->approaches->$approach) && (int) $data->approaches->$approach >= 0?(int) $data->approaches->$approach:0);

				$this->clearVar('stunts');
				if (sizeof($data->stunts)) 
					foreach ($data->stunts as $stuntInfo) 
						$this->addStunt($stuntInfo);

				$this->setStress((int) $data->stress >= 0 && (int) $data->stress <= 3?(int) $data->stress:0);

				$this->clearVar('consequences');
				foreach ($data->consequences as $level => $consequence) 
					$this->setConsequences($level, $consequence);

				$this->setNotes($data->notes);
			}

			parent::save();
		}
	}
?>