<?
	class DiceDisplay {
		private $rolls;

		const VIS_HIDE_NONE = 0;
		const VIS_HIDE_ROLL = 1;
		const VIS_HIDE_ROLL_RESULT = 2;
		const VIS_HIDE_ALL = 3;

		function __construct() {}

		function addRoll($rolls) {
			if (is_int($rolls)) {

			} else $this->rolls = $rolls;
		}

		function showHTML() {
			foreach ($this->rolls as $roll) {
				echo '<div class="roll">';
				echo '<p class="rollString">'.$roll['string'].'</p>';
				echo '<p class="rollResults">( ';
				$results = array();
				foreach ($roll['results'] as $key => $result) {
					if (sizeof($result) == 1) $results[$key] = $result[0];
					else {
						$results[$key] = '[ ';
						$results[$key] .= implode(', ', $result);
						$results[$key] .= ' ]';
					}
				}
				echo implode(', ', $results);
				echo ' )';
				if ($roll['modifier'] < 0) echo ' - '.abs($roll['modifier']);
				elseif ($roll['modifier'] > 0) echo ' + '.$roll['modifier'];
				echo ' = '.$roll['total'].'</p>';
			}
		}
	}
?>