<?
	class StarWarsFFGDie extends BaseDie {
		private $type;
		private $dice = array(
			'ability' => array(1 => '', 'success', 'success', 'advantage', 'success_success', 'success_advantage', 'advantage_advantage'),
			'proficiency' => array(1 => '', 'success', 'success', 'advantage', 'success_success', 'success_success', 'success_advantage', 'success_advantage', 'success_advantage', 'advantage_advantage', 'advantage_advantage', 'triumph'),
			'boost' => array(1 => '', '', 'success', 'advantage', 'success_advantage', 'advantage_advantage'),
			'difficulty' => array(1 => '', 'failure', 'threat', 'threat', 'threat', 'failure_failure', 'failure_threat', 'threat_threat'),
			'challenge' => array(1 => '', 'failure', 'failure', 'threat', 'threat', 'failure_failure', 'failure_failure', 'failure_threat', 'failure_threat', 'threat_threat', 'threat_threat', 'despair'),
			'setback' => array(1 => '', '', 'failure', 'failure', 'threat', 'threat'),
			'force' => array(1 => 'whiteDot', 'whiteDot', 'whiteDot_whiteDot', 'whiteDot_whiteDot', 'whiteDot_whiteDot', 'blackDot', 'blackDot', 'blackDot', 'blackDot', 'blackDot', 'blackDot', 'blackDot_blackDot'),
			);

		public function __construct($type) {
			if (!array_key_exists($type, $this->dice)) throw new Exception('Invalid type');
			parent::__construct(sizeof($this->dice[$type]));
			$this->type = $type;
		}

		public function __toString() {
			return $result;
		}

		public function roll() {
			$this->result = $this->dice[$this->type][mt_rand(1, $this->sides)];

			return $this->result;
		}
	}
?>
