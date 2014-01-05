<?
	class SWEOTEDice extends Dice {
		private $type;
		private $rawTypes = array(
			'ability' => array(1 => '', 'success', 'success', 'advantage', 'success_success', 'success_advantage', 'advantage_advantage'),
			'proficiency' => array(1 => '', 'success', 'success', 'advantage', 'success_success', 'success_success', 'success_advantage', 'success_advantage', 'success_advantage', 'advantage_advantage', 'advantage_advantage', 'triumph'),
			'boost' => array(1 => '', '', 'success', 'advantage', 'success_advantage', 'advantage_advantage'),
			'difficulty' => array(1 => '', 'failure', 'threat', 'threat', 'threat', 'failure_failure', 'failure_threat', 'threat_threat'),
			'challenge' => array(1 => '', 'failure', 'failure', 'threat', 'threat', 'failure_failure', 'failure_failure', 'failure_threat', 'failure_threat', 'threat_threat', 'threat_threat', 'dispair'),
			'setback' => array(1 => '', '', 'failure', 'failure', 'threat', 'threat'),
			'force' => array(1 => 'whiteDot', 'whiteDot', 'whiteDot_whiteDot', 'whiteDot_whiteDot', 'whiteDot_whiteDot', 'blackDot', 'blackDot', 'blackDot', 'blackDot', 'blackDot', 'blackDot', 'blackDot_blackDot'),
			);

		public function __construct($type) {
			if (!$this->validDiceType($type)) throw new Exception('Invalid type');
			parent::__construct(sizeof($this->rawTypes[$type]));
		}

		public function __toString() {
			return $result;
		}

		static public function validDiceType($type) {
			if (array_key_exists($type, $this->rawTypes)) return true;
			else return false;
		}

		static public function validDiceTypes() {
			return array_keys($this->rawTypes);
		}
		
		public function roll() {
			$this->result = mt_rand(1, $this->sides);
		}
	}
?>