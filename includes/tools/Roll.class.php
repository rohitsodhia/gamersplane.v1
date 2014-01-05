<?
	abstract class Roll {
		private $rolls = array();
		private $dice = array();

		const VIS_HIDE_NONE = 0;
		const VIS_HIDE_ROLL = 1;
		const VIS_HIDE_ROLL_RESULT = 2;
		const VIS_HIDE_ALL = 3;

		abstract function __construct($dice);

		abstract function roll();

		abstract function getResults();

		function getData() {
			return $this->rolls;
		}
	}
?>