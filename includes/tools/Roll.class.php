<?
	abstract class Roll {
		const VIS_HIDE_NONE = 0;
		const VIS_HIDE_ROLL = 1;
		const VIS_HIDE_ROLL_RESULT = 2;
		const VIS_HIDE_ALL = 3;

		protected $rolls = array();
		protected $dice = array();
		protected $reason = '';
		protected $visibility = self::VIS_HIDE_NONE;
		protected $visText = array(1 => '[Hidden Roll/Result]', '[Hidden Dice &amp; Roll]', '[Everything Hidden]');

		abstract function __construct();

		abstract function newRoll($diceString);

		abstract function roll();

		abstract function getResults();

		function getData() {
			return $this->rolls;
		}

		abstract function showHTML($showAll = FALSE);
	}
?>