<?
	abstract class Roll {
		const VIS_HIDE_NONE = 0;
		const VIS_HIDE_ROLL = 1;
		const VIS_HIDE_ROLL_RESULT = 2;
		const VIS_HIDE_ALL = 3;

		protected $rollID = NULL;
		protected $rolls = array();
		protected $dice = array();
		protected $reason = '';
		protected $visibility = self::VIS_HIDE_NONE;
		protected $visText = array(1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]', '[Hidden Reason]');

		abstract function __construct();

		abstract function newRoll($diceString);

		abstract function roll();

		function getRollID() {
			return $this->rollID;
		}

		function setReason($reason) {
			$this->reason = $reason;
		}

		function getReason() {
			return $this->reason;
		}

		function setVisibility($visibility) {
			$this->visibility = $visibility;
		}

		function getVisibility() {
			return $this->visibility;
		}

		abstract function getResults();

		function getData() {
			return $this->rolls;
		}

		abstract function showHTML($showAll = FALSE);
	}
?>