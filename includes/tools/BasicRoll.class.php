<?
	class BasicRoll extends Roll {
		private $rerollAces = FALSE;
		private $rolls = array();
		private $dice = array();

		function __construct($diceString, $options) {
			if (isset($options['rerollAces']) && $options['rerollAces']) $this->rerollAces = TRUE;
			preg_match_all('/(\d*)d(\d+)([+-]\d+)?/', $diceString, $rolls, PREG_SET_ORDER);
			foreach ($rolls as $roll) {
				if ($roll[1] == '') {
					$roll[1] = 1;
					$roll[0] = '1'.$roll[0];
				}
				if (!isset($roll[3])) $roll[3] = 0;
				else $roll[3] = intval($roll[3]);

				$this->rolls[] = array('string' => $roll[0], 'number' => $roll[1], 'sides' => $roll[2], 'modifier' => $roll[3], 'results' => array(), 'total' => 0);
				$this->dice[$roll[2]] = new BasicDice($roll[2]);
			}
		}

		function roll() {
			foreach ($this->rolls as $key => &$roll) {
				for ($count = 0; $count < $roll['number']; $count++) {
					$result = $this->dice[$roll['sides']]->roll();
					$roll['results'][$count][] = $result;
					$roll['total'] += $result;
					if ($this->rerollAces && $result == $roll['sides']) $count -= 1;
				}
				$roll['total'] += $roll['modifier'];
			}
		}

		function getResults() {
			return $roll['total'];
		}

		function getData() {
			return $this->rolls;
		}
	}
?>